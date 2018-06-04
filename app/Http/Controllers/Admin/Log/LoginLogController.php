<?php

namespace App\Http\Controllers\Admin\Log;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Support\Facades\DB;
use App\Models\LoginLog;
use App\Models\AdminUsers;
use Illuminate\Support\Facades\Auth;

class LoginLogController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 登录日志列表
	 *
	 * @author yyj 20171025
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function login_log()
	{
		// 处理排序
		$query = LoginLog::orderBy('id', 'desc');
		$query->where('login_log.type', 1);
		if(Auth::id()!=1){
			$query->where('login_log.uid','<>', 1);
		}
		// 筛选登录时间
		if (request('created_at_from')) {
			$query->where('login_log.created_at', '>=', request('created_at_from'));
		}
		if (request('created_at_to')) {
			$query->where('login_log.created_at', '<=', date('Y-m-d', strtotime(request('created_at_to') . " +1 day")));
		}
		// 筛选用户名
		if (request('username')) {
			$query->where(function($query){
				$query->where('admin_users.username', 'LIKE', "%" . request('username') . "%")->orWhere('admin_users.nickname', 'LIKE', "%" . request('username') . "%");
			});
		}
		// 取得列表
		$users = $query->rightJoin('admin_users', 'admin_users.uid', '=', 'login_log.uid')->select('login_log.*', 'admin_users.username', 'admin_users.nickname', 'admin_users.email')->paginate(parent::PERPAGE);
		// 将查询参数拼接到分页链接中
		$users->appends(app('request')->all());

		return view('admin.log.login_log', [
			'users' => $users
		]);
	}

	

}
