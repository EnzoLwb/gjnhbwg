<?php

namespace App\Http\Controllers\Api;

use App\Dao\ExhibitDao;
use App\Models\ExhibitComment;
use App\Models\Exhibition;
use App\Models\Exhibit;
use App\Models\ExhibitLanguage;
use App\Models\ExhibitLike;
use App\Models\ExUserVisit;
use App\Models\ExUserVisitfoot;
use App\Models\ExhibitCommentLikelist;
use App\Models\VisitRoad;
use App\Models\LearnRelation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 展品导览相关接口
 *
 * @author yyj 20171110
 * @package App\Http\Controllers\Api
 */
class ExhibitController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 获取所有展厅接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /exhibition_list 100.获取所有展厅接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {array} temporary 临时展览
	 * @apiSuccess {array} theme 主题展览
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {string} exhibition_address 展厅地址
	 * @apiSuccess {string} exhibition_img 展厅图片
	 * @apiSuccess {int} exhibition_id 展览id
	 * @apiSuccess {string} remark 摘要
	 * @apiSuccess {string} learn_url 学习单链接
	 */
	public function exhibition_list()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$data = [];
		//获取临时展览
		$data['temporary'] = Exhibition::join('exhibition_language', 'exhibition_language.exhibition_id', '=', 'exhibition.id')->where('exhibition.type', 2)->where('exhibition.is_show_list', 1)->where('exhibition_language.language', $language)->select('exhibition_language.exhibition_name', 'exhibition_language.exhibition_address', 'exhibition.exhibition_img', 'exhibition.id as exhibition_id')->get()->toarray();
		foreach ($data['temporary'] as $k => $g) {
			$img_arr = json_decode($g['exhibition_img'], true);
			$data['temporary'][$k]['exhibition_img'] = $img_arr['list_img'];
		}
		//获取主题展览
		$data['theme'] = Exhibition::join('exhibition_language', 'exhibition_language.exhibition_id', '=', 'exhibition.id')->where('exhibition.type', 1)->where('exhibition_language.language', $language)->where('exhibition.is_show_list', 1)->select('exhibition_language.exhibition_name', 'exhibition_language.exhibition_address', 'exhibition_language.content', 'exhibition.exhibition_img', 'exhibition.id as exhibition_id')->get()->toarray();
		foreach ($data['theme'] as $k => $g) {
			$img_arr = json_decode($g['exhibition_img'], true);
			$data['theme'][$k]['exhibition_img'] = $img_arr['list_img'];
			$data['theme'][$k]['remark'] = str_limit(cutstr_html($data['theme'][$k]['content']), $limit = 100, $end = '...');
			unset($data['theme'][$k]['content']);

			$data['theme'][$k]['learn_url'] = "/api/learn_content_info?type_id=1&p=" . request('p') . "&rela_id=" . $g['exhibition_id'] . "&api_token=";
		}
		return response_json(1, $data);
	}

	/**
	 * 展厅详情接口
	 *
	 * @author yyj 20171110
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /exhibition_info 101.展厅详情接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} exhibition_id 展厅编号
	 * @apiParam {int} language 语种，1中文，2英语，10蒙语
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {array} exhibition_info 展厅详情
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {string} exhibition_imgs 展厅图片
	 * @apiSuccess {int} floor  所在楼层
	 * @apiSuccess {string} content_url 内容h5
	 * @apiSuccess {string} learn_url 学习单url
	 */
	public function exhibition_info()
	{
		$language = request('language', 1);
		if ($language == 10) {
			//$language=1;
			//$language_img='my_';
			$language_img = '';
		} else {
			$language_img = '';
		}
		$type = request('type', 1);
		$skip = request('skip', 0);
		$take = request('take', 10);
		$auto_num_str = request('auto_num_str', 0);
		$exhibition_id = request('exhibition_id', 0);
		$data = [];
		$p = request('p', 'a');
		//获取展览简介
		$exhibition = Exhibition::join('exhibition_language', 'exhibition_language.exhibition_id', '=', 'exhibition.id')->where('exhibition_language.language', $language)->where('exhibition.id', $exhibition_id)->select('exhibition_language.exhibition_name', 'exhibition.' . $language_img . 'exhibition_img as exhibition_img', 'exhibition.id as exhibition_id', 'exhibition.floor_id as floor')->first();
		if (!empty($exhibition)) {

			$imgs = json_decode($exhibition['exhibition_img'], true);
			$imgs = isset($imgs['exhibition_imgs']) ? $imgs['exhibition_imgs'] : '';

			$data['exhibition_info'] = $exhibition;
			//$data['exhibition_info']['exhibition_share_url'] = '/api/exhibition_share_info/' . $language . '/' . $exhibition_id . '?p=' . $p.'&language='.$language;
			$data['exhibition_info']['exhibition_imgs'] = $imgs;
			unset($data['exhibition_info']['exhibition_img']);
			$data['exhibition_info']['content_url'] = '/api/exhibition_content_info/' . $language . '/' . $exhibition_id . '?p=' . $p . '&language=' . $language;
			$data['exhibition_info']['learn_url'] = "/api/learn_content_info?type_id=1&p=" . request('p') . "&rela_id=" . $exhibition_id . "&api_token=";
		} else {
			$data['exhibition_info'] = [];
		}
		/*$exhibit_list=ExhibitDao::exhibit_list($type, $language, $skip, $take,$auto_num_str,$exhibition_id);
		$data['exhibit_list'] = [];
		$uid=0;
		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
		}
		foreach ($exhibit_list as $k => $g) {
			$imgs=json_decode($g['exhibit_img'], true);
			$imgs=isset($imgs[$language_img.'exhibit_list'])?$imgs[$language_img.'exhibit_list']:'';
			$data['exhibit_list'][$k]['exhibit_list_img'] = $imgs;
			$data['exhibit_list'][$k]['exhibit_id'] = $g['exhibit_id'];
			$data['exhibit_list'][$k]['exhibit_name'] = $g['exhibit_name'];
			$data['exhibit_list'][$k]['look_num'] = $g['look_num'];
			$data['exhibit_list'][$k]['like_num'] = $g['like_num'];
			if($uid){
				$data['exhibit_list'][$k]['is_like'] = ExhibitLike::where('uid', $uid)->where('exhibit_id', $g['exhibit_id'])->where('type', 1)->count();
			}
			else{
				$data['exhibit_list'][$k]['is_like'] = 0;
			}
		}
		$data['comment_list']=ExhibitDao::comment_list(1,$language,0,5,$exhibition_id,$uid);*/
		return response_json(1, $data);
	}

	/**
	 * 展厅详情页
	 *
	 * @author yyj 20171111
	 * @param  int $language 语种1中文2英文10蒙文
	 * @param  int $exhibition_id 展厅编号
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function exhibition_content_info($language, $exhibition_id)
	{
		$exhibition = Exhibition::join('exhibition_language', 'exhibition_language.exhibition_id', '=', 'exhibition.id')->where('exhibition_language.language', $language)->where('exhibition.id', $exhibition_id)->select('exhibition_language.exhibition_name', 'exhibition_language.exhibition_subtitle', 'exhibition_language.exhibition_address', 'exhibition_language.content', 'exhibition.exhibition_img as exhibition_img', 'exhibition.id as exhibition_id')->first();
		return view('api.exhibit.exhibition_content_info', array(
			'info' => $exhibition,
			'language' => $language,
		));
	}

	/**
	 * 展品列表接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /exhibit_list 102.展品列表接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiParam {int} exhibition_id 展厅编号
	 * @apiParam {int} skip 数据偏移量默认0
	 * @apiParam {int} take 查询数量默认10
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} exhibit_list_img 展品图片
	 * @apiSuccess {string} audio 音频
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {int} look_num 浏览数量
	 * @apiSuccess {int} like_num 点赞数量
	 */
	public function exhibit_list()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'exhibition_id' => 'required|min:0|integer',
			'skip' => 'required|min:0|integer',
			'take' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$skip = request('skip', 0);
		$take = request('take', 10);
		$exhibition_id = request('exhibition_id', 0);
		$data = [];
		$exhibit_list = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', $language)->where('exhibit.is_show_list', 1)->select('exhibit_language.exhibit_name', 'exhibit_language.audio', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibit.look_num', 'exhibit.like_num')->where('exhibit.exhibition_id', $exhibition_id)->orderBy('exhibit.order_id', 'asc')->orderBy('exhibit.id', 'asc')->skip($skip)->take($take)->get()->toArray();
		foreach ($exhibit_list as $k => $g) {
			$imgs = json_decode($g['exhibit_img'], true);
			$imgs = isset($imgs['exhibit_list']) ? $imgs['exhibit_list'] : '';
			$data[$k]['exhibit_list_img'] = $imgs;
			$data[$k]['exhibit_id'] = $g['exhibit_id'];
			$data[$k]['exhibit_name'] = $g['exhibit_name'];
			$data[$k]['audio'] = $g['audio'];
			$data[$k]['look_num'] = $g['look_num'];
			$data[$k]['like_num'] = $g['like_num'];
		}
		return response_json(1, $data);
	}

	/**
	 * 展品详情接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /exhibit_info 103.展品详情接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiParam {int} exhibit_id 展品编号
	 * @apiParam {string} [api_token] token(登录后上传)
	 * @apiParam {int} [road_id] 线路id，默认为0，不传默认0按所在展厅搜索
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {array} exhibit_imgs 展品图片
	 * @apiSuccess {array} exhibit_list 展品图片(导览机，导览机，列表圆图，足迹小图)
	 * @apiSuccess {string} exhibit_icon1 地图页图片(亮)
	 * @apiSuccess {string} exhibit_icon2 地图页图片(暗)
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {string} audio 音频地址
	 * @apiSuccess {string} content_url 内容详细url
	 * @apiSuccess {string} share_url 分享url
	 * @apiSuccess {int} is_like 是否点赞1已点赞0未点赞
	 * @apiSuccess {int} is_collection 是否收藏1已收藏0未收藏
	 * @apiSuccess {int} map_id 地图编号
	 * @apiSuccess {int} x x轴坐标
	 * @apiSuccess {int} y y轴坐标
	 * @apiSuccess {string} exhibition_name 展览名
	 * @apiSuccess {string} floor 所在楼层
	 * @apiSuccess {string} reorder 上一页下一页所需排序
	 * @apiSuccess {int} is_have_wenda 是否有问题，0没有   大于0表示有
	 * @apiSuccess {string} learn_url 展品问答url
	 */
	public function exhibit_info()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'exhibit_id' => 'required|min:0|integer',
		]);
		$p = request('p', 'a');
		$language = request('language', 1);
		$exhibit_id = request('exhibit_id', 0);
		$exhibit_info = Exhibit::join('exhibit_language', function ($join) use ($language) {
			$join->on('exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', '=', $language);
		})->join('exhibition', 'exhibition.id', '=', 'exhibit.exhibition_id')->join('exhibition_language', function ($join) use ($language) {
			$join->on('exhibition.id', '=', 'exhibition_language.exhibition_id')->where('exhibition_language.language', '=', $language);
		})->where('exhibit.id', $exhibit_id)->select('exhibit_language.exhibit_name', 'exhibit_language.content as exhibit_content', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibit_language.audio', 'exhibit.map_id', 'exhibit.x', 'exhibit.y', 'exhibition_language.exhibition_name', 'exhibition.floor_id', 'exhibit.exhibition_id')->first();
		$data = [];
		if (!empty($exhibit_info)) {
			$data['exhibit_id'] = $exhibit_info->exhibit_id;
			$data['exhibit_name'] = $exhibit_info->exhibit_name;
			$data['exhibit_content'] = $exhibit_info->exhibit_content;
			$data['exhibit_imgs'] = json_decode($exhibit_info->exhibit_img, true)['exhibit_imgs'];
			$data['exhibit_list'] = json_decode($exhibit_info->exhibit_img, true)['exhibit_list'];
			$data['exhibit_icon1'] = json_decode($exhibit_info->exhibit_img, true)['exhibit_icon1'];
			$data['exhibit_icon2'] = json_decode($exhibit_info->exhibit_img, true)['exhibit_icon2'];
			$data['audio'] = $exhibit_info->audio;
			$data['map_id'] = $exhibit_info->map_id;
			$data['x'] = $exhibit_info->x;
			$data['y'] = $exhibit_info->y;
			$data['content_url'] = '/api/exhibit_content_info/' . $language . '/' . $exhibit_id . '?p=' . $p . '&language=' . $language;
			//$data['knowledge_url'] = '/api/exhibit_knowledge_info/' . $language . '/' . $exhibit_id . '?p=' . $p.'&language='.$language;
			$data['share_url'] = '/api/exhibit_share_info/' . $language . '/' . $exhibit_id . '?p=' . $p . '&language=' . $language;
			$data['exhibition_name'] = $exhibit_info->exhibition_name;
			$data['floor'] = config('floor')[$exhibit_info->floor_id];
			$user = Auth::user();
			if (false == empty($user)) {
				$uid = $user->uid;
				$data['is_like'] = ExhibitLike::where('uid', $uid)->where('exhibit_id', $data['exhibit_id'])->where('type', 1)->count();
				$data['is_collection'] = ExhibitLike::where('uid', $uid)->where('exhibit_id', $data['exhibit_id'])->where('type', 2)->count();
			} else {
				$data['is_like'] = 0;
				$data['is_collection'] = 0;
			}

			$road_id = intval(request('road_id', 0));
			if ($road_id == 0) {
				$reorder = Exhibit::where('exhibition_id', $exhibit_info['exhibition_id'])->where('is_show_list', 1)->select('id')->orderBy('exhibit.order_id', 'asc')->orderBy('exhibit.id', 'asc')->get()->toArray();
				$reorder_array = array();
				if ($reorder) {
					foreach ($reorder as $k => $v) {
						$reorder_array[] = $v['id'];
					}
				}
				$data['reorder'] = $reorder_array;
			} else {
				$visitroad = VisitRoad::where('id', $road_id)->first();
				if (!$visitroad) {
					return response_json(0, '', 'error road_id');
				} else {
					$weight_exhibit_ids = json_decode($visitroad['weight_exhibit_ids'], true);
					if (!in_array($exhibit_id, $weight_exhibit_ids)) {
						return response_json(0, '', 'error road_id,no exhibit_id');
					} else {
						$data['reorder'] = $weight_exhibit_ids;
					}
				}

			}

			$is_have_wenda = LearnRelation::where('type_id', 2)->where('rela_id', $exhibit_id)->count();
			$data['is_have_wenda'] = $is_have_wenda ? 1 : 0;

			$data['learn_url'] = '/api/learn_content_info?type_id=2&p=' . request('p') . '&rela_id=' . $exhibit_id;
			return response_json(1, $data);
		} else {
			return response_json(0, '', 'error exhibit_id');
		}
	}

	/**
	 * 展品详情页
	 *
	 * @author yyj 20180321
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @param  int $exhibit_id 展品编号
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function exhibit_content_info($language, $exhibit_id)
	{
		$info = ExhibitLanguage::where('language', $language)->where('exhibit_id', $exhibit_id)->select('content', 'exhibit_name')->first();
		return view('api.exhibit.exhibit_content_info', array(
			'info' => $info,
			'language' => $language,
		));
	}

	/**
	 * 展品科普知识页
	 *
	 * @author yyj 20180321
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @param  int $exhibit_id 展品编号
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function exhibit_knowledge_info($language, $exhibit_id)
	{
		$info = ExhibitLanguage::where('language', $language)->where('exhibit_id', $exhibit_id)->select('knowledge', 'exhibit_name')->first();
		return view('api.exhibit.exhibit_knowledge_info', array(
			'info' => $info,
			'language' => $language,
		));
	}

	/**
	 * 展品分享页
	 *
	 * @author yyj 20180321
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @param  int $exhibit_id 展品编号
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function exhibit_share_info($language, $exhibit_id)
	{
		$info = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', $language)->where('exhibit.id', $exhibit_id)->select('exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit_language.knowledge', 'exhibit_language.content', 'exhibit_language.audio')->first();
		return view('api.exhibit.exhibit_share_info', array(
			'info' => $info,
			'language' => $language,
		));
	}

	/**
	 * 展品点赞收藏操作接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /do_like 104.展品点赞收藏添加取消操作接口（系统自动判断执行添加还是取消）
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} exhibit_id 展品编号
	 * @apiParam {int} type 类别1点赞2收藏
	 * @apiParam {string} api_token token
	 * @apiSuccess {array} data 数组
	 * @apiSuccess {int} data.result 操作结果1成功0失败
	 * @apiSuccess {int} data.is_like 操作完后，状态 1已点赞（收藏） 0未点赞（收藏）
	 */
	public function do_like()
	{
		$this->validate([
			'exhibit_id' => 'required|min:0|integer',
			'type' => 'required|min:0|integer',
		]);
		$exhibit_id = request('exhibit_id');
		$type = request('type');
		$uid = Auth::user()->uid;
		$is_set = ExhibitLike::where('uid', $uid)->where('exhibit_id', $exhibit_id)->where('type', $type)->first();
		if (empty($is_set)) {
			$r = ExhibitLike::create([
				'uid' => $uid,
				'exhibit_id' => $exhibit_id,
				'type' => $type
			]);
			if ($type == 1) {
				Exhibit::where('id', $exhibit_id)->increment('like_num');
			} elseif ($type == 2) {
				Exhibit::where('id', $exhibit_id)->increment('collection_num');
			}
			$data['is_like'] = 1;
		} else {
			$r = ExhibitLike::where('uid', $uid)->where('exhibit_id', $exhibit_id)->where('type', $type)->delete();
			if ($type == 1) {
				Exhibit::where('id', $exhibit_id)->decrement('like_num');
			} elseif ($type == 2) {
				Exhibit::where('id', $exhibit_id)->decrement('collection_num');
			}
			$data['is_like'] = 0;
		}
		if ($r) {
			$data['result'] = 1;
			return response_json(1, $data);
		} else {
			$data['result'] = 0;
			return response_json(1, $data);
		}
	}

	/**
	 * 展厅/展品评论接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {POST} /exhibit_comment 105.展厅/展品评论接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} exhibition_id 展厅编号，展品评论时传0
	 * @apiParam {int} exhibit_id 展品编号，展厅评论时传0
	 * @apiParam {int} type 类别1展厅评论2展品评论
	 * @apiParam {string} api_token token
	 * @apiParam {string} comment 评论内容
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function exhibit_comment()
	{
		$this->validate([
			'exhibit_id' => 'required|min:0|integer',
			'exhibition_id' => 'required|min:0|integer',
			'type' => 'required|min:0|integer',
			'comment' => 'required|string|max:500',
		]);
		$exhibit_id = request('exhibit_id', 0);
		$exhibition_id = request('exhibition_id', 0);
		$type = request('type');
		$uid = Auth::user()->uid;
		$comment = request('comment', '');
		$r = ExhibitComment::create([
			'exhibit_id' => $exhibit_id,
			'exhibition_id' => $exhibition_id,
			'type' => $type,
			'uid' => $uid,
			'comment' => $comment,
			'is_check' => 1,
			'like_num' => 0,
		]);
		if ($r) {
			if ($type == 2) {
				Exhibit::where('id', $exhibit_id)->increment('comment_num');
			}
			return response_json(1, 1);
		} else {
			return response_json(1, 0);
		}
	}

	/**
	 * 展厅/展品评论列表
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /comment_list 106.展厅/展品评论列表
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} ex_id 展厅编号或展品编号
	 * @apiParam {int} type 类别1展厅评论2展品评论
	 * @apiParam {int} skip 数据偏移量默认0
	 * @apiParam {int} take 查询数量默认10
	 * @apiParam {string} [api_token] token
	 * @apiSuccess {array} data.list 列表信息
	 * @apiSuccess {int} data.list.comment_id 评论id
	 * @apiSuccess {int} data.list.like_num点赞数量
	 * @apiSuccess {string} data.list.datetime 评论时间
	 * @apiSuccess {string} data.list.nickname 昵称
	 * @apiSuccess {string} data.list.avatar 头像
	 * @apiSuccess {string} data.list.is_like 是否点赞
	 * @apiSuccess {int} data.total 评论总数
	 */
	public function comment_list()
	{
		$this->validate([
			'ex_id' => 'required|min:0|integer',
			'type' => 'required|min:0|integer',
			'skip' => 'required|min:0|integer',
			'take' => 'required|min:0|integer',
		]);
		$type = request('type', 1);
		$ex_id = request('ex_id', 1);
		$skip = request('skip', 0);
		$take = request('take', 10);
		$uid = 0;
		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
		}
		$data = ExhibitDao::comment_list($type, $skip, $take, $ex_id, $uid);
		return response_json(1, $data);
	}

	/**
	 * 评论点赞取消接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /comment_do_like 107.评论点赞取消接口（系统自动判断执行添加还是取消）
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} comment_id 评论编号
	 * @apiParam {string} api_token token
	 * @apiSuccess {array} data 数组
	 * @apiSuccess {int} data.result 操作结果1成功0失败
	 * @apiSuccess {int} data.is_like 操作完后，状态 1已点赞（收藏） 0未点赞（收藏）
	 */
	public function comment_do_like()
	{
		$this->validate([
			'comment_id' => 'required|min:0|integer',
		]);
		$comment_id = request('comment_id');
		$uid = Auth::user()->uid;
		$is_set = ExhibitCommentLikelist::where('uid', $uid)->where('comment_id', $comment_id)->first();
		if (empty($is_set)) {
			$r = ExhibitCommentLikelist::create([
				'uid' => $uid,
				'comment_id' => $comment_id
			]);
			ExhibitComment::where('id', $comment_id)->increment('like_num');
			$data['is_like'] = 1;
		} else {
			$r = ExhibitCommentLikelist::where('uid', $uid)->where('comment_id', $comment_id)->delete();
			ExhibitComment::where('id', $comment_id)->decrement('like_num');
			$data['is_like'] = 0;
		}
		if ($r) {
			$data['result'] = 1;
			return response_json(1, $data);
		} else {
			$data['result'] = 0;
			return response_json(1, $data);
		}
	}

	/**
	 * 展品浏览收听接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /visit_exhibit 108.展品浏览收听接口（浏览展品和播放语音时调用）
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} exhibit_id 展品编号
	 * @apiParam {string} [api_token] token
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function visit_exhibit()
	{
		$this->validate([
			'exhibit_id' => 'required|min:0|integer',
		]);
		$exhibit_id = request('exhibit_id', 1);
		$uid = 0;
		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
		}
		//展品浏览量+1
		Exhibit::where('id', $exhibit_id)->increment('look_num');
		if ($uid) {
			ExhibitLike::create([
				'uid' => $uid,
				'exhibit_id' => $exhibit_id,
				'type' => 3
			]);
		}
		//展品收听量+1
		$r = Exhibit::where('id', $exhibit_id)->increment('listen_num');
		if ($uid) {
			$u_ex_info = ExUserVisit::where('uid', $uid)->first();
			if (empty($u_ex_info)) {
				ExUserVisit::create([
					'uid' => $uid,
					'use_time' => 0,
					'listen_num' => 1
				]);
			} else {
				ExUserVisit::where('uid', $uid)->increment('listen_num');
			}

			//foot start
			$u_ex_finfo = ExUserVisitfoot::where('uid', $uid)->where('exhibit_id', $exhibit_id)->first();
			$n_time = date('Y-m-d H:i:s', time());
			if (empty($u_ex_finfo)) {
				ExUserVisitfoot::create([
					'uid' => $uid,
					'exhibit_id' => $exhibit_id
				]);
			} else {
				ExUserVisitfoot::where('uid', $uid)->where('exhibit_id', $exhibit_id)->update(['updated_at' => $n_time]);
			}
			//foot end
		}
		if ($r) {
			return response_json(1, 1);
		} else {
			return response_json(1, 0);
		}
	}

	/**
	 * 展品搜索接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /exhibit_search 109.展品搜索接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {string} keyword 展品名或编号
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} exhibit_id 展品编号
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} audio 音频
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {string} floor 所在楼层
	 */
	public function exhibit_search()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'keyword' => 'required|string|max:255',
		]);
		$language = request('language', 1);
		$keyword = request('keyword', 1);
		$exhibit_list = Exhibit::join('exhibit_language', function ($join) use ($language) {
			$join->on('exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', '=', $language);
		})->join('exhibition', 'exhibition.id', '=', 'exhibit.exhibition_id')->join('exhibition_language', function ($join) use ($language) {
			$join->on('exhibition.id', '=', 'exhibition_language.exhibition_id')->where('exhibition_language.language', '=', $language);
		})->where('exhibit.is_show_list', 1)->where(function ($query) use ($keyword) {
			$query->where('exhibit.exhibit_num', 'like', '%' . $keyword . '%')->orwhere('exhibit_language.exhibit_name', 'like', '%' . $keyword . '%');
		})->select('exhibit_language.exhibit_name', 'exhibit_language.audio', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibition_language.exhibition_name', 'exhibition.floor_id')->orderBy('exhibit.look_num', 'desc')->get();
		$data = [];
		foreach ($exhibit_list as $k => $g) {
			$imgs = json_decode($g->exhibit_img, true);
			$imgs = isset($imgs['exhibit_list']) ? $imgs['exhibit_list'] : '';
			$data[$k]['exhibit_name'] = $g->exhibit_name;
			$data[$k]['exhibit_id'] = $g->exhibit_id;
			$data[$k]['exhibit_list_img'] = $imgs;
			$data[$k]['audio'] = $g->audio;
			$data[$k]['exhibition_name'] = $g->exhibition_name;
			$data[$k]['floor'] = config('floor')[$g->floor_id];
		}
		return response_json(1, $data);
	}

	/**
	 * 热门展品接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /exhibit_hot 110.热门展品接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiParam {int} skip 数据偏移量默认0
	 * @apiParam {int} take 查询数量默认10
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} exhibit_list_img 展品图片
	 * @apiSuccess {string} audio 音频
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {int} look_num 浏览数量
	 * @apiSuccess {int} like_num 点赞数量
	 */
	public function exhibit_hot()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'skip' => 'required|min:0|integer',
			'take' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$skip = request('skip', 0);
		$take = request('take', 10);
		$exhibition_id = request('exhibition_id', 0);
		$data = [];
		$exhibit_list = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', $language)->where('exhibit.is_show_list', 1)->select('exhibit_language.exhibit_name', 'exhibit_language.audio', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibit.look_num', 'exhibit.like_num')->orderBy('exhibit.look_num', 'desc')->orderBy('exhibit.like_num', 'desc')->orderBy('exhibit.order_id', 'asc')->skip($skip)->take($take)->get()->toArray();
		foreach ($exhibit_list as $k => $g) {
			$imgs = json_decode($g['exhibit_img'], true);
			$imgs = isset($imgs['exhibit_list']) ? $imgs['exhibit_list'] : '';
			$data[$k]['exhibit_list_img'] = $imgs;
			$data[$k]['exhibit_id'] = $g['exhibit_id'];
			$data[$k]['exhibit_name'] = $g['exhibit_name'];
			$data[$k]['audio'] = $g['audio'];
			$data[$k]['look_num'] = $g['look_num'];
			$data[$k]['like_num'] = $g['like_num'];
		}
		return response_json(1, $data);
	}

}