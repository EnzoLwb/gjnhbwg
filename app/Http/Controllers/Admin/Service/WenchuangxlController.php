<?php
namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\WcProduct;
use App\Models\WcXl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
/**
 * 文创系列控制器
 * @author ljy
 * @package App\Http\Controllers
 */
class WenchuangxlController extends BaseAdminController
{

	public function index(){
		$query = WcXl::OrderBy('order_no','desc');
		// 筛选是否显示
		if (request('is_show')) {
			$query->where('is_show', request('is_show'));
		}
		// 筛选名称
		if (request('title')) {
			$query->where('title', 'LIKE','%'.request('title').'%');
		}
		$data  = $query->paginate(parent::PERPAGE);
		// 将查询参数拼接到分页链接中
		$data->appends(app('request')->all());
		return view('admin.service.wc.xl_index',[
			'data'=>$data
		]);
	}
	public function add(){
		return view('admin.service.wc.xl_edit');
	}
	public function edit($id){
		$xl = WcXl::findOrFail($id);
		return view('admin.service.wc.xl_edit',[
			'data'=>$xl,
			'id'=>$id
		]);
	}
	public function save(){
		$id = request('id',0);
		// 验证
		$this->validate(request(), [
			'title' => 'required|unique:wc_xl,title,' . $id . ',id',
			'order_no' => 'required|integer',
		]);

		$xl = WcXl::findOrNew($id);
		$xl->img = request('img');
		$xl->img_1 = request('img_1');
		$xl->title = request('title');
		$xl->content = request('content');
		$xl->is_show = request('is_show');
		$xl->order_no = request('order_no');
		$xl->save();
		return $this->success(get_session_url('index'));
	}
	public function delete($id){
		if (request()->ajax()) {
			WcXl::destroy($id);
			WcProduct::where('xl_id',$id)->delete();
			return $this->success(get_session_url('index'));
		}

	}
	public function set_show($id){
		if (request()->ajax()) {
			WcXl::where('id',$id)->update(['is_show'=>1]);
			return $this->success(get_session_url('index'));
		}

	}
	public function unset_show($id){
		if (request()->ajax()) {
			WcXl::where('id',$id)->update(['is_show'=>2]);
			return $this->success(get_session_url('index'));
		}

	}


}
