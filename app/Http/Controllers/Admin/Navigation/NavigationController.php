<?php
namespace App\Http\Controllers\Admin\Navigation;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Exhibit;
use Illuminate\Support\Str;
use App\Models\SvgMapTable as Map;
use App\Models\NavigationRoad;
use App\Models\NavigationPoint;
use Illuminate\Support\Facades\DB;

/**
 * 路线导航控制器
 *
 * @author yyj
 * @package App\Http\Controllers\Navigation
 */
class NavigationController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @param $map_id
	 * @return array 数据格式化
	 */
	private function getPointsByMapId($map_id)
	{
		$res = [];
		$point_list = NavigationPoint::where('map_id', '=', $map_id)->get();
		foreach ($point_list as $point) {
			$info['name'] = $point['id'];
			$info['value'] = $point['id'];
			$info['geoCoord'] = array(
				$point['x'],
				$point['y']
			);
			$res[] = $info;
		}
		return $res;
	}

	/**
	 * 地图信息列表显示
	 *
	 * @author yyj 20171117
	 * @param $id int 地图id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function show($id = 0)
	{
		$maps = Map::all()->toArray();
		if ($id) {
			$map_id = $id;
		} else {
			$map_id = $maps[0]['id'];
		}
		if (!$map_id) {
			$map_id = $maps[0]['id'];
		}
		$path = Map::where('id', '=', $map_id)->value('map_path');
		$points = $this->getPointsByMapId($map_id);
		return view('admin.navigation.map', [
			'maps' => $maps,
			'map_id' => $map_id,
			'infoArr' => $points,
			'path' => $path
		]);
	}

	/**
	 *
	 *   辅助导航点添加
	 *  * @author yyj 20171117
	 */
	public function add_point()
	{

		$x = request('x');
		$y = request('y');
		$y_point = request('y_point');
		$x_point = request('x_point');
		$map_id = request('map_id');
		if (!empty($x_point)) {
			$x = NavigationPoint::where('id', '=', $x_point)->where('map_id', '=', $map_id)->value('x');
			if (empty($x)) {
				return $this->error('x轴关联点不存在');
			}
		}
		if (!empty($y_point)) {
			$y = NavigationPoint::where('id', '=', $y_point)->where('map_id', '=', $map_id)->value('y');
			if (empty($y)) {
				return $this->error('y轴关联点不存在');
			}
		}
		$data['map_id'] = $map_id;
		$data['x'] = $x;
		$data['y'] = $y;
		$data['axis'] = json_encode(array(
			'x' => $x,
			'y' => $y
		));
		$i = NavigationPoint::create($data);
		if ($i) {
			return $this->success(get_session_url("show/$map_id"));
		} else {
			return $this->error('操作失败，请重试');
		}
	}

	/**
	 * 进行关联点信息编辑
	 *
	 * @param $map_id int 地图id
	 * @param $id int 辅助点id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @author  yyj 20171117
	 */
	public function edit_navigation($map_id, $id)
	{
		//获取地图url
		$where['id'] = $map_id;
		//获取地图url
		$map_path = Map::where('id', '=', $map_id)->value('map_path');
		//获取已经关联的点位信息
		$one_floors = NavigationRoad::where([
			[
				'floors',
				'=',
				'1'
			],
			[
				'map_id',
				'=',
				$map_id
			]
		])->where([
			[
				'end_id',
				'=',
				$id
			]
		])->orwhere([
			[
				'start_id',
				'=',
				$id
			]
		])->get()->toArray();
		foreach ($one_floors as $k => $g) {
			$ids[$k] = $id == $g['start_id'] ? $g['end_id'] : $g['start_id'];//点位Id
		}
		if (empty($one_floors)) {
			$ids[] = $id;
		}
		//获取当前关联点位信息
		$info = NavigationPoint::whereIn('id', $ids)->where('map_id', '=', $map_id)->get();
		//获取其余点位信息
		$info2 = NavigationPoint::whereNotIn('id', $ids)->where('map_id', '=', $map_id)->get();
		return view('admin.navigation.edit_navigation', [
			'info' => $info,
			'info2' => $info2,
			'map_path' => $map_path,
			'$ids' => $ids,
			'map_id' => $map_id,
			'id' => $id
		]);
	}

	/**
	 * 进行管连接点信息编辑保存
	 *
	 * @author  yyj 20171117
	 */
	public function edit_navigation_save()
	{
		$ids = request('ids');
		$map_id = request('map_id');
		$id = request('id');
		if (count($ids) <= 0) {
			return $this->error('请选择关联点位');
		} else {
			//删除所有与该点相关的路径
			NavigationRoad::where([
				[
					'end_id',
					'=',
					$id
				]
			])->orwhere([
				[
					'start_id',
					'=',
					$id
				]
			])->delete();
			//查询所有点位坐标
			$ids[4] = $id;
			$axis_info = NavigationPoint::whereIn('id', $ids)->where('map_id', '=', $map_id)->select('id', 'axis')->get()->toArray();
			foreach ($axis_info as $k => $v) {
				$axis_info_new[$v['id']] = $v['axis'];
			}
			//更新相关路径
			foreach ($ids as $k => $g) {
				if ($g != '' && $g != null && $g != $id) {
					$data['start_id'] = $id;
					$data['end_id'] = $g;
					$data['road'] = $axis_info_new[$id] . ',' . $axis_info_new[$g];
					$data['axis'] = $id . '_' . $g;
					$data['floors'] = 1;
					$data['map_id'] = $map_id;
					//计算距离
					$size = 1;//地图比例
					$arr1 = json_decode($axis_info_new[$id], true);
					$arr2 = json_decode($axis_info_new[$g], true);
					$x1 = $arr1['x'];
					$x2 = $arr2['x'];
					$y1 = $arr1['y'];
					$y2 = $arr2['y'];
					$distance = abs($x1 - $x2) * abs($x1 - $x2) + abs($y1 - $y2) * abs($y1 - $y2);
					$distance = sqrt($distance) / $size;
					$data['distance'] = round($distance, 2);
					NavigationRoad::create($data);
				}
			}
		}
		return $this->success(get_session_url("show/$map_id"));
	}

	/**
	 *  进行节点的编辑
	 *
	 * @author yyj 20171117
	 */
	function edit_point($map_id, $id)
	{
		//获取地图url
		$map_path = Map::where('id', '=', $map_id)->value('map_path');
		//获取点位信息
		$info = NavigationPoint::where('id', '=', $id)->first();
		return view('admin.navigation.edit_point', [
			'map_id' => $map_id,
			'id' => $id,
			'map_path' => $map_path,
			'info' => $info
		]);
	}

	/**
	 *  辅助点编辑删除
	 *
	 * @author yyj 20171117
	 */
	public function edit_point_save()
	{
		$data['x'] = request('x');
		$data['y'] = request('y');
		$map_id = request('map_id');
		$data['axis'] = json_encode(array(
			'x' => request('x'),
			'y' => request('y')
		));
		if (request('del')) {
			$r = NavigationPoint::where('id', '=', request('del'))->where('map_id', '=', $map_id)->delete();
			if ($r) {
				NavigationRoad::where([
					[
						'end_id',
						'=',
						request('del')
					]
				])->orwhere([
					[
						'start_id',
						'=',
						request('del')
					]
				])->delete();
				return $this->success('删除成功');
			} else {
				return $this->error('操作失败，请重试');
			}
		}
		$i = NavigationPoint::where('id', '=', request('id'))->where('map_id', '=', $map_id)->update($data);
		if ($i > 0) {
			return $this->success(get_session_url("show/$map_id"));
		} else {
			return $this->error('操作失败，请重试');
		}

	}

	//地图切换
	public function ajax_map2()
	{
		//获取地图信息
		$map_id = request('map_id');
		$path = Map::where('id', '=', $map_id)->value('map_path');
		//获取该地图上的讲解点位信息
		$pos_info = Exhibit::where('map_id', $map_id)->where('is_show_map', 1)->select('auto_num', 'x', 'y')->get();
		//获取地图上的导航辅助点
		$dh_info = NavigationPoint::where('map_id', '=', $map_id)->get();
		$arr['dh_count'] = count($dh_info);
		$arr['dh_info'] = $dh_info;
		$arr['count'] = count($pos_info);
		$arr['pos_info'] = $pos_info;
		$arr['map_path'] = $path;
		//获取单条线路
		$road_info = NavigationRoad::where('map_id', '=', $map_id)->where('floors', '=', 1)->get()->toArray();
		if (!empty($road_info)) {
			foreach ($road_info as $k => $g) {
				$road[$k] = json_decode('[' . $g['road'] . ']', true);
			}
			$arr['road_info'] = $road;
			$arr['road_num'] = count($road);
		} else {
			$arr['road_info'] = '';
			$arr['road_num'] = '';
		}

		return $arr;

	}

	public function ajax_axis()
	{
		$id = request('id');
		$map_id = request('map_id');
		$info = NavigationPoint::where('id', '=', $id)->where('map_id', '=', $map_id)->first();
		return $info;
	}

}