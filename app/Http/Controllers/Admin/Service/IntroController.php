<?php
namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Intro;

/**
 * 场馆简介控制器
 * @author ljy
 * @package App\Http\Controllers
 */
class IntroController extends BaseAdminController
{

	public function index(){
		$data = Intro::first();
		if($data){
			$data->extfile_img = [];
			if ($data->imgs) {
				$data->extfile_img = explode(',', $data->imgs);
			}
			$data->d_extfile_img = [];
			if ($data->d_imgs) {
				$data->d_extfile_img = explode(',', $data->d_imgs);
			}
		}

		return view('admin.service.intro',[
			'data'=>$data
		]);
	}
	public function save(){
		$id = request('id', 0);
		$visit = Intro::findOrNew($id);
		if(request('imgs')){
			$visit->imgs = request('imgs');
		}else{
			return $this->error('请上传app场馆简介图片');
		}
		if(request('d_imgs')){
			$visit->d_imgs = request('d_imgs');
		}else{
			return $this->error('请上传导览机场馆简介图片');
		}


		$visit->title = request('title');
		$visit->content = request('content');
		$visit->add_time =date("Y-m-d H:i:s",time());
		$visit->save();
		return $this->success(get_session_url('index'));
	}
}
