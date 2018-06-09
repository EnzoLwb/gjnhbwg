<?php
namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Intro;
use App\Models\IntroLanguage;

/**
 * 场馆简介控制器
 * @author ljy
 * @package App\Http\Controllers
 */
class IntroController extends BaseAdminController
{

	public function index(){
		$data = Intro::first()->toArray();
		$language_info = IntroLanguage::where('intro_id',$data['id'])->get()->toArray();
		foreach ($language_info as $k => $g) {
			$data['language'][$g['language_id']] = $g;
		}
		if($data){
			$data['extfile_img'] = [];
			if ($data['imgs']) {
				$data['extfile_img'] = explode(',', $data['imgs']);
			}
			$data['d_extfile_img'] = [];
			if ($data['d_imgs']) {
				$data['d_extfile_img'] = explode(',', $data['d_imgs']);
			}
		}

		return view('admin.service.intro',[
			'data'=>$data
		]);
	}
	public function save(){
		$id = request('id', 0);
		$intro = Intro::findOrNew($id);
		if(request('imgs')){
			$intro->imgs = request('imgs');
		}else{
			return $this->error('请上传app场馆简介图片');
		}
		if(request('d_imgs')){
			$intro->d_imgs = request('d_imgs');
		}else{
			return $this->error('请上传导览机场馆简介图片');
		}
		$intro->add_time =date("Y-m-d H:i:s",time());
		$intro->save();
		IntroLanguage::where('intro_id',$intro->id)->delete();
		//语种信息入库
		foreach (config('language') as $k => $g) {

			//展览名称不为空就写入数据
			if (!empty(request('title_' . $k))) {
				$data2 = [
					'intro_id' => $intro->id,
					'title' => request('title_' . $k),
					'content' => request('content_' . $k),
					'language_id' => $k
				];

				IntroLanguage::create($data2);
			}
		}


		return $this->success(get_session_url('index'));
	}
}
