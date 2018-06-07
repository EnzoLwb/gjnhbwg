<?php
namespace App\Models;

/**
 * 辅助点关联路线
 *
 * @author yyj 20171117
 */
class NavigationRoad extends BaseMdl
{
	protected $table = 'navigation_road';
	protected $primaryKey = 'id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
	
}
