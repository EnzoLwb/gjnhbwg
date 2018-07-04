<?php

namespace App\Dao;

use App\Models\Exhibit;
use App\Models\ExhibitComment;
use App\Models\ExhibitCommentLikelist;
use App\Models\Exhibition;
use App\Models\ExhibitLike;
use Illuminate\Support\Facades\DB;
/**
 * 展品列表距离排序
 *
 * @author yyj 20171111
 */
class ExhibitDao extends Exhibit
{

	/**
	 * 展品列表距离排序
	 *
	 * @author yyj 20171111
	 * @param int $type
	 * @param int $language
	 * @param int $skip
	 * @param int $take
	 * @param string $auto_num_str
	 * @param int $exhibition_id
	 * @return array
	 */
	public static function exhibit_list($type, $language, $skip, $take, $auto_num_str, $exhibition_id = 0)
	{
		if($language==10){
			$language=1;
		}
		if ($type == 1) {
			//获取所有展品
			$exhibit_list = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', $language)->where('exhibit.is_show_list', 1)->select('exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibit.look_num', 'exhibit.like_num');
			if (!empty($exhibition_id)) {
				$exhibit_list = $exhibit_list->where('exhibit.exhibition_id', $exhibition_id);
			}
			$exhibit_list = $exhibit_list->orderBy('exhibit.look_num', 'desc')->skip($skip)->take($take)->get();
			if (!empty($exhibit_list)) {
				$exhibit_list = $exhibit_list->toArray();
			}
		} else {
			$exhibit_list = [];
			$arr = explode('#', $auto_num_str);
			if ($skip == 0 && !empty(count($arr))) {
				foreach ($arr as $k => $g) {
					$info = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', $language)->where('exhibit.is_show_list', 1)->where('exhibit.auto_num', $g)->where('exhibit.exhibition_id', $exhibition_id)->select('exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibit.look_num', 'exhibit.like_num')->get();
					if (!empty($info)) {
						if (count($exhibit_list)) {
							$exhibit_list = array_merge($exhibit_list, $info->toArray());
						} else {
							$exhibit_list = $info->toArray();
						}
					}
				}
			}
			$info2 = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', $language)->where('exhibit.is_show_list', 1);
			if (!empty($arr)) {
				$info2 = $info2->whereNotIn('auto_num', $arr);
			}
			if (!empty($exhibition_id)) {
				$info2 = $info2->where('exhibit.exhibition_id', $exhibition_id);
			}
			$info2 = $info2->select('exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibit.look_num', 'exhibit.like_num')->orderBy('exhibit.exhibit_num', 'desc')->skip($skip)->take($take)->get();
			if (count($exhibit_list)) {
				$exhibit_list = array_merge($exhibit_list, $info2->toArray());
			} else {
				$exhibit_list = $info2->toArray();
			}
		}
		return $exhibit_list;
	}


	/**
	 * 评论列表
	 *
	 * @author yyj 20171112
	 * @param int $type 1展厅评论2展品评论
	 * @param int $skip
	 * @param int $take
	 * @param int $ex_id 展厅展品编号
	 * @param int $uid
	 * @return array
	 */
	public static function comment_list($type,$skip, $take, $ex_id, $uid)
	{
		$comment_list = ExhibitComment::join('users', 'users.uid', '=', 'exhibit_comment.uid')->where('exhibit_comment.type', $type);
		if(config('app_check')['exhibit_comment']){
			$comment_list=$comment_list->where('exhibit_comment.is_check', 2);
		}
		if ($type == 1) {
			$comment_list = $comment_list->where('exhibit_comment.exhibition_id', $ex_id);
		} else {
			$comment_list = $comment_list->where('exhibit_comment.exhibit_id', $ex_id);
		}
		$comment_list_count=$comment_list->count();
		$comment_list = $comment_list->select('exhibit_comment.comment', 'exhibit_comment.created_at', 'exhibit_comment.like_num', 'users.nickname', 'users.avatar', 'exhibit_comment.id')->skip($skip)->take($take)->orderBy('exhibit_comment.created_at', 'desc')->get();

		$list = [];
		if (!empty($comment_list)) {
			foreach ($comment_list->toArray() as $k => $g) {
				$list[$k]['comment_id'] = $g['id'];
				$list[$k]['comment'] = $g['comment'];
				$list[$k]['datetime'] = date('m-d H:i', strtotime($g['created_at']));
				$list[$k]['like_num'] = $g['like_num'];
				$list[$k]['nickname'] = $g['nickname'];
				$list[$k]['avatar'] = $g['avatar'];
				if ($uid) {
					$list[$k]['is_like'] = ExhibitCommentLikelist::where('uid', $uid)->where('comment_id', $list[$k]['comment_id'])->count();
				} else {
					$list[$k]['is_like'] = 0;
				}
			}
		}
		$data['list']=$list;
		$data['total']= $comment_list_count;
		return $data;
	}

	/**
	 * 蓝牙关联列表
	 *
	 * @author yyj 20171112
	 * @param array $auto_list 蓝牙关联列表
	 * @return array
	 */
	public static function autonum_list($auto_list){

		$data=[];
		//获取展品详情
		$exhibit_list = Exhibit::where('is_show_list', 1)->select('id as exhibit_id','exhibit_name','exhibition_id','auto_num')->orderBy('auto_num','asc')->get();
		//获取展厅列表
		$exhibition=Exhibition::select('exhibition_name','floor_id','id as exhibition_id')->orderBy('order_id', 'desc')->orderBy('id', 'asc')->get();
		$is_add=1;
		if(!empty($exhibition)){
			foreach ($exhibition as $k=>$g) {
				$data[$g->exhibition_id]=[
					'exhibition_name'=>$g->exhibition_name,
					'exhibition_id'=>$g->exhibition_id,
					'exhibit_list'=>[],
					'check_num'=>0,
				];
			}
		}
		foreach ($exhibit_list as $k=>$g){
			$exhibit_info=[
				'exhibiti_name'=>$g->exhibit_name,
				'exhibit_id'=>$g->exhibit_id,
				'is_check'=>in_array($g->exhibit_id,$auto_list)?1:0,
				'exhibit_auto_num'=>$g->auto_num,
			];
			$data[$g->exhibition_id]['exhibit_list'][]=$exhibit_info;
			if($exhibit_info['is_check']==1){
				$data[$g->exhibition_id]['check_num']+=1;
				$is_add=2;
			}
		}
		sort($data);
		$re_data['data']=$data;
		$re_data['is_add']=$is_add;
		return $re_data;
	}


	/**
	 * 通过审核
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 */
	public static function pass_check($type,$ids){
		$idArray = explode(',', $ids);
		if(!is_array($idArray)){
			$idArray[]=$idArray;
		}
		if($type==1){
			ExhibitComment::whereIn('id', $idArray)->where('type',1)->update(['is_check'=>2]);
		}
		elseif ($type==2){
			ExhibitComment::whereIn('id', $idArray)->where('type',2)->update(['is_check'=>2]);
		}
	}

	/**
	 * 不通过审核
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 */
	public static function unpass_check($type,$ids){
		$idArray = explode(',', $ids);
		if(!is_array($idArray)){
			$idArray[]=$idArray;
		}
		if($type==1){
			ExhibitComment::whereIn('id', $idArray)->where('type',1)->update(['is_check'=>3]);
		}
		elseif ($type==2){
			ExhibitComment::whereIn('id', $idArray)->where('type',2)->update(['is_check'=>3]);
		}
	}

	/**
	 * 删除评论
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 */
	public static function del_check($type,$ids){
		$idArray = explode(',', $ids);
		if(!is_array($idArray)){
			$idArray[]=$idArray;
		}
		if($type==1){
			ExhibitComment::whereIn('id', $idArray)->where('type',1)->delete();
		}
		elseif ($type==2){
			ExhibitComment::whereIn('id', $idArray)->where('type',2)->delete();
		}
	}


	/**
	 * 路线列表
	 *
	 * @author yyj 20171112
	 * @param int $language
	 * @param array $road_list 路线列表
	 * @param int $uid
	 * @return array
	 */
	public static function road_list($language,$road_list,$uid){
		if($language==10){
			//$language=1;
			//$language_img='my_';
			$language_img='';
		}
		else{
			$language_img='';
		}
		$data=[];
		//获取展品详情
		$exhibit_list = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit.is_show_map', 1)->where('exhibit_language.language', $language)->select('exhibit.exhibit_img', 'exhibit.id as exhibit_id','exhibit_language.exhibit_name','exhibit.exhibition_id','exhibit.like_num')->get();
		//获取展厅列表
		$exhibition=Exhibition::join('exhibition_language', 'exhibition_language.exhibition_id', '=', 'exhibition.id')->where('exhibition_language.language', $language)->select('exhibition_language.exhibition_name','exhibition.floor_id','exhibition.id as exhibition_id')->get();
		if(!empty($exhibition)){
			foreach ($exhibition as $k=>$g) {
				$data[$g->exhibition_id]=[
					'exhibition_name'=>config('floor')[$g->floor_id].' '.$g->exhibition_name,
					'exhibition_id'=>$g->exhibition_id,
					'is_all_check'=>1,
					'exhibit_list'=>[]
				];
			}
		}
		/*foreach (config('exhibition')[$language] as $k=>$g){
			$data[$k]=[
				'exhibition_name'=>$g,
				'exhibition_id'=>$k,
				'is_all_check'=>1,
				'exhibit_list'=>[]
			];
		}*/
		//获取已点赞的展品
		$like_list=ExhibitLike::where('uid', $uid)->where('type', 1)->pluck('exhibit_id');
		if(count($like_list)==0){
			$like_list=[0];
		}
		else{
			$like_list=$like_list->toArray();
		}
		foreach ($exhibit_list as $k=>$g){
			$imgs=json_decode($g->exhibit_img, true);
			$imgs=isset($imgs[$language_img.'exhibit_list'])?$imgs[$language_img.'exhibit_list']:'';
			$exhibit_info=[
				'exhibiti_name'=>$g->exhibit_name,
				'exhibit_img'=>$imgs,
				'exhibit_id'=>$g->exhibit_id,
				'like_num'=>$g->like_num,
				'is_check'=>in_array($g->exhibit_id,$road_list)?1:0,
				'is_like'=>in_array($g->exhibit_id,$like_list)?1:0,
			];
			$data[$g->exhibition_id]['exhibit_list'][]=$exhibit_info;
			if($exhibit_info['is_check']==0){
				$data[$g->exhibition_id]['is_all_check']=0;
			}
		}
		sort($data);
		return $data;
	}




	/**
	 * 路线列表
	 *
	 * @author yyj 20171112
	 * @param int $language
	 * @param int $road_id
	 * @return array
	 */
	public static function road_list_exhibit($language,$road_id){
		if($language==10){
			//$language=1;
			//$language_img='my_';
			$language_img='';
		}
		else{
			$language_img='';
		}
		$exhibit_arr=VisitRoad::where('id',$road_id)->value('road_list');
		$exhibit_arr=empty($exhibit_arr)?[0]:json_decode($exhibit_arr,true);
		//获取展品详情
		$exhibit_list = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit.is_show_map', 1)->whereIn('exhibit.id',$exhibit_arr)->where('exhibit_language.language', $language)->select('exhibit.exhibit_img', 'exhibit.id as exhibit_id','exhibit_language.exhibit_name','exhibit.exhibition_id','exhibit.like_num')->get();
		//获取展厅列表
		$exhibition=Exhibition::join('exhibition_language', 'exhibition_language.exhibition_id', '=', 'exhibition.id')->where('exhibition_language.language', $language)->select('exhibition_language.exhibition_name','exhibition.floor_id','exhibition.id as exhibition_id')->get();
		if(!empty($exhibition)){
			foreach ($exhibition as $k=>$g) {
				$data[$g->exhibition_id]=[
					'exhibition_name'=>config('floor')[$g->floor_id].' '.$g->exhibition_name,
					'exhibition_id'=>$g->exhibition_id,
					'exhibit_list'=>[]
				];
			}
		}
		/*foreach (config('exhibition')[$language] as $k=>$g){
			$data[$k]=[
				'exhibition_name'=>$g,
				'exhibition_id'=>$k,
				'exhibit_list'=>[]
			];
		}*/

		foreach ($exhibit_list as $k=>$g){
			$imgs=json_decode($g->exhibit_img, true);
			$imgs=isset($imgs[$language_img.'exhibit_list'])?$imgs[$language_img.'exhibit_list']:'';
			$exhibit_info=[
				'exhibiti_name'=>$g->exhibit_name,
				'exhibit_img'=>$imgs,
				'exhibit_id'=>$g->exhibit_id,
				'like_num'=>$g->like_num,
			];
			$data[$g->exhibition_id]['exhibit_list'][]=$exhibit_info;
		}
		foreach ($data as $k=>$g){
			if(count($g['exhibit_list'])==0){
				unset($data[$k]);
			}
		}
		sort($data);
		return $data;
	}



}
