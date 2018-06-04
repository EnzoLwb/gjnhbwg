<?php

namespace App\Dao\Load;

use App\Models\Migrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\UploadedType;

/**
 * 随手拍模块安装模型
 *
 * @author yyj 20171213
 */
class LoadDao
{

	//数据库卸载
	protected static function drop_table($arr)
	{
		foreach ($arr as $k => $g) {
			Schema::dropIfExists($g['tableName']);
			if (env('DB_CONNECTION') == 'oracle') {
				$sequence = DB::getSequence();
				$sequence->drop(strtoupper($g['tableName'] . '_' . $g['primaryKey'] . '_SEQ'));
			}
			Migrations::where('migration','like','%000000_create_'.$g['tableName'].'_table')->delete();
		}
	}

	/**
	 * 删除目录
	 * @param string $dir 目录地址
	 * @param int $is_del 是否删除当前文件夹 1删除 0不删除
	 * @author yyj 20161011
	 */
	public static function deldir($dir,$is_del=1) {
		if(file_exists($dir)){
			//先删除目录下的文件：
			$dh=opendir($dir);
			while ($file=readdir($dh)) {
				if($file!="." && $file!="..") {
					$fullpath=$dir."/".$file;
					if(!is_dir($fullpath)) {
						unlink($fullpath);
					} else {
						self::deldir($fullpath);
					}
				}
			}
			closedir($dh);
			if($is_del==1){
				//删除当前文件夹：
				rmdir($dir);
			}
		}
	}

	/**
	 * 文件检测
	 * @param  array $arr 模块属性
	 * @param array $load_file_list 基础安装列表文件
	 * @param array $install_file_list 安装列表文件
	 * @return array
	 * @author yyj 20180303
	 */
	protected static function check_file($arr,$load_file_list,$install_file_list){
		$load_file_num = 0;
		$file_num = 0;
		$load_file_des = '';
		$file_des = '';
		foreach ($load_file_list as $k => $g) {
			$load_file_num = $load_file_num + 1;
			if (!file_exists($g)) {
				$load_file_des = $load_file_des . '基础安装文件'.$g . '缺失<br />';
			} else {
				if (!file_exists($install_file_list[$k])) {
					$file_des = $file_des . '功能模块文件'.$install_file_list[$k] . '缺失<br />';
				} else {
					if(md5(file_get_contents($g))!==md5(file_get_contents($install_file_list[$k]))){
						$arr['status'] = 4;
						$file_des = $file_des .'功能模块文件'. $install_file_list[$k] . '与基础安装文件不符<br />';
					}
					else{
						$file_num = $file_num + 1;
					}
				}
			}
		}

		if (!empty($load_file_des)) {
			$arr['status'] = 2;
			$arr['des'] = $load_file_des;
		} elseif ($file_num == 0) {
			$arr['status'] = 0;
		} elseif ($file_num != $load_file_num&&$arr['status']==0) {
			$arr['status'] = 3;
			$arr['des'] = $file_des;
		} elseif ($file_num != $load_file_num&&$arr['status']!=0) {
			$arr['des'] = $file_des;
		}
		else{
			$arr['status'] = 1;
			$arr['des'] = '';
		}
		return $arr;
	}
}
