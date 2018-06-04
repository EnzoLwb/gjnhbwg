<?php

namespace App\Http\Controllers\Admin\SvgMapAdmin;

use App\Models\SvgMapTable;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BaseAdminController;

class SvgMapAdminController extends BaseAdminController
{
	//地图名称是否多语种
	private $is_more_language = false;

	//地图名称
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 地图信息列表
	 *
	 * @author yyj 20171026
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function map_list()
	{
		$floor_info = config('floor');
		// 处理排序
		$map_list = SvgMapTable::orderBy('id', 'desc')->get();
		return view('admin.svgmap.svgmap_list', [
			'map_list' => $map_list,
			'floor_info' => $floor_info,
			'floor_arr' => config('floor')
		]);
	}

	/**
	 * 地图编辑
	 *
	 * @author yyj 20171026
	 * @param  int $id 地图id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit($id)
	{
		if (request()->isMethod('post')) {
			$this->validate(request(), [
				'map_path' => 'required',
				'map_name' => 'required|max:20',
				'width' => 'required|numeric',
				'height' => 'required|numeric'
			]);
			$map_name_json[1] = request('map_name');
			foreach (config('language') as $k => $g) {
				if ($k != 1) {
					if ($this->is_more_language) {
						$map_name_json[$k] = request('map_name_' . $g['dir']);
					} else {
						$map_name_json[$k] = '';
					}
				}
			}
			$data = [
				'map_name' => request('map_name'),
				'map_path' => request('map_path'),
				'width' => request('width'),
				'height' => request('height'),
				'floor_id' => request('floor_id'),
				'map_name_json' => json_encode($map_name_json)
			];
			if ($id == 'add') {
				SvgMapTable::create($data);

			} else {
				SvgMapTable::where('id', $id)->update($data);
			}
			return $this->success(get_session_url('map_list'));
		} else {
			if ($id != 'add') {
				$map = SvgMapTable::where('id', $id)->first()->toArray();
			} else {
				$map['id'] = 'add';
			}
			return view('admin.svgmap.svgmap_edit', array(
				'map' => $map,
				'floor_arr' => config('floor'),
				'is_more_language' => $this->is_more_language
			));
		}
	}

	/**
	 * 删除地图信息
	 *
	 * @author yyj 20171026
	 * @param  int $id 地图id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function delete($id)
	{

		//判断是否被使用
		/*$is_use=Exhibit::where('map_id',$id)->count();
		if(!empty($is_use)){
			return $this->error('地图正在使用中，请先删除相关展品');
		}*/
		SvgMapTable::where('id', $id)->delete();
		return $this->success(get_session_url('index'));
	}

	/**
	 * 地图信息预览
	 *
	 * @author yyj 20171026
	 * @param  int $id 地图id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function view($id)
	{
		$info = SvgMapTable::where('id', $id)->first();
		return view('admin.svgmap.svgmap_view', array('map' => $info));
	}
}
