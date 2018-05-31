<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Admin\BaseAdminController;
use Psy\VarDumper\Dumper;

/**
 * 系统日志控制器
 *
 * @package App\Http\Controllers\Admin\Setting
 */
class SystemlogController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$logpath = storage_path('logs');
		$dirhandler = opendir($logpath);
		$basedirlist = [];
		while (($dir = readdir($dirhandler)) !== false) {
			if ($dir != '..' && $dir != '.') {
				$dirpath = $logpath . '/' . $dir;
				if (is_dir($dirpath)) {
					array_push($basedirlist, [
						'name' => $dir,
						'path' => $dirpath,
						'type' => 'dir'
					]);
				} elseif (file_exists($dirpath) && pathinfo($dirpath)['extension'] == 'log') {
					array_push($basedirlist, [
						'name' => $dir,
						'path' => $dirpath,
						'type' => 'file'
					]);
				}
			}
		}
		array_multisort(array_column($basedirlist, 'name'), SORT_ASC, $basedirlist);

		return view('admin.setting.systemlog', ['dirlist' => $basedirlist]);
	}

	public function getdir()
	{
		$path = request('path');
		$list = [];
		if (is_dir($path)) {
			$dirhandler = opendir($path);
			while (($dir = readdir($dirhandler)) !== false) {
				if ($dir != '..' && $dir != '.') {
					$dirpath = $path . '/' . $dir;
					if (is_dir($dirpath)) {
						array_push($list, [
							'name' => $dir,
							'path' => $dirpath,
							'type' => 'dir'
						]);
					} elseif (file_exists($dirpath) && pathinfo($dirpath)['extension'] == 'log') {
						array_push($list, [
							'name' => $dir,
							'path' => $dirpath,
							'type' => 'file'
						]);
					}
				}
			}
		}
		array_multisort(array_column($list, 'name'), SORT_ASC, $list);

		return response()->json($list);
	}

	/**
	 * view
	 *
	 * @author lxp
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|void
	 */
	public function view()
	{
		$filepath = request('path');
		if (!file_exists($filepath)) {
			return $this->error('文件不存在');
		}

		// 起始行数
		$start_line = request('start_line', 1);
		// 终止行数
		$end_line = request('end_line', 20);

		$forward_line = request('forward_line', 20);
		$backward_line = request('backward_line', 20);

		$filecontents = [];
		// 只读方式打开文件
		$fp = fopen($filepath, 'r');
		$line = 0;
		while (!feof($fp)) {
			$line++;
			if ($line >= $start_line && $line <= $end_line) {
				// 在要求行数内取得数据
				$filecontents[] = trim(fgets($fp));
			} else {
				fgets($fp);
			}
		}

		// 倒序查询
		//		fseek($fp, -1, SEEK_END);
		//		$n = 5;
		//		while ($n) {
		//			$c = fgetc($fp);
		//			switch ($c) {
		//				case "\r":
		//				case "\n":
		//					$fc = fgets($fp);
		//					if (trim($fc)) {
		//						$filecontents[] = trim($fc);
		//						fseek($fp, 0 - strlen($fc), SEEK_CUR);
		//						$n--;
		//					}
		//					break;
		//			}
		//			fseek($fp, -2, SEEK_CUR);
		//		}
		fclose($fp);

		if (isset($filecontents[0]) && is_json($filecontents[0])) {
			echo <<<EOF
<script src="/js/jquery-1.12.4.min.js"></script>
<form method="get" id="lineform">
路径：$filepath
<br/>
行数：$line
<br/>
查询行数：<input type="text" name="start_line" id="start_line" placeholder="起始行数" value="$start_line"> -
<input type="text" name="end_line" id="end_line" placeholder="结束行数" value="$end_line">
<input type="hidden" name="path" value="$filepath">
<input type="submit" value="查询">
<br/>
向后<input type="text" name="forward_line" id="forward_line" value="$forward_line" size="5">行<input type="button" value="go" onclick="$('#start_line').val(parseInt($('#start_line').val())+parseInt($('#forward_line').val()));$('#end_line').val(parseInt($('#end_line').val())+parseInt($('#forward_line').val()));$('#lineform').submit();">
<br/>
向前<input type="text" name="backward_line" id="backward_line" value="$backward_line" size="5">行<input type="button" value="go" onclick="$('#start_line').val(parseInt($('#start_line').val())+parseInt($('#backward_line').val()));$('#end_line').val(parseInt($('#end_line').val())+parseInt($('#backward_line').val()));$('#lineform').submit();">
</form>
EOF;

			foreach ($filecontents as $v) {
				if ($v) {
					(new \Illuminate\Support\Debug\Dumper())->dump(json_decode($v));
				}
			}
			return;
		} else {
			return view('admin.setting.systemlog_view', [
				'filepath' => $filepath,
				'filecontents' => $filecontents,
			]);
		}
	}
}