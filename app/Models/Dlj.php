<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dlj extends Model
{
	protected $table = 'equipment';
	protected $primaryKey = 'device_no';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'device_no'
	];
}
