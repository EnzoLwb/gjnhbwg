<?php

namespace App\Http\Controllers\Api\Service;


use App\Models\CgznZl;
use App\Models\CgznJt;
use App\Models\CgznXz;
use App\Http\Controllers\Api\Controller;

/**
 * 参观指南接口
 *
 * @author yyj 20171110
 * @package App\Http\Controllers\Api
 */
class CgznController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 服务信息-参观指南
	 *
	 * @author ljy 20180609
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 *
	 * @api {GET} /cgzn_html 3.服务信息-参观指南网页（lijinyu）
	 * @apiGroup Service
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,d:导览机
	 * @apiParam {int} language 1中文，2英语，3韩语，4日语，5法语，6俄语
	 */
	public function cgzn_html(){
		$p = request('p');
		$xz = CgznXz::where('language_id',request('language',1))->first();
		$jt = CgznJt::where('language_id',request('language',1))->first();
		$zl = CgznZl::where('language_id',request('language',1))->first();

		if(!is_null($xz) && !is_null($jt) && !is_null($zl)){
			if($p=='d'){
				return view('api.service.dlj_cgzn',[
					'xz'=>$xz,
					'jt'=>$jt,
					'zl'=>$zl
				]);
			}else{
				return view('api.service.app_cgzn',[
					'xz'=>$xz,
					'jt'=>$jt,
					'zl'=>$zl
				]);
			}
		}else{
			return response_json(0,[],'信息为空');
		}

	}

}