<?php

namespace App\Http\Controllers\Admin\AppRoad;

use App\Dao\ExhibitDao;
use App\Dao\ResourceDao;
use App\Models\Exhibit;
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
				/*'road_exhibit_id' => [
					'required'
				],*/
				'road_long' => 'required',
				'road_img' => 'required',
				'road_name_1' => 'required'
			]);

			$road_raw_list1 = is_array(request('road_exhibit_id1')) ? request('road_exhibit_id1') : array();
			$road_raw_list2 = is_array(request('road_exhibit_id2')) ? request('road_exhibit_id2') : array();
			$road_raw_list3 = is_array(request('road_exhibit_id3')) ? request('road_exhibit_id3') : array();

			$road_raw_list = array_merge($road_raw_list1, $road_raw_list2, $road_raw_list3);

			if (empty($road_raw_list)) {
				return response()->json([
					'status' => 'false',
					'msg' => '请选择线路上展品'
				]);
			}

			$data = [
				'uid' => 0,
				'type' => 1,
				'road_list' => json_encode($road_raw_list),
				'road_info' => json_encode([]),
				'road_long' => request('road_long'),
				'road_img' => request('road_img')
			];

			//all start
			$road_info = array();
			$weight_exhibit_ids = array();
			$weight_ex_quanzhong = array();
			foreach ($road_raw_list as $key => $exhibit_id) {
				$road_info[] = $exhibit_id . '_' . request('weight_' . $exhibit_id);
				$weight_exhibit_ids[$exhibit_id] = request('weight_' . $exhibit_id);
				$weight_ex_quanzhong[$exhibit_id] = request('weight_' . $exhibit_id);
			}
			$data['road_info'] = json_encode($road_info);

			arsort($weight_exhibit_ids);
			$data['weight_exhibit_ids'] = json_encode(array_keys($weight_exhibit_ids));

			$data['weight_exhibit_ids_all']= $this->weight_exhibit_ids_all($data['weight_exhibit_ids']);
			//all end

			$road1_data = $this->road_handle($road_raw_list1, $weight_ex_quanzhong);
			$road2_data = $this->road_handle($road_raw_list2, $weight_ex_quanzhong);
			$road3_data = $this->road_handle($road_raw_list3, $weight_ex_quanzhong);

			$data['road_list1'] = json_encode(request('road_exhibit_id1'));
			$data['road_list2'] = json_encode(request('road_exhibit_id2'));
			$data['road_list3'] = json_encode(request('road_exhibit_id3'));

			$data['road_info1'] = $road1_data['road_info'];
			$data['road_info2'] = $road2_data['road_info'];
			$data['road_info3'] = $road3_data['road_info'];

			$data['weight_exhibit_ids1'] = $road1_data['weight_exhibit_ids'];
			$data['weight_exhibit_ids2'] = $road2_data['weight_exhibit_ids'];
			$data['weight_exhibit_ids3'] = $road3_data['weight_exhibit_ids'];

			$data['weight_exhibit_ids1_all']= $this->weight_exhibit_ids_all($data['weight_exhibit_ids1']);
			$data['weight_exhibit_ids2_all']= $this->weight_exhibit_ids_all($data['weight_exhibit_ids2']);
			$data['weight_exhibit_ids3_all']= $this->weight_exhibit_ids_all($data['weight_exhibit_ids3']);

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

			//resource start
			if (config('exhibit_config.is_version_zip')) {
				$old_info['road_img'] = request('road_img_old');
				$new_info['road_img'] = request('road_img');
				if (request('road_img_old') !== request('road_img')) {
					ResourceDao::update_road_resource($new_info, $old_info, $road_id);
				}
			}
			//resource end

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
			//var_dump($exhibit_list);
			return view('admin.approad.edit', array(
				'info' => $info,
				'id' => $id,
				'exhibit_list' => $exhibit_list,
				'road_raw_info' => $road_raw_info
			));
		}
	}

	public function road_handle($road_raw_list, $weight_ex_quanzhong)
	{
		if (empty($road_raw_list)) {
			$data['road_info'] = '';
			$data['weight_exhibit_ids'] = '';
			return $data;
		} else {
			//node start
			$road_info = array();
			$weight_exhibit_ids = array();
			foreach ($road_raw_list as $key => $exhibit_id) {
				$road_info[] = $exhibit_id . '_' . $weight_ex_quanzhong[$exhibit_id];
				$weight_exhibit_ids[$exhibit_id] = $weight_ex_quanzhong[$exhibit_id];
			}
			$data['road_info'] = json_encode($road_info);

			arsort($weight_exhibit_ids);
			$data['weight_exhibit_ids'] = json_encode(array_keys($weight_exhibit_ids));
			//node end
			return $data;
		}
	}

	public function weight_exhibit_ids_all($weight_exhibit_ids)
	{

		if (empty($weight_exhibit_ids)) {
			return '';
		}else{
			$weight_exhibit_ids = json_decode($weight_exhibit_ids,true);
			$data=array();
			foreach ($weight_exhibit_ids as $exhibit_id) {
				$data[]=$exhibit_id;
				$auto_num=Exhibit::where('id',$exhibit_id)->value('auto_num');
				$ex_auto_num_counts= Exhibit::where('auto_num',$auto_num)->count();
				if($ex_auto_num_counts>1){
					$gl_temp= Exhibit::where('auto_num',$auto_num)->where('id','<>',$exhibit_id)->select('id as exhibit_id')->orderBy('exhibit.order_id', 'asc')->orderBy('exhibit.id', 'asc')->get();

					foreach ($gl_temp as $item) {
						$data[]= $item['exhibit_id'];
					}

				}
			}
			return json_encode($data);
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
