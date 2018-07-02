<?php
namespace App\Http\Controllers\Admin\Stat;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Exhibit;
use App\Models\ExhibitLanguage;
use App\Models\ExhibitLike;
use App\Models\ExhibitComment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 *展品热度统计控制器
 *
 * @author ljy
 * @package App\Http\Controllers\position
 */
class ExhibitHotController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}
	/*
	 * 展品热度统计
	 * @author ljy 20170804
	 *
	 */
	public function exhibit_hot(Request $request){
		$type = $request->input('type') ? $request->input('type') : "look_num";

		switch ($type) {
			case 'look_num':
				$info = Exhibit::join('exhibit_language','exhibit_language.exhibit_id','=','exhibit.id')->where('exhibit_language.language',1)->select('exhibit.look_num as num','exhibit_language.exhibit_name as exhibit_name')
					->orderBy('exhibit.id','desc')->paginate(parent::PERPAGE);
				$arr['title'] = '浏览统计';
				$arr['name'] = '浏览数量';
				break;

			case 'like_num':
				$info = Exhibit::join('exhibit_language','exhibit_language.exhibit_id','=','exhibit.id')->where('exhibit_language.language',1)->select('exhibit.like_num as num','exhibit_language.exhibit_name as exhibit_name')
					->orderBy('exhibit.id','desc')->paginate(parent::PERPAGE);
				$arr['title'] = '点赞统计';
				$arr['name'] = '点赞数量';
				break;
			case 'comment_num':
				$info = Exhibit::join('exhibit_language','exhibit_language.exhibit_id','=','exhibit.id')->where('exhibit_language.language',1)->select('exhibit.comment_num as num','exhibit_language.exhibit_name as exhibit_name')
					->orderBy('exhibit.id','desc')->paginate(parent::PERPAGE);
				$arr['title'] = '评论统计';
				$arr['name'] = '评论数量';
				break;
		}

		$info->appends(app('request')->all());

		return view('admin.stat.exhibit_hot',[
			'type'=>$type,
			'arr'=>$arr,
			'info'=>$info
		]);

	}
	/*
	 * 导出
	 * @author ljy 20170804
	 */
	public function export_exhibit_hot(Request $request){
		$type = $request->input('type') ? $request->input('type') :"look_num";
		switch ($type) {
			case 'look_num':
				$info = Exhibit::join('exhibit_language','exhibit_language.exhibit_id','=','exhibit.id')->where('exhibit_language.language',1)->select('exhibit.look_num as num','exhibit_language.exhibit_name as exhibit_name')
					->orderBy('exhibit.id','desc')->get();
				$title = '浏览统计';
				$name = '浏览数量';
				break;

			case 'like_num':
				$info = Exhibit::join('exhibit_language','exhibit_language.exhibit_id','=','exhibit.id')->where('exhibit_language.language',1)->select('exhibit.like_num as num','exhibit_language.exhibit_name as exhibit_name')
					->orderBy('exhibit.id','desc')->get();
				$title = '点赞统计';
				$name = '点赞数量';
				break;
		}


		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		//设置标题
		$objPHPExcel->getActiveSheet()->setCellValue('A1', $title);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:B1');//合并单元格
		$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);//设置高度
		//设置样式
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('宋体');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(30);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet()->setCellValue('A2', '展品名称');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', $name);
		$objPHPExcel->getActiveSheet()->getStyle('A2:B2')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A2:B2')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
		$objPHPExcel->getActiveSheet()->getStyle('A2:B2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$i = 3;
		foreach ($info as $g) {
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $g['exhibit_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $g['num']);
			$i = $i + 1;
		}
		$fileext = '.xlsx';
		$filename = $title . date('YmdHis') . rand(10, 99) . $fileext;
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		// 文件保存路径
		$path = preg_replace('(/+)', '/', storage_path('/tempdoc'));
		if (!is_dir($path)) {
			@mkdir($path, 0755, true);
		}
		$pathToFile = $path . '/' . substr(md5($filename), 0, 10) . date('YmdHis') . rand(1000, 9999) . $fileext;
		$objWriter->save($pathToFile);

		if (file_exists($pathToFile)) {
			$headers = [
				'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'Content-Length' => filesize($pathToFile),
				'Content-Disposition' => 'attachment; filename="' . $filename . '"'
			];
			return response()->download($pathToFile, $filename, $headers);
		} else {
			return response_json(0, [], '导出错误，请刷新页面后重试');
		}
	}

}