<?php

namespace App\Http\Controllers\Api\Service;


use App\Models\Intro;
use App\Http\Controllers\Api\Controller;
use App\Models\IntroLanguage;

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
	 * @apiParam {int} language 1中文，2英语，3韩语，4日语，5法语，6俄语
	 */
	public function intro_html(){
		$p = request('p');
		$data = Intro::leftJoin('intro_language','intro.id','intro_language.intro_id')
			->where('language_id',request('language',1))
			->where('intro.id',1)->first();


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
	 * @apiParam {int} language 1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiSuccess {array} data 数据
	 * @apiSuccess {int} data.title 标题
	 * @apiSuccess {array} data.imgs 图片
	 * @apiSuccess {string} data.intro_html 场馆简介html地址
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"msg":"","data":{"imgs":["\/uploadfiles\/intro\/20180609\/201806091317375003.png","\/uploadfiles\/intro\/20180609\/201806091317411483.png"],"title":"\u4e2d\u56fd\uff08\u6d77\u5357\uff09\u5357\u6d77\u535a\u7269\u9986","intro_html":"api\/intro_html?p=i&language=1"}}
	 */
	public function intro(){
		$data = Intro::leftJoin('intro_language','intro.id','intro_language.intro_id')
			->where('language_id',request('language',1))
			->where('intro.id',1)->select('intro.imgs','intro_language.title')->first();
		$data['imgs'] = explode(',',$data['imgs']);
		$data['intro_html'] = 'api/intro_html?p='.request('p').'&language='.request('language',1);
		return response_json(1, $data);
	}
}