<?php
namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Support\Facades\DB;
use App\Models\Xjhd;

/**
 * 宣教活动控制器
 * @author ljy
 * @package App\Http\Controllers
 */
class XjhdController extends BaseAdminController
{

	//宣教活动列表
	public function index(){

		$query = Xjhd::orderBy('order_no','desc');
		// 筛选是否显示
		if (request('is_show')) {
			$query->where('is_show', request('is_show'));
		}
		// 筛选名称
		if (request('title')) {
			$query->where('title', 'LIKE','%'.request('title').'%');
		}
		// 筛选添加时间
		if (request('created_at_from')) {
			$query->where('active_start_date', '>=', request('created_at_from'));
		}
		if (request('created_at_to')) {
			$query->where('active_end_date', '<=', date('Y-m-d', strtotime(request('created_at_to') . " +1 day")));
		}

		$data  = $query->paginate(parent::PERPAGE);
		// 将查询参数拼接到分页链接中
		$data->appends(app('request')->all());

		return view('admin.service.xjhd_index',[
			'data'=>$data
		]);
	}
	//添加
	public function add(){
		return view('admin.service.xjhd_edit');

	}
	public function edit($id){
		$data = Xjhd::findOrFail($id);
		return view('admin.service.xjhd_edit',[
			'data'=>$data,
			'id'=>$id
		]);
	}
	public function save(){
		$id = request('id',0);
		// 验证
		$this->validate(request(), [
			'title' => 'required|unique:xjhd,title,' . $id . ',id',
			'order_no' => 'required|integer',
		]);

		$xl = Xjhd::findOrNew($id);
		$xl->img = request('img');
		$xl->title = request('title');
		$xl->title_1 = request('title_1');
		$xl->active_place = request('active_place');
		$xl->active_start_date = request('active_start_date');
		$xl->active_end_date = request('active_end_date');
		$xl->active_price = request('active_price');
		$xl->active_time = request('active_time');
		$xl->content = request('content');
		$xl->is_show = request('is_show');
		$xl->order_no = request('order_no');
		$xl->save();
		return $this->success(get_session_url('index'));
	}
	public function delete($id){
		if (request()->ajax()) {
			Xjhd::where('id',$id)->delete();
			return $this->success(get_session_url('index'));
		}

	}
	public function set_show($id){
		if (request()->ajax()) {
			Xjhd::where('id',$id)->update(['is_show'=>1]);
			return $this->success(get_session_url('index'));
		}

	}
	public function unset_show($id){
		if (request()->ajax()) {
			Xjhd::where('id',$id)->update(['is_show'=>2]);
			return $this->success(get_session_url('index'));
		}

	}

}
