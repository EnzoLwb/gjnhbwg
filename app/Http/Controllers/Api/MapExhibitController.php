<?php

namespace App\Http\Controllers\Api;

use App\Dao\ResourceDao;
use App\Exceptions\ApiErrorException;
use App\Models\Autonum;
use App\Models\Exhibition;
use App\Models\Exhibit;
use App\Models\SvgMapTable;
use App\Models\VersionList;
use App\Models\VisitRoad;
use App\Models\NavigationRoad;
use App\Dao\NavigationDao;
use App\Models\Trajectory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 展品导览相关接口
 *
 * @author yyj 20171112
 * @package App\Http\Controllers\Api
 */
class MapExhibitController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 获取地图页展品数据
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /map_exhibit 301.获取地图页展品数据
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiParam {int} map_id 地图编号,传0返回所有数据
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {string} exhibit_icon1 地图页图片(亮)
	 * @apiSuccess {string} exhibit_icon2 地图页图片(暗)
	 * @apiSuccess {int} map_id 地图编号
	 * @apiSuccess {int} x x轴坐标
	 * @apiSuccess {int} y y轴坐标
	 * @apiSuccess {array} auto_list 蓝牙关联列表
	 * @apiSuccess {string} mx_and 安卓门限
	 * @apiSuccess {string} mx_ios ios门限
	 * @apiSuccess {int} autonum 蓝牙编号
	 * @apiSuccess {string} auto_string 关联的蓝牙号
	 *
	 */
	public function map_exhibit()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'map_id' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$map_id = request('map_id', 0);
		$data = [];
		//获取展品信息
		$exhibit_list = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', $language)->where('exhibit.is_show_map', 1)->select('exhibit_language.exhibit_name', 'exhibit_language.content as exhibit_content', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibit.map_id', 'exhibit.x', 'exhibit.y', 'exhibit_language.audio');
		//获取蓝牙关联详情
		$auto_info = Autonum::select('exhibit_list', 'autonum', 'mx_and', 'mx_ios');
		if ($map_id) {
			$exhibit_list = $exhibit_list->where('exhibit.map_id', $map_id);
			$auto_info = $auto_info->where('map_id', $map_id);
		}
		$exhibit_list = $exhibit_list->get();
		$auto_info = $auto_info->get()->toArray();

		foreach ($auto_info as $k => $g) {
			$auto_info[$k]['exhibit_list'] = json_decode($g['exhibit_list']);
			foreach ($auto_info[$k]['exhibit_list'] as $kk => $gg) {
				$auto_string_list[$gg][] = $g['autonum'];
				$auto_list[$gg][] = [
					'autonum' => $g['autonum'],
					'mx_and' => $g['mx_and'],
					'mx_ios' => $g['mx_ios'],
				];
			}
		}
		foreach ($exhibit_list as $k => $g) {
			$data[$k]['exhibit_id'] = $g->exhibit_id;
			$data[$k]['exhibit_name'] = $g->exhibit_name;
			$data[$k]['exhibit_content'] = $g->exhibit_content;
			$data[$k]['exhibit_icon1'] = json_decode($g->exhibit_img, true)['exhibit_icon1'];
			$data[$k]['exhibit_icon2'] = json_decode($g->exhibit_img, true)['exhibit_icon2'];
			$data[$k]['map_id'] = $g->map_id;
			$data[$k]['x'] = $g->x;
			$data[$k]['y'] = $g->y;
			if (isset($auto_string_list[$g->exhibit_id]) && isset($auto_list[$g->exhibit_id])) {
				$data[$k]['auto_string'] = implode('#', $auto_string_list[$g->exhibit_id]);
				$data[$k]['auto_list'] = $auto_list[$g->exhibit_id];
			} else {
				$data[$k]['auto_string'] = '';
				$data[$k]['auto_list'] = [];
			}
		}

		return response_json(1, $data);
	}

	/**
	 * 获取附近展厅
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /map_near_exhibition 02.获取附近展厅
	 * @apiGroup MapExhibit
	 * @apiIgnore
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiParam {string} autonum 收到的蓝牙号
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {int} exhibition_id 展厅id
	 * @apiSuccess {string} exhibition_img 展厅图片
	 *
	 */
	public function map_near_exhibition()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'autonum' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$autonum = request('autonum', 1);
		$exhibit = Autonum::where('autonum', $autonum)->value('exhibit_list');
		$exhibit_arr = json_decode($exhibit, true);
		if (!empty($exhibit_arr) && is_array($exhibit_arr)) {
			$exhibition_id = Exhibit::whereIn('id', $exhibit_arr)->pluck('exhibition_id')->toArray();
			if (!empty($exhibition_id) && is_array($exhibition_id)) {
				$exhibition = Exhibition::join('exhibition_language', 'exhibition_language.exhibition_id', '=', 'exhibition.id')->where('exhibition_language.language', $language)->whereIn('exhibition.id', $exhibition_id)->select('exhibition_language.exhibition_name', 'exhibition.exhibition_img', 'exhibition.id as exhibition_id')->get()->toArray();
				foreach ($exhibition as $k => $g) {
					$exhibition[$k]['exhibition_img'] = json_decode($g['exhibition_img'], true)['list_img'];
				}
				return response_json(1, $exhibition);
			} else {
				return response_json(1, []);
			}
		} else {
			return response_json(1, []);
		}
	}

	/**
	 * 获取附近展品
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /map_near_exhibit 302.获取附近展品（蓝牙号查询）
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiParam {string} autonum_str 收到的蓝牙号用#拼接例如101#102
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} exhibit_id 展品编号
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {string} floor 所在楼层
	 *
	 */
	public function map_near_exhibit()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'autonum_str' => 'required|string',
		]);
		$language = request('language', 1);
		$autonum_str = request('autonum_str', 0);
		$arr = explode('#', $autonum_str);
		$exhibit_id_arr = Autonum::whereIn('autonum', $arr)->pluck('exhibit_list');
		if (empty($exhibit_id_arr)) {
			return response_json(1, []);
		} else {
			$exhibit_arr = [];
			foreach ($exhibit_id_arr as $k => $g) {
				$exhibit_arr = array_merge(json_decode($g, true), $exhibit_arr);
			}
			$exhibit_arr = array_unique($exhibit_arr);
		}
		$exhibit_list = Exhibit::join('exhibit_language', function ($join) use ($language) {
			$join->on('exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', '=', $language);
		})->join('exhibition', 'exhibition.id', '=', 'exhibit.exhibition_id')->join('exhibition_language', function ($join) use ($language) {
			$join->on('exhibition.id', '=', 'exhibition_language.exhibition_id')->where('exhibition_language.language', '=', $language);
		})->where('exhibit.is_show_list', 1)->whereIn('exhibit.id', $exhibit_arr)->select('exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibition_language.exhibition_name', 'exhibition.floor_id')->get();

		$data = [];
		foreach ($exhibit_list as $k => $g) {
			$imgs = json_decode($g['exhibit_img'], true);
			$imgs = isset($imgs['exhibit_list']) ? $imgs['exhibit_list'] : '';
			$data[$k]['exhibit_name'] = $g['exhibit_name'];
			$data[$k]['exhibit_id'] = $g['exhibit_id'];
			$data[$k]['exhibit_list_img'] = $imgs;
			$data[$k]['exhibition_name'] = $g['exhibition_name'];
			$data[$k]['floor'] = $g['floor_id'];
		}
		return response_json(1, $data);
	}

	/**
	 * 导航线路生成接口
	 *
	 * @author yyj 20171117
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /road_navigation 307.导航线路生成接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,d:导览机
	 * @apiParam {string} deviceno 机器号
	 * @apiParam {int} map_id 地图id（楼层）
	 * @apiParam {int} exhibit_id 终点展品id
	 * @apiSuccess {int} status 1正常生成线路数据 ， -1未定位到您的位置信息，-2楼层位置不对，请切换楼层 ，-3输入的展品id有误
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} x x轴坐标
	 * @apiSuccess {int} y y轴坐标
	 *
	 */
	public function road_navigation()
	{

		$this->validate([
			'deviceno' => 'required',
			'map_id' => 'required|in:1,2,3',
			'exhibit_id' => 'required|min:0|integer',
		]);

		$map_id = request('map_id', 1);
		$deviceno = request('deviceno');
		$exhibit_id_last = request('exhibit_id');
		$road_info = [];

		$look_date = date('Y-m-d');

		$trajectory_info = Trajectory::where('look_date', $look_date)->where('deviceno', $deviceno)->orderBy('updated_at', 'desc')->first();

		if (empty($trajectory_info)) {
			//return response_json(-1, $road_info, '未定位到您的位置信息');
			throw new ApiErrorException('未定位到您的位置信息');
		} else {
			$trajectory_info = $trajectory_info->toArray();
			if ($trajectory_info['map_id'] != $map_id) {
				//return response_json(-2, $road_info, '请走到' . $map_id . '层再导航');
				throw new ApiErrorException('请走到' . $map_id . '层再导航');
			}

			$exhibit_info_last = Exhibit::where('id', $exhibit_id_last)->first();
			if (empty($exhibit_info_last)) {
				//return response_json(-3, $road_info, '输入的展品id有误');
				throw new ApiErrorException('输入的展品id有误');
			}

			$road_arr_info = [];
			$road_arr = NavigationDao::get_road($trajectory_info['x'], $trajectory_info['y'], $exhibit_info_last['x'], $exhibit_info_last['y'], $map_id);


			if (count($road_arr_info) && count($road_arr)) {
				unset($road_arr[0]);
				$road_arr_info = array_merge($road_arr_info, $road_arr);
			} else {
				$road_arr_info = $road_arr;
			}

			$road_info = $road_arr_info;

			return response_json(1, $road_info);
		}

	}

	/**
	 * 楼层路线生成接口
	 *
	 * @author yyj 20171117
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /road_info 303.楼层路线生成接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiParam {int} map_id 地图id（楼层）
	 * @apiParam {int} road_id 路线id
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} x x轴坐标
	 * @apiSuccess {int} y y轴坐标
	 *
	 */
	public function road_info()
	{

		$this->validate([
			'map_id' => 'required|in:1,2,3'
		]);

		$language = request('language', 1);
		$map_id = request('map_id', 1);
		$road_id = request('road_id', 1);
		$road_info = [];

		$road_exhibit = VisitRoad::where('id', $road_id)->where('type', 1)->first();

		if (empty($road_exhibit)) {
			return response_json(1, $road_info);
		} else {
			$weight_exhibit_ids_mapid = 'weight_exhibit_ids' . $map_id;

			if (empty($road_exhibit[$weight_exhibit_ids_mapid])) {
				return response_json(1, $road_info);
			}

			$exhibit_ids = json_decode($road_exhibit[$weight_exhibit_ids_mapid], true);

			//获取要经过展品
			$exhibit_list = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', $language)->whereIn('exhibit.id', $exhibit_ids)->where('exhibit.map_id', $map_id)->where('exhibit.is_show_map', 1)->select('exhibit.auto_num', 'exhibit.x', 'exhibit.y')->orderBy('exhibit.auto_num', 'asc')->get()->toArray();
			if (!empty($exhibit_list)) {
				$n = count($exhibit_list);
				$road_arr_info = [];
				for ($i = 0; $i < $n - 1; $i++) {
					$road_arr = NavigationDao::get_road($exhibit_list[$i]['x'], $exhibit_list[$i]['y'], $exhibit_list[$i + 1]['x'], $exhibit_list[$i + 1]['y'], $map_id);
					if (count($road_arr_info) && count($road_arr)) {
						unset($road_arr[0]);
						$road_arr_info = array_merge($road_arr_info, $road_arr);
					} else {
						$road_arr_info = $road_arr;
					}
				}
				$road_info = $road_arr_info;

				return response_json(1, $road_info);
			} else {
				return response_json(1, []);
			}

		}
	}

	/**
	 * 路线列表接口
	 *
	 * @author yyj 20171117
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /road_list 304.路线列表接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} data.road_id 线路id
	 * @apiSuccess {string} data.road_name 线路名
	 * @apiSuccess {string} data.road_img 线路图
	 *
	 */
	public function road_list()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$data = [];
		// 处理排序
		$query = VisitRoad::orderBy('visit_road.id', 'asc')->join('visit_road_language', 'visit_road.id', '=', 'visit_road_language.road_id')->where('visit_road.type', 1)->where('visit_road_language.language', $language);

		// 取得列表
		$data = $query->select('visit_road.id as road_id', 'visit_road_language.road_name', 'visit_road.road_img')->get();
		return response_json(1, $data);
	}

	/**
	 * 路线列表接口
	 *
	 * @author yyj 20171117
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /road_detail 305.路线详情接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiParam {int} road_id 路线id
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} data.road_id 线路id
	 * @apiSuccess {string} data.road_name 线路名
	 * @apiSuccess {array} data.floor1 1层数据
	 * @apiSuccess {array} data.floor2 2层数据
	 * @apiSuccess {array} data.floor3 3层数据
	 * @apiSuccess {int} data.exhibit_counts 展品数
	 * @apiSuccess {string} data.road_long 游览时长
	 *
	 */
	public function road_detail()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'road_id' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$road_id = request('road_id', 1);

		$road_exhibit = VisitRoad::where('id', $road_id)->where('type', 1)->first();

		if (empty($road_exhibit)) {
			return response_json(1, []);
		} else {
			$query = VisitRoad::orderBy('visit_road.id', 'asc')->join('visit_road_language', 'visit_road.id', '=', 'visit_road_language.road_id')->where('visit_road.type', 1)->where('visit_road.id', $road_id)->where('visit_road_language.language', $language);
			$road_data = $query->select('visit_road.id as road_id', 'visit_road.road_long', 'visit_road.weight_exhibit_ids', 'visit_road.weight_exhibit_ids1', 'visit_road.weight_exhibit_ids2', 'visit_road.weight_exhibit_ids3', 'visit_road_language.road_name')->first();
			if (empty($road_data)) {
				return response_json(1, []);
			} else {
				$road_data = $road_data->toArray();
				$data = array();
				$data['road_name'] = $road_data['road_name'];

				$data['floor1'] = $this->exhibit_handle($road_data['weight_exhibit_ids1'], 1, $language);
				$data['floor2'] = $this->exhibit_handle($road_data['weight_exhibit_ids2'], 2, $language);
				$data['floor3'] = $this->exhibit_handle($road_data['weight_exhibit_ids3'], 3, $language);

				$exhibit_ids = json_decode($road_data['weight_exhibit_ids'], true);
				$data['exhibit_counts'] = count($exhibit_ids);
				$data['road_long'] = $road_data['road_long'];

				return response_json(1, $data);
			}
		}

	}

	/**
	 * 路线列表接口
	 *
	 * @author yyj 20171117
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /road_detail_all 306.路线详情全部接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3韩语，4日语，5法语，6俄语
	 * @apiParam {int} road_id 路线id ,传0返回全部线路
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} data.road_id 线路id
	 * @apiSuccess {string} data.road_name 线路名
	 * @apiSuccess {array} data.floor 楼层数据
	 * @apiSuccess {int} data.exhibit_counts 展品数
	 * @apiSuccess {string} data.road_long 游览时长
	 *
	 */
	public function road_detail_all()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'road_id' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$road_id = request('road_id', 1);

		if($road_id==0){
			$road_exhibits = VisitRoad::where('type', 1)->get();
		}else{
			$road_exhibits = VisitRoad::where('id', $road_id)->where('type', 1)->get();
		}

		if (empty($road_exhibits)) {
			return response_json(1, []);
		} else {
			$data_return =  array();
			foreach ($road_exhibits as $rk=>$rv) {

				$query = VisitRoad::orderBy('visit_road.id', 'asc')->join('visit_road_language', 'visit_road.id', '=', 'visit_road_language.road_id')->where('visit_road.type', 1)->where('visit_road.id', $rv['id'])->where('visit_road_language.language', $language);
				$road_data = $query->select('visit_road.id as road_id', 'visit_road.road_long', 'visit_road.weight_exhibit_ids', 'visit_road.weight_exhibit_ids1', 'visit_road.weight_exhibit_ids2', 'visit_road.weight_exhibit_ids3', 'visit_road_language.road_name')->first();
				if (empty($road_data)) {
				} else {
					$road_data = $road_data->toArray();
					$data = array();
					$data['road_id'] = $road_data['road_id'];
					$data['road_name'] = $road_data['road_name'];

					$floor1 = $this->exhibit_handle($road_data['weight_exhibit_ids1'], 1, $language);
					if($floor1){
						$data['floor'][] = $floor1;
					}

					$floor2 = $this->exhibit_handle($road_data['weight_exhibit_ids2'], 2, $language);
					if($floor2){
						$data['floor'][] = $floor2;
					}

					$floor3 = $this->exhibit_handle($road_data['weight_exhibit_ids3'], 3, $language);
					if($floor3){
						$data['floor'][] = $floor3;
					}


					$exhibit_ids = json_decode($road_data['weight_exhibit_ids'], true);
					$data['exhibit_counts'] = count($exhibit_ids);
					$data['road_long'] = $road_data['road_long'];

					$data_return[]=$data;
				}


			}

			return response_json(1, $data_return);
		}

	}

	function exhibit_handle($weight_exhibit_ids, $map_id, $language)
	{
		if ($weight_exhibit_ids) {
			$exhibit_ids_floor = json_decode($weight_exhibit_ids, true);
			$exhibition_info = Exhibition::join('exhibition_language', 'exhibition_language.exhibition_id', '=', 'exhibition.id')->where('exhibition.id', $map_id)->where('exhibition_language.language', $language)->where('exhibition.is_show_list', 1)->select('exhibition_language.exhibition_name', 'exhibition_language.exhibition_address', 'exhibition.id as exhibition_id')->first()->toarray();
			$exhibit_list = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', $language)->whereIn('exhibit.id', $exhibit_ids_floor)->where('exhibit.map_id', $map_id)->where('exhibit.is_show_map', 1)->select('exhibit.id as exhibit_id', 'exhibit_language.exhibit_name')->get()->toArray();

			$data['exhibition_id'] = $exhibition_info['exhibition_id'];
			$data['exhibition_name'] = $exhibition_info['exhibition_name'];
			$data['exhibition_address'] = $exhibition_info['exhibition_address'];

			$exhibit_list_temp = array();
			foreach ($exhibit_list as $item) {
				$exhibit_list_temp[$item['exhibit_id']] = $item['exhibit_name'];
			}

			foreach ($exhibit_ids_floor as $item) {
				$temp = array();
				$temp['exhibit_id'] = $item;
				$temp['exhibit_name'] = $exhibit_list_temp[$item];
				$data['exhibit'][] = $temp;
			}
			return $data;
		} else {
			return '';
		}
	}

	/**
	 * 资源版本更新(导览机专用)
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /update_version_resource 资源版本更新(导览机专用)
	 * @apiGroup Resource
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} version_id 资源版本编号，导览机内置版本号为0
	 * @apiSuccess {string} is_update 是否需要更新1需要，0不需要
	 * @apiSuccess {string} version_id 版本编号
	 * @apiSuccess {string} down_url 资源下载地址
	 */
	public function update_version_resource()
	{
		$this->validate([
			'version_id' => 'required|min:0|integer',
		]);
		$version = request('version_id');
		//白板程序直接下载整包资源，版本更新到最高。
		//当前最高版本
		$the_newest = VersionList::where('type', '<>', 0)->OrderBy('id', 'desc')->value('id');

		if ($version == $the_newest) {
			$info['is_update'] = 0;
			$info['version_id'] = $the_newest;
			$info['down_url'] = '';
		} elseif ($version == 0) {
			$info['is_update'] = 1;
			$info['version_id'] = $the_newest;
			$info['down_url'] = '/resource_zip/resource.zip';
		} elseif ($version < $the_newest) {
			$info = $this->get_zip($version, $the_newest);
		} else {
			return response_json(0, [], '版本号错误');
		}
		return response_json(1, $info, '查询成功');
	}

	private function get_zip($version, $the_newest)
	{
		//获取下一版本编号
		$next_version = $version + 1;
		if (file_exists(base_path() . '/public/resource_zip/version_' . $next_version . '/resource.zip')) {
			$info['is_update'] = 1;
			$info['version_id'] = $next_version;
			$info['down_url'] = '/resource_zip/version_' . $next_version . '/resource.zip';
		} else {
			if ($next_version < $the_newest) {
				//直接全量更新
				$info['is_update'] = 1;
				$info['version_id'] = $the_newest;
				$info['down_url'] = '/resource_zip/resource.zip';
			} else {
				$info['is_update'] = 0;
				$info['version_id'] = $the_newest;
				$info['down_url'] = '';
			}
		}
		return $info;
	}

	/**
	 * 获取所有数据库
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /datas_info 获取所有数据库,获取后直接覆盖原数据库(导览机专用)
	 * @apiGroup Resource
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiSuccess {array} autonum_list 多模蓝牙数据列表
	 * @apiSuccess {int} autonum 多模蓝牙编号
	 * @apiSuccess {int} map_id 展厅编号
	 * @apiSuccess {int} x x坐标
	 * @apiSuccess {int} y y坐标
	 * @apiSuccess {int} mx_dlj 触发门限
	 * @apiSuccess {array} map_list 地图数据
	 * @apiSuccess {int} map_id 地图编号
	 * @apiSuccess {int} width 宽
	 * @apiSuccess {int} height 高
	 * @apiSuccess {int} floor_id 楼层id
	 * @apiSuccess {array} exhibition_* 展厅语种数据
	 * @apiSuccess {int} exhibition_id 展厅编号
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {string} exhibition_address 展厅地址
	 * @apiSuccess {string} exhibition_subtitle 展厅副标题
	 * @apiSuccess {string} exhibition_content 展厅简介
	 * @apiSuccess {int} is_lb 是否轮播1轮播，2不轮播
	 * @apiSuccess {int} type 展厅类别1常设展厅2临时展厅
	 * @apiSuccess {int} is_show_list 是否显示1显示2不显示
	 * @apiSuccess {int} order_id 排序编号，越小的在越前面
	 * @apiSuccess {int} floor_id 楼层id
	 * @apiSuccess {array} exhibit_* 展品语种数据
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {int} is_lb 是否轮播
	 * @apiSuccess {int} is_show_map 是否在地图页显示
	 * @apiSuccess {int} is_show_list 是否在列表页显示
	 * @apiSuccess {int} map_id 所属地图id
	 * @apiSuccess {int} x x轴坐标
	 * @apiSuccess {int} y y轴坐标
	 * @apiSuccess {string} exhibition_id 所属展厅id
	 * @apiSuccess {string} exhibit_num 展品编号
	 * @apiSuccess {int} order_id 排序编号，越小的在越前面
	 * @apiSuccess {int} type 展品类别1常展2临展
	 * @apiSuccess {string} autonum_list 相关联的蓝牙号
	 * @apiSuccess {int} imgs_num 展品图片数量
	 */
	public function datas_info()
	{
		//获取多模蓝牙数据
		$info['autonum_list'] = Autonum::orderBy('autonum', 'asc')->select('autonum', 'map_id', 'x', 'y', 'mx_dlj')->get()->toArray();
		//获取地图数据
		$info['map_list'] = SvgMapTable::select('id as map_id', 'floor_id', 'width', 'height')->get()->toArray();
		//获取语种数据
		foreach (config('language') as $k => $g) {
			//展厅数据
			$info['exhibition_' . $g['dir']] = Exhibition::join('exhibition_language', 'exhibition.id', '=', 'exhibition_language.exhibition_id')->where('exhibition_language.language', '=', $k)->select('exhibition_language.exhibition_id', 'exhibition_language.exhibition_name', 'exhibition_language.exhibition_address', 'exhibition_language.exhibition_subtitle', 'exhibition_language.content as exhibition_content', 'exhibition.is_lb', 'exhibition.type', 'exhibition.is_show_list', 'exhibition.order_id', 'exhibition.floor_id')->get()->toArray();
		}
		//获取蓝牙关联详情
		$auto_info = Autonum::select('exhibit_list', 'autonum', 'mx_and', 'mx_ios', 'mx_dlj');
		$auto_info = $auto_info->get()->toArray();
		foreach ($auto_info as $k => $g) {
			$auto_info[$k]['exhibit_list'] = json_decode($g['exhibit_list']);
			foreach ($auto_info[$k]['exhibit_list'] as $kk => $gg) {
				$auto_string_list[$gg][] = $g['autonum'];
				$mxdlj_string_list[$gg][] = $g['mx_dlj'];
			}
		}
		foreach (config('language') as $k => $g) {
			//展品数据
			$info['exhibit_' . $g['dir']] = Exhibit::join('exhibit_language', 'exhibit.id', '=', 'exhibit_language.exhibit_id')->where('exhibit_language.language', '=', $k)->select('exhibit_language.exhibit_id', 'exhibit_language.exhibit_name', 'exhibit_language.content as exhibit_content', 'exhibit.is_lb', 'exhibit.is_show_map', 'exhibit.is_show_list', 'exhibit.map_id', 'exhibit.x', 'exhibit.y', 'exhibit.exhibition_id', 'exhibit.exhibit_num', 'exhibit.order_id', 'exhibit.type', 'exhibit.imgs_num')->get()->toArray();
			foreach ($info['exhibit_' . $g['dir']] as $kk => $gg) {
				if (isset($auto_string_list[$gg['exhibit_id']])) {
					$info['exhibit_' . $g['dir']][$kk]['autonum_list'] = implode('#', $auto_string_list[$gg['exhibit_id']]);
					$info['exhibit_' . $g['dir']][$kk]['mx_dlj_list'] = implode('#', $mxdlj_string_list[$gg['exhibit_id']]);
				} else {
					$info['exhibit_' . $g['dir']][$kk]['autonum_list'] = '';
					$info['exhibit_' . $g['dir']][$kk]['mx_dlj_list'] = '';
				}
			}
		}

		foreach (config('language') as $k => $g) {
			//线路数据
			$info['road_' . $g['dir']] = VisitRoad::join('visit_road_language', 'visit_road.id', '=', 'visit_road_language.road_id')->where('visit_road_language.language', '=', $k)->select('visit_road_language.road_id', 'visit_road_language.road_name')->get()->toArray();
		}

		return response_json(1, $info, '查询成功');
	}
}