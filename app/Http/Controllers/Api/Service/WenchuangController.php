<?php

namespace App\Http\Controllers\Api\Service;


use App\Models\WcProduct;
use App\Models\WcXl;
use App\Http\Controllers\Api\Controller;

/**
 * 文创接口
 *
 * @author yyj 20171110
 * @package App\Http\Controllers\Api
 */
class WenchuangController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 文创系列列表
	 *
	 * @author ljy 20180609
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 *
	 * @api {GET} /xl_list 3.服务信息-文创系列列表（lijinyu）
	 * @apiGroup Service
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,d:导览机
	 * @apiSuccess {array} data
	 * @apiSuccess {int} id 系列id
	 * @apiSuccess {string} img 图片url
	 * @apiSuccess {string} title 标题
	 * @apiSuccess {int} product_count 产品数量
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"msg":"","data":[{"id":3,"title":"\u5370\u8c61\u4e03\u8fde\u5c7f","img":"\/uploadfiles\/intro\/20180611\/201806111150574905.png","product_count":1},{"id":2,"title":"\u6e14\u5bb6\u7cfb\u5217","img":"\/uploadfiles\/intro\/20180611\/201806111147097179.png","product_count":0}]}
	 */
	public function xl_list(){
		$xl = WcXl::where('is_show',1)->orderBy('order_no','desc')->select('id','title','img')->get();
		foreach($xl as $k=>$v){
			$xl[$k]['product_count'] = WcProduct::where('is_show',1)->where('xl_id',$v['id'])->count();
		}
		return response_json(1,$xl,'');

	}
	/**
	 * 文创系列详情
	 *
	 * @author ljy 20180609
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 *
	 * @api {GET} /xl_detail 4.服务信息-文创系列详情（导览机）（lijinyu）
	 * @apiGroup Service
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，d:导览机
	 * @apiParam {int} xl_id 系列id
	 * @apiSuccess {array} data
	 * @apiSuccess {int} id 系列id
	 * @apiSuccess {array} data.xl 系列数组
	 * @apiSuccess {int} data.xl.id 系列id
	 * @apiSuccess {string} data.xl.title 系列标题
	 * @apiSuccess {string} data.xl.img 系列背景图
	 * @apiSuccess {string} data.xl.img_1 系列小图标
	 * @apiSuccess {string} data.xl.content 系列简介
	 * @apiSuccess {string} data.xl.content_html 系列简介html地址
	 * @apiSuccess {array} data.product 产品数组
	 * @apiSuccess {int} data.product.id 产品id
	 * @apiSuccess {int} data.product.xl_id 系列id
	 * @apiSuccess {string} data.product.pro_title 产品名称
	 * @apiSuccess {string} data.product.pro_img 产品图片
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"msg":"","data":{"xl":{"id":3,"title":"\u5370\u8c61\u4e03\u8fde\u5c7f","img":"\/uploadfiles\/intro\/20180611\/201806111150574905.png","img_1":"\/uploadfiles\/intro\/20180611\/201806111151001982.png"},"product":[{"id":1,"xl_id":3,"pro_title":"\u4ea7\u54c1\u4e001","pro_img":"\/uploadfiles\/intro\/20180611\/201806111515256226.png"}]}}
	 */
	public function xl_detail(){
		$xl_id = request('xl_id');
		if(request('p')=='d'){
			$xl = WcXl::where('id',$xl_id)->select('id','title','img','img_1','content')->first();
			if($xl){
				$xl->content_html = '/api/xl_content?p='.request('p').'&xl_id='.$xl_id;
				$xl->product = WcProduct::where('is_show',1)->where('xl_id',$xl_id)->select('id','xl_id','pro_title','pro_img')->orderBy('order_no','desc')->get();
			}
			$data['list'] = $xl;
			return response_json(1,$data,'');
		}else{
			return response_json(0,[],'平台参数为d');
		}


	}

	/**
	 * 文创系列详情
	 *
	 * @author ljy 20180609
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 *
	 * @api {GET} /xl_detail_a 4.服务信息-文创系列详情（lijinyu）
	 * @apiGroup Service
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i:IOS a:安卓
	 * @apiSuccess {array} data
	 * @apiSuccess {int} data.id 系列id
	 * @apiSuccess {string} data.title 系列标题
	 * @apiSuccess {string} data.img 系列背景图
	 * @apiSuccess {string} data.img_1 系列小图标
	 * @apiSuccess {string} data.content 系列简介
	 * @apiSuccess {string} data.content_html 系列简介html地址
	 * @apiSuccess {array} data.product 产品数组
	 * @apiSuccess {int} data.product.id 产品id
	 * @apiSuccess {int} data.product.xl_id 系列id
	 * @apiSuccess {string} data.product.pro_title 产品名称
	 * @apiSuccess {string} data.product.pro_img 产品图片
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"msg":"","data":[{"id":4,"title":"\u559c\u4e0a\u7709\u68a2\u7cfb\u5217","img":"\/uploadfiles\/intro\/20180612\/201806121528059817.png","img_1":"\/uploadfiles\/intro\/20180612\/201806121528076739.png","content":"<p>\u559c\u4e0a\u7709\u68a2\u7cfb\u5217\u559c\u4e0a\u7709\u68a2\u7cfb\u5217<\/p>","content_html":"\/api\/xl_content?p=a&xl_id=4","product":[]},{"id":3,"title":"\u5370\u8c61\u4e03\u8fde\u5c7f","img":"\/uploadfiles\/intro\/20180612\/201806121528189393.png","img_1":"\/uploadfiles\/intro\/20180612\/201806121528204717.png","content":"<p>\u5370\u8c61\u4e03\u8fde\u5c7f\u5370\u8c61\u4e03\u8fde\u5c7f\u5370\u8c61\u4e03\u8fde\u5c7f\u5370\u8c61\u4e03\u8fde\u5c7f<br\/><\/p>","content_html":"\/api\/xl_content?p=a&xl_id=3","product":[{"id":1,"xl_id":3,"pro_title":"\u4ea7\u54c1\u4e001","pro_img":"\/uploadfiles\/intro\/20180611\/201806111515256226.png"}]},{"id":2,"title":"\u6e14\u5bb6\u7cfb\u5217","img":"\/uploadfiles\/intro\/20180612\/201806121528266885.png","img_1":"\/uploadfiles\/intro\/20180612\/201806121528298268.png","content":"<p>\u6e14\u5bb6\u7cfb\u5217\u6e14\u5bb6\u7cfb\u5217\u6e14\u5bb6\u7cfb\u5217\u6e14\u5bb6\u7cfb\u5217<\/p>","content_html":"\/api\/xl_content?p=a&xl_id=2","product":[]}]}
	 */
	public function xl_detail_a(){
		$xl_list = WcXl::select('id','title','img','img_1','content')->orderBy('order_no','desc')->get();
		foreach($xl_list as $k=>$v){
			$xl_list[$k]['content_html'] = '/api/xl_content?p='.request('p').'&xl_id='.$v['id'];
			$xl_list[$k]['product'] = WcProduct::where('is_show',1)->where('xl_id',$v['id'])->select('id','xl_id','pro_title','pro_img')->orderBy('order_no','desc')->get();
		}
//		$data['list'] = $xl_list;
		return response_json(1,$xl_list,'');

	}
	/**
	 * 文创产品详情
	 *
	 * @author ljy 20180609
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 *
	 * @api {GET} /product_detail 5.服务信息-文创产品详情（lijinyu）
	 * @apiGroup Service
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,d:导览机
	 * @apiParam {int} xl_id 系列id
	 * @apiParam {int} product_id 产品id
	 * @apiSuccess {array} data
	 * @apiSuccess {int} id 系列id
	 * @apiSuccess {array} data.xl 系列数组
	 * @apiSuccess {int} data.xl.id 系列id
	 * @apiSuccess {string} data.xl.title 系列标题
	 * @apiSuccess {string} data.xl.img 系列背景图
	 * @apiSuccess {string} data.xl.img_1 系列小图标
	 * @apiSuccess {string} data.xl.content 系列简介
	 * @apiSuccess {string} data.xl.content_html 系列简介html地址
	 * @apiSuccess {array} data.product 产品数组
	 * @apiSuccess {int} data.product.id 产品id
	 * @apiSuccess {int} data.product.xl_id 系列id
	 * @apiSuccess {string} data.product.pro_title 产品名称
	 * @apiSuccess {string} data.product.pro_img 产品图片
	 * @apiSuccess {string} data.product.pro_content 产品简介
	 * @apiSuccess {string} data.product.pro_content_html 产品简介
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"msg":"","data":{"xl":{"id":3,"title":"\u5370\u8c61\u4e03\u8fde\u5c7f","img":"\/uploadfiles\/intro\/20180611\/201806111150574905.png","img_1":"\/uploadfiles\/intro\/20180611\/201806111151001982.png"},"product":[{"id":1,"xl_id":3,"pro_title":"\u4ea7\u54c1\u4e001","pro_img":"\/uploadfiles\/intro\/20180611\/201806111515256226.png"}]}}
	 */
	public function product_detail(){
		$xl_id = request('xl_id');
		$product_id = request('product_id');
		$xl = WcXl::where('id',$xl_id)->select('id','title','img','img_1','content')->first();
		if($xl){
			$xl->content_html = '/api/xl_content?p='.request('p').'&xl_id='.$xl_id;
		}

		$product = WcProduct::where('is_show',1)->where('xl_id',$xl_id)->where('id',$product_id)->first();
		if($product){
			$product->pro_content_html = '/api/product_content?p='.request('p').'&product_id='.$product_id;
		}

		$data['xl'] = $xl;
		$data['product'] = $product;
		return response_json(1,$data,'');
	}

	public function xl_content(){
		$xl_id = request('xl_id');
		$p = request('p');
		$content = WcXl::where('xl_id',$xl_id)->value('content');
		if($p=='d'){
			return view('api.service.dlj_wc_xl',[
				'data'=>$content
			]);
		}else{
			return view('api.service.app_wc_xl',[
				'data'=>$content
			]);
		}
	}



	public function product_content(){
		$product_id = request('product_id');
		$p = request('p');
		$content = WcProduct::where('id',$product_id)->value('pro_content');
		if($p=='d'){
			return view('api.service.dlj_wc_product',[
				'data'=>$content
			]);
		}else{
			return view('api.service.app_wc_product',[
				'data'=>$content
			]);
		}
	}

}