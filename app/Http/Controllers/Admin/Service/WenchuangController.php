<?php
namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Support\Facades\DB;
use App\Models\WcXl;
use App\Models\WcProduct;

/**
 * 文创产品控制器
 * @author ljy
 * @package App\Http\Controllers
 */
class WenchuangController extends BaseAdminController
{

	//文创产品列表
	public function index(){
		$xl = WcXl::where('is_show',1)->orderBy('order_no','desc')->select('title','id')->get();
		$query = WcProduct::leftJoin('wc_xl','wc_product.xl_id','wc_xl.id')->select('wc_product.id','wc_xl.title','wc_product.pro_title','wc_product.pro_img','wc_product.is_show')
		->orderBy('wc_product.order_no','desc');
		// 筛选是否显示
		if (request('is_show')) {
			$query->where('wc_product.is_show', request('is_show'));
		}
		// 筛选名称
		if (request('title')) {
			$query->where('wc_product.title', 'LIKE','%'.request('title').'%');
		}
		// 筛选系列
		if (request('xl_id')) {
			$query->where('wc_product.xl_id', request('xl_id'));
		}

		$data  = $query->paginate(parent::PERPAGE);
		// 将查询参数拼接到分页链接中
		$data->appends(app('request')->all());

		return view('admin.service.wc.pro_index',[
			'data'=>$data,
			'xl'=>$xl
		]);
	}
	//添加
	public function add(){
		$xl = WcXl::where('is_show',1)->orderBy('order_no','desc')->select('title','id')->get();
		return view('admin.service.wc.pro_edit',[
			'xl'=>$xl
		]);

	}
	public function edit($id){
		$data = WcProduct::findOrFail($id);
		$xl = WcXl::where('is_show',1)->orderBy('order_no','desc')->select('title','id')->get();
		return view('admin.service.wc.pro_edit',[
			'data'=>$data,
			'xl'=>$xl,
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

		$xl = WcProduct::findOrNew($id);
		$xl->xl_id = request('xl_id');
		$xl->pro_img = request('img');
		$xl->pro_title = request('title');
		$xl->pro_content = request('content');
		$xl->is_show = request('is_show');
		$xl->order_no = request('order_no');
		$xl->save();
		return $this->success(get_session_url('index'));
	}
	public function delete($id){
		if (request()->ajax()) {
			WcProduct::where('id',$id)->delete();
			return $this->success(get_session_url('index'));
		}

	}
	public function set_show($id){
		if (request()->ajax()) {
			WcProduct::where('id',$id)->update(['is_show'=>1]);
			return $this->success(get_session_url('index'));
		}

	}
	public function unset_show($id){
		if (request()->ajax()) {
			WcProduct::where('id',$id)->update(['is_show'=>2]);
			return $this->success(get_session_url('index'));
		}

	}

}
