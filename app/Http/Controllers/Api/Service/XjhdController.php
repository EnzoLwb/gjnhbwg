<?php

namespace App\Http\Controllers\Api\Service;


use App\Models\Xjhd;
use App\Http\Controllers\Api\Controller;

/**
 * 宣教活动接口
 *
 * @author yyj 20171110
 * @package App\Http\Controllers\Api
 */
class XjhdController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 宣教活动列表
	 *
	 * @author ljy 20180609
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 *
	 * @api {GET} /xjhd_list 8.服务信息-宣教活动列表（lijinyu）
	 * @apiGroup Service
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,d:导览机
	 * @apiSuccess {array} data
	 * @apiSuccess {int} id id
	 * @apiSuccess {string} img 图片url
	 * @apiSuccess {string} title 标题
	 * @apiSuccess {string} title_1 副标题
	 * @apiSuccess {string} active_date 活动时间
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"msg":"","data":[{"id":1,"title":"\u5ba3\u6559\u6d3b\u52a81","title_1":"\u7b2c\u4e00\u671f","img":"\/uploadfiles\/intro\/20180611\/201806111559507584.png","active_start_date":"2018-06-12","active_end_date":"2018-06-13","active_date":"2018.06.12-06.13"}]}
	 */
	public function xjhd_list(){
		$data = Xjhd::where('is_show',1)->orderBy('order_no','desc')->select('id','title','title_1','img','active_start_date','active_end_date')->get();

		foreach($data as $k=>$v){
			$data[$k]['active_date'] = date("Y.m.d",strtotime($v['active_start_date'])).'-'.date("m.d",strtotime($v['active_end_date']));
		}

		return response_json(1,$data,'');

	}
	/**
	 * 宣教活动详情
	 *
	 * @author ljy 20180609
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 *
	 * @api {GET} /xjhd_detail 9.服务信息-宣教活动详情（lijinyu）
	 * @apiGroup Service
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,d:导览机
	 * @apiParam {int} id 活动id
	 * @apiSuccess {array} data
	 * @apiSuccess {int} id id
	 * @apiSuccess {string} img 图片url
	 * @apiSuccess {string} title 标题
	 * @apiSuccess {string} title_1 副标题
	 * @apiSuccess {string} active_date 活动时间
	 * @apiSuccess {string} active_place 活动地点
	 * @apiSuccess {string} active_price 活动价格
	 * @apiSuccess {string} active_time 活动时长
	 * @apiSuccess {string} active_content_html 活动时长
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"msg":"","data":{"id":1,"title":"\u5ba3\u6559\u6d3b\u52a81","img":"\/uploadfiles\/intro\/20180611\/201806111559507584.png","active_place":"\u5ba3\u6559\u6d3b\u52a8","active_start_date":"2018-06-12","active_end_date":"2018-06-13","active_price":"\u514d\u8d39","active_time":"30","content":"<p>\u5ba3\u6559\u6d3b\u52a8\u5ba3\u6559\u6d3b\u52a8\u5ba3\u6559\u6d3b\u52a8\u5ba3\u6559\u6d3b\u52a8\u5ba3\u6559\u6d3b\u52a8<\/p>","is_show":1,"order_no":255,"title_1":"\u7b2c\u4e00\u671f","active_date":"2018.06.12-06.13","active_content_html":"\/api\/xjhd_content?p=i&id=1"}}
	 */
	public function xjhd_detail(){
		$id = request('id');
		$data = Xjhd::where('id',$id)->first();
		$data->active_date = date("Y.m.d",strtotime($data->active_start_date)).'-'.date("m.d",strtotime($data->active_end_date));
		$data->active_content_html = '/api/xjhd_content?p='.request('p').'&id='.$id;
		return response_json(1,$data,'');
	}
	/**
	 * 宣教活动简介页面
	 *
	 * @author ljy 20180609
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 *
	 * @api {GET} /xjhd_content 10.服务信息-宣教活动简介页面（lijinyu）
	 * @apiGroup Service
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,d:导览机
	 * @apiParam {int} id 活动id
	 */

	public function xjhd_content(){
		$id = request('id');
		$p = request('p');
		$content = Xjhd::where('id',$id)->value('content');
		if($p=='d'){
			return view('api.service.dlj_xjhd_content',[
				'data'=>$content
			]);
		}else{
			return view('api.service.app_xjhd_content',[
				'data'=>$content
			]);
		}
	}


}