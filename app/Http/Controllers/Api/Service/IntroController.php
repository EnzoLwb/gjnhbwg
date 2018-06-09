<?php

namespace App\Http\Controllers\Api\Service;


use App\Models\Intro;
use App\Http\Controllers\Api\Controller;

/**
 * 场馆简介接口
 *
 * @author yyj 20171110
 * @package App\Http\Controllers\Api
 */
class IntroController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 服务信息-场馆简介
	 *
	 * @author ljy 20180609
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 *
	 * @api {GET} /intro_html 1.服务信息-场馆简介网页（lijinyu）
	 * @apiGroup Service
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,d:导览机
	 */
	public function intro_html(){
		$p = request('p');
		$data = Intro::orderBy('id','desc')->first();
		$data['d_imgs'] = explode(',',$data['d_imgs']);
		if($p=='d'){
			return view('api.service.dlj_intro',[
				'data'=>$data
			]);
		}else{
			return view('api.service.app_intro',[
				'data'=>$data
			]);
		}
	}
	/**
	 * 服务信息-场馆简介
	 *
	 * @author lxp 20180309
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /intro 2.服务信息-场馆简介（lijinyu）
	 * @apiGroup Service
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓
	 * @apiSuccess {array} data 数据
	 * @apiSuccess {int} data.title 标题
	 * @apiSuccess {array} data.imgs 图片
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"msg":"","data":{"title":"\u4e2d\u56fd\uff08\u6d77\u5357\uff09\u5357\u6d77\u535a\u7269\u9986","imgs":["\/uploadfiles\/intro\/20180609\/201806091041493571.png","\/uploadfiles\/intro\/20180609\/201806091042382457.png"]}}
	 */
	public function intro(){
		$data = Intro::orderBy('id','desc')->select('title','imgs')->first();
		$data['imgs'] = explode(',',$data['imgs']);
		return response_json(1, $data);
	}
}