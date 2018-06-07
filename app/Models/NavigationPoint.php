<?php
namespace App\Models;

/**
 * 导航辅助点
 *
 * @author yyj 20171117
 */
class NavigationPoint extends BaseMdl
{
	protected $table = 'navigation_point';
	protected $primaryKey = 'id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
	
}
