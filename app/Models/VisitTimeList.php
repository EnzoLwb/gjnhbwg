<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitTimeList extends Model
{
	protected $table = 'visit_time_list';
	protected $primaryKey = 'time_slot_id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'time_slot_id'
	];
}
