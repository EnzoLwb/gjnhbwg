<?php
namespace App\Models;

/**
 * 上传文件类型模型
 *
 * @author lxp 20160712
 */
class Migrations extends BaseMdl {
	protected $primaryKey = 'id';
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
}
