<?php

namespace App\Http\Controllers\Admin\Data;

use App\Dao\ResourceDao;
use App\Dao\ExhibitDao;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Exhibit;
use App\Models\Exhibition;
use App\Models\ExhibitionLanguage;
use App\Models\ExhibitComment;
use App\Models\Learn;
use App\Models\LearnRelation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExhibitionController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 展厅列表
	 *
	 * @author yyj 20171108
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function exhibition_list()
	{
		// 处理排序
		$query = Exhibition::orderBy('order_id', 'asc')->orderBy('id', 'asc');
		// 筛选是名称
		if (request('exhibition_name')) {
			$query->where('exhibition_name', 'LIKE', "%" . request('exhibition_name') . "%");
		}
		// 筛选是否轮播
		if (request('is_lb')) {
			$query->where('is_lb', request('is_lb'));
		}
		// 筛选展览类别
		if (request('type')) {
			$query->where('type', request('type'));
		}
		// 取得列表
		$info = $query->paginate(parent::PERPAGE);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());

		return view('admin.data.exhibition_list', [
			'info' => $info
		]);
	}


	/**
	 * 展厅编辑
	 *
	 * @author yyj 20171027
	 * @param  int $id 展厅id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit($id){
		if (request()->isMethod('post')) {
			//图片验证
			$imgs_arr=ResourceDao::check_upload_imgs(config('exhibit_config.exhibition.imgs'));
			if($imgs_arr['status']){
				$exhibition_img=json_encode($imgs_arr['imgs']);
			}
			else{
				return $this->error($imgs_arr['msg']);
			}
			//轮播限制验证
			if(config('exhibit_config.exhibition.is_lb')&&request('is_lb')==1){
				$lb_num=config('exhibit_config.exhibition.lb_num');
				$num=Exhibition::where('is_lb',1)->count();
				if($num>=$lb_num){
					return $this->error('最多只能设置'.$lb_num.'个轮播展厅');
				}
			}
			$data = [
				'exhibition_img' =>$exhibition_img,
				'exhibition_name' => request('exhibition_name_1'),
				'floor_id' => request('floor_id'),
				'order_id' => request('order_id'),
				'type' => config('exhibit_config.exhibition.is_linzhan')?request('type'):1,
				'is_lb' => config('exhibit_config.exhibition.is_lb')?request('is_lb'):2,
				'is_show_list' => config('exhibit_config.exhibition.is_show')?request('is_show_list'):1,
				'near_exhibition'=>config('exhibit_config.exhibition.is_near_exhibition')?json_encode(request('near_exhibition')):json_encode([])
			];
			//基本信息入库
			if($id=='add'){
				$this->validate(request(), [
					'exhibition_name_1' =>[
						'required',
						Rule::unique('exhibition', 'exhibition_name'),
						'max:20'
					],
				]);
				$new_info=Exhibition::create($data);
				$exhibition_id=$new_info->id;
				$old_info=[];
			}
			else{
				$this->validate(request(), [
					'exhibition_name_1' =>[
						'required',
						Rule::unique('exhibition', 'exhibition_name')->ignore($id, 'id'),
						'max:20'
					],
				]);

				if(config('exhibit_config.is_version_zip')){
					$old_info=$this->get_old_info($id);
				}
				Exhibition::where('id',$id)->update($data);
				$exhibition_id=$id;
				ExhibitionLanguage::where('exhibition_id',$exhibition_id)->delete();
			}
			$new_info=$data;
			//语种信息入库
			foreach (config('language') as $k=>$g){
				//展览名称不为空就写入数据
				if(!empty(request('exhibition_name_'.$k))){
					$data2=[
						'exhibition_id'=>$exhibition_id,
						'exhibition_name'=>request('exhibition_name_'.$k),
						'exhibition_subtitle'=>request('exhibition_subtitle_'.$k),
						'exhibition_address'=>request('exhibition_address_'.$k),
						'content'=>request('content_'.$k),
						'language'=>$k
					];
					ExhibitionLanguage::create($data2);
					$new_info['language'][$k]=$data2;
				}
			}
			if(config('exhibit_config.is_version_zip')){
				ResourceDao::update_exhibition_resource($new_info,$old_info,$exhibition_id);
			}
			return $this->success(get_session_url('exhibition_list'));
		} else {
			$info=$this->get_old_info($id);
			//获取展厅信息
			if(config('exhibit_config.exhibition.is_near_exhibition')){
				$exhibition_list=Exhibition::pluck('exhibition_name','id');
			}
			else{
				$exhibition_list=[];
			}
			return view('admin.data.exhibition_edit', array(
				'info' => $info,
				'id'=>$id,
				'exhibition_list'=>$exhibition_list
			));
		}
	}

	/**
	 * 获取旧的展厅信息
	 *
	 * @author yyj 20180306
	 * @param  int $id 展览id
	 * @return array
	 */
	private function get_old_info($id){
		if($id!='add'){
			$info = Exhibition::where('id', $id)->first()->toArray();
			$info['exhibition_img']=empty(json_decode($info['exhibition_img'],true))?[]:json_decode($info['exhibition_img'],true);
			$language_info=ExhibitionLanguage::where('exhibition_id',$id)->select('exhibition_name','exhibition_subtitle','exhibition_address','content','language')->get()->toArray();
			foreach ($language_info as $k=>$g){
				$info['language'][$g['language']]=$g;
			}
		}
		else{
			$info=[];
		}
		return $info;
	}

	/**
	 * 展览删除
	 *
	 * @author yyj 20171109
	 * @param  int $id 展览id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function delete($id)
	{
		//判断是否被使用
		$is_use=Exhibit::where('exhibition_id',$id)->count();
		if(!empty($is_use)){
			return $this->error('展览正在使用中，请先删除相关展品');
		}
		//删除图片资源
		ResourceDao::del_img('exhibition',$id);
		Exhibition::where('id', $id)->delete();
		ExhibitionLanguage::where('exhibition_id',$id)->delete();
		return $this->success(get_session_url('exhibition_list'));
	}

	/**
	 * 设为轮播
	 *
	 * @author yyj 20171109
	 * @param  int $id 展览id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function set_lb($id)
	{
		$num=Exhibition::where('is_lb',1)->count();
		if($num>=5){
			return $this->error('最多只能设置5个轮播展览');
		}
		Exhibition::where('id', $id)->update(['is_lb'=>1]);
		return $this->success(get_session_url('exhibition_list'));
	}

	/**
	 * 取消轮播
	 *
	 * @author yyj 20171109
	 * @param  int $id 展览id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function unset_lb($id)
	{
		Exhibition::where('id', $id)->update(['is_lb'=>2]);
		return $this->success(get_session_url('exhibition_list'));
	}

	/**
	 * 展厅评论审核
	 *
	 * @author yyj 20171026
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function exhibition_comment_list()
	{

		$is_check=request('is_check',1);
		// 处理排序
		$query = ExhibitComment::orderBy('exhibit_comment.id', 'desc')->join('exhibition','exhibition.id','=','exhibit_comment.exhibition_id')->where('exhibit_comment.type',1);
		// 筛选是评论内容
		if (request('comment')) {
			$query->where('exhibit_comment.comment', 'LIKE', "%" . request('comment') . "%");
		}
		// 筛选是展览名称
		if (request('exhibition_name')) {
			$query->where('exhibition.exhibition_name', 'LIKE', "%" . request('exhibition_name') . "%");
		}
		// 筛选是否审核
		if ($is_check) {
			$query->where('exhibit_comment.is_check', $is_check);
		}
		// 筛选发表时间时间
		if (request('created_at_from')) {
			$query->where('exhibit_comment.created_at', '>=', request('created_at_from'));
		}
		if (request('created_at_to')) {
			$query->where('exhibit_comment.created_at', '<=', date('Y-m-d', strtotime(request('created_at_to') . " +1 day")));
		}
		// 取得列表
		$info = $query->leftJoin('users', 'users.uid', '=', 'exhibit_comment.uid')->select('exhibit_comment.*', 'users.username', 'users.nickname','exhibition.exhibition_name','exhibition.exhibition_img')->paginate(parent::PERPAGE);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());
		return view('admin.data.exhibition_comment_list', [
			'info' => $info,
		]);
	}

	/**
	 * 通过审核
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function pass_check($type,$ids){
		if (request()->ajax()) {
			ExhibitDao::pass_check($type,$ids);
			return $this->success(get_session_url('exhibit_comment_list'));
		}
	}

	/**
	 * 不通过审核
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function unpass_check($type,$ids){
		if (request()->ajax()) {
			ExhibitDao::unpass_check($type,$ids);
			return $this->success(get_session_url('exhibition_comment_list'));
		}
	}

	/**
	 * 删除
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function del_check($type,$ids){
		if (request()->ajax()) {
			ExhibitDao::del_check($type,$ids);
			return $this->success(get_session_url('exhibition_comment_list'));
		}
	}

	/**
	 * 添加学习单
	 *
	 * @author ljy 20180613
	 * @param int $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function add_learn($id){
		$list = Learn::leftJoin('learn_relation','learn.id','learn_relation.learn_id')
			->select('learn.id','learn.title','learn_relation.exhibition_id')
			->OrderBy('learn.id','desc')->paginate(50);
		return view('admin.data.learn_exhibition', [
			'list' => $list,
			'exhibition_id'=>$id
		]);
	}
	public function save_learn($exhibition_id,$learn_ids){
		if (request()->ajax()) {
			$idArray = explode(',', $learn_ids);
			$learn_list = LearnRelation::where('exhibition_id',$exhibition_id)->get();
			$new_list=[];
			foreach($learn_list as $k=>$v){
				$new_list[]=$v['learn_id'];
			}
//			print_r($new_list);
			$list = array_unique(array_merge($idArray,$new_list));


			foreach($idArray as $k=>$v){


				$data=[
					'learn_id'=>$v,
					'exhibition_id'=>$exhibition_id
				];
				LearnRelation::insert($data);
			}
			return $this->success(get_session_url('exhibition_list'));
		}

	}
}
