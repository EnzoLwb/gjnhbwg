<?php

namespace App\Http\Controllers\Api;


use App\Models\ServicePoint;

/**
 * 展品导览相关接口
 *
 * @author yyj 20171110
 * @package App\Http\Controllers\Api
 */
class ServicePointController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 获取地图页服务设施点位
	 *
	 * @author yyj 20171112
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /map_service_point 401.获取地图页服务设施点位
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} map_id 地图编号传
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} img 设施图片
	 * @apiSuccess {int} map_id 地图编号
	 * @apiSuccess {int} x x轴坐标
	 * @apiSuccess {int} y y轴坐标
	 *
	 */
	public function map_service_point(){
		$map_id = request('map_id');
		$data=ServicePoint::where('map_id',$map_id)->select('map_id','x','y','img')->get();
		return response_json(1, $data);
	}


	/**
	 * 获取服务设施类型列表
	 *
	 * @author yyj 20171112
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /service_point_list 402.获取服务设施类型列表
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信 导览机d
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} tpye_id type_id
	 * @apiSuccess {string} name 名称
	 * @apiSuccess {string} img 图片地址
	 *
	 */
	public function service_point_list(){
		$stype= config('stype');
		$data = array();
		foreach ($stype as $k=>$v) {
			$temp = array();
			$temp['tpye_id'] = $k;
			$temp['name'] = $v;
			$temp['img']="/img/service/".$k.".png";
			$data[] = $temp;
		}
		return response_json(1, $data);
	}


	/**
	 * 服务设施查询
	 *
	 * @author yyj 20171112
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /service_point_search 403.服务设施查询
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信 导览机d
	 * @apiParam {int} type_id 类型id
	 * @apiParam {int} map_id 地图id（楼层）
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} tpye_id type_id
	 * @apiSuccess {string} name 名称
	 * @apiSuccess {string} img 图片地址
	 *
	 */
	public function service_point_search(){

		$this->validate([
			'type_id' => 'required|min:0|integer',
			'map_id' => 'required|in:1,2,3'
		]);

		$type_id = request('type_id');
		$map_id = request('map_id');

		$data['now'] = ServicePoint::where('map_id',$map_id)->where('stype',$type_id)->select('map_id','x','y','img')->get();

		$data['other'] = ServicePoint::whereNotIN('map_id',[$map_id])->where('stype',$type_id)->orderBy('map_id','asc')->select('map_id','x','y','img')->get();

		return response_json(1, $data);
	}

}