<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
	protected $table = 'group';
	protected $primaryKey = 'group_id';
	public $timestamps = false;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'group_id'
	];
}
