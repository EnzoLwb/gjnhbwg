<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuideEvaluate extends Model
{
	protected $table = 'guide_evaluate';
	protected $primaryKey = 'eva_id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'eva_id'
	];
}
