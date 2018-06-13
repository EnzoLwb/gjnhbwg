<?php
namespace App\Http\Controllers\Admin\Service;

use App\Dao\ResourceDao;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\CgznJt;
use App\Models\CgznZl;
use App\Models\CgznXz;
use Illuminate\Support\Facades\DB;

/**
 * 参观指南控制器
 * @author ljy
 * @package App\Http\Controllers
 */
class CgznController extends BaseAdminController
{

	//交通周边
	public function index(){
		$data=[];
		$language_info = CgznJt::get()->toArray();
		foreach ($language_info as $k => $g) {
			$data['language'][$g['language_id']] = $g;
		}
		return view('admin.service.jtzb',[
			'data'=>$data
		]);
	}
	public function save(){
		DB::table('cgzn_jt')->truncate();
		//语种信息入库
		foreach (config('language') as $k => $g) {

			//展览名称不为空就写入数据
			if (!empty(request('jiaotong_' . $k))) {
				$data2 = [
					'jiaotong' => request('jiaotong_' . $k),
					'contact' => request('contact_' . $k),
					'language_id' => $k
				];
				CgznJt::insert($data2);
			}
		}
		return $this->success(get_session_url('index'));
	}
	//参观须知
	public function cgxz(){

		$language_info = CgznXz::get()->toArray();
		$data=[];
		foreach ($language_info as $k => $g) {
			$data['language'][$g['language_id']] = $g;
		}
		return view('admin.service.cgxz',[
			'data'=>$data
		]);
	}
	public function cgxz_save(){
		DB::table('cgzn_xz')->truncate();
		//语种信息入库
		foreach (config('language') as $k => $g) {

			//展览名称不为空就写入数据
			if (!empty(request('shuoming_' . $k))) {
				$data2 = [
					'shuoming' => request('shuoming_' . $k),
					'zysx' => request('zysx_' . $k),
					'language_id' => $k
				];
				CgznXz::insert($data2);
			}
		}
		return $this->success(get_session_url('index'));
	}
	//设备租赁
	public function sbzl(){
		$data=[];
		$language_info = CgznZl::get()->toArray();
		foreach ($language_info as $k => $g) {
			$data['language'][$g['language_id']] = $g;
		}
		return view('admin.service.sbzl',[
			'data'=>$data
		]);
	}
	public function sbzl_save(){
		DB::table('cgzn_zl')->truncate();
		//语种信息入库
		foreach (config('language') as $k => $g) {

			//展览名称不为空就写入数据
			if (!empty(request('step1_' . $k))) {
				$data2 = [
					'step1' => request('step1_' . $k),
					'step2' => request('step2_' . $k),
					'step3' => request('step3_' . $k),
					'step4' => request('step4_' . $k),
					'step5' => request('step5_' . $k),
					'language_id' => $k
				];
				CgznZl::insert($data2);
			}
		}
		return $this->success(get_session_url('index'));
	}
}
