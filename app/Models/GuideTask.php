<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuideTask extends Model
{
	protected $table = 'guide_task';
	protected $primaryKey = 'task_id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'task_id'
	];
}
