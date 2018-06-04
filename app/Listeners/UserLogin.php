<?php

namespace App\Listeners;

use App\Models\LoginLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;

class UserLogin
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * handle
	 *
	 * @author lxp 20170615
	 * @param Login $event
	 */
	public function handle(Login $event)
	{
		// 可区分前后台
		//		switch (Auth::getDefaultDriver()){
		//			case 'web':
		//				break;
		//			case 'admin':
		//				break;
		//		}

		// 更新最后登录时间和IP
		$event->user->last_login = date('Y-m-d H:i:s');
		$event->user->lastloginip = app('request')->ip();
		$event->user->save();
		//记录登录日志
		$user_info=Auth::user();
		if(Auth::getDefaultDriver()=='admin'){
			$type=1;
		}
		else{
			$type=2;
		}
		LoginLog::create([
			'uid'=>$user_info->uid,
			'ip'=>$user_info->lastloginip,
			'content'=>'用户'.$user_info->username.$user_info->last_login.'登录了系统后台，登录ip为'.$user_info->lastloginip,
			'type'=>$type,
		]);
	}
}
