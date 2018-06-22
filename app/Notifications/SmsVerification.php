<?php

namespace App\Notifications;

use App\Channels\AlidySmsChannel;
use App\Channels\HnsbSmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 短信验证码通知，已使用队列
 *
 * @author lxp 20170811
 * @package App\Notifications
 */
class SmsVerification extends Notification implements ShouldQueue
{
	use Queueable;

	/**
	 * SmsVerification constructor.
	 *
	 */
	public function __construct()
	{
	}

	/**
	 * 返回通知频道
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		// 添加短信通知频道
		//$via = [HnsbSmsChannel::class];
		$via = [AlidySmsChannel::class];

		return $via;
	}

	/**
	 * 短信内容
	 *
	 * @param $notifiable
	 * @return string
	 */
	public function toSmscode($notifiable)
	{
		return "{$notifiable->smscode}";
	}

}
