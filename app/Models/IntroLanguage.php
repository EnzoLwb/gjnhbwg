<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
 * 场馆简介多语种模型
 *
 * @author lxp 20160705
 */
class IntroLanguage extends BaseMdl
{
	protected $table = 'intro_language';
	protected $primaryKey = 'id';
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];

}
