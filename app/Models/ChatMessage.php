<?php
namespace App\Models;

/**
 * 消息记录模型
 *
 * @author lxp 20160707
 */
class ChatMessage extends BaseMdl
{
	protected $primaryKey = 'message_id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'message_id'
	];
}
