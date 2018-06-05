<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManningList extends Model
{
	protected $table = 'manning_list';
	protected $primaryKey = 'type_id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'type_id'
	];
}
