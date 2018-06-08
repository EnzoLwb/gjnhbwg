<?php

namespace App\Http\Controllers\Admin\AppRoad;

use App\Dao\ExhibitDao;
use App\Models\VisitRoad;
use App\Models\VisitRoadLanguage;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BaseAdminController;

class AppRoadController extends BaseAdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 路线列表
	 *
	 * @author yyj 20171116
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function road_list()
	{
		// 处理排序
		$query = VisitRoad::orderBy('visit_road.id', 'desc')->join('visit_road_language', 'visit_road.id', '=', 'visit_road_language.road_id')->where('visit_road.type', 1)->where('visit_road_language.language', 1);

		// 取得列表
		$info = $query->select('visit_road.id', 'visit_road_language.road_name', 'visit_road.updated_at')->paginate(parent::PERPAGE);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());

		return view('admin.approad.road_list', [
			'info' => $info,
		]);
	}

	/**
	 * 路线编辑
	 *
	 * @author yyj 20171116
	 * @param  int $id 路线id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit($id)
	{
		if (request()->isMethod('post')) {
			$this->validate(request(), [
				'road_exhibit_id' => [
					'required'
				],
				'road_name_1' => 'required'
			]);
			$data = [
				'uid' => 0,
				'type' => 1,
				'road_list' => json_encode(request('road_exhibit_id')),
				'road_info' => json_encode([]),
			];

			$road_raw_list = request('road_exhibit_id');
			$road_info = array();
			$weight_exhibit_ids = array();
			foreach ($road_raw_list as $key => $exhibit_id) {
				$road_info[] = $exhibit_id . '_' . request('weight_' . $exhibit_id);
				$weight_exhibit_ids[$exhibit_id] = request('weight_' . $exhibit_id);
			}
			$data['road_info'] = json_encode($road_info);

			arsort($weight_exhibit_ids);
			$data['weight_exhibit_ids'] = json_encode(array_keys($weight_exhibit_ids));

			//基本信息入库
			if ($id == 'add') {
				$new_info = VisitRoad::create($data);
				$road_id = $new_info->id;
			} else {
				VisitRoad::where('id', $id)->update($data);
				$road_id = $id;
				VisitRoadLanguage::where('road_id', $road_id)->delete();
			}
			//语种信息入库
			foreach (config('language') as $k => $g) {
				//展览名称不为空就写入数据
				if (!empty(request('road_name_' . $k))) {
					$data2 = [
						'road_id' => $road_id,
						'road_name' => request('road_name_' . $k),
						'language' => $k
					];
					VisitRoadLanguage::create($data2);
				}
			}
			return $this->success(get_session_url('road_list'));
		} else {
			$info = [];
			$road_list = [];
			$road_raw_info = array();
			if ($id !== 'add') {
				$info = VisitRoad::where('id', $id)->first()->toArray();
				$language_info = VisitRoadLanguage::where('road_id', $id)->get()->toArray();
				$road_list = json_decode($info['road_list'], true);
				$road_info = json_decode($info['road_info'], true);
				foreach ($road_info as $key => $v) {
					$v = explode('_', $v);
					if (count($v) == 1) {
						$road_raw_info[$v[0]] = $key;
					} else {
						$road_raw_info[$v[0]] = $v[1];
					}
				}

				foreach ($language_info as $k => $g) {
					$info['language'][$g['language']] = $g;
				}
			}
			$exhibit_list = ExhibitDao::road_list(1, $road_list, 0);
			return view('admin.approad.edit', array(
				'info' => $info,
				'id' => $id,
				'exhibit_list' => $exhibit_list,
				'road_raw_info' => $road_raw_info
			));
		}
	}

	/**
	 * 路线删除
	 *
	 * @author yyj 20171116
	 * @param  int $id 展览id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function delete($id)
	{
		VisitRoad::where('id', $id)->delete();
		VisitRoadLanguage::where('road_id', $id)->delete();
		return $this->success(get_session_url('road_list'));
	}
}
