<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BeforeMiddleware
{
	/**
	 * 前置中间件，运行于处理请求之前
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		/**
		 * 监听所有执行的sql，记录日志
		 * 过滤查询语句
		 *
		 * @author lxp 20170111
		 */
		DB::listen(function ($query) {
			if (preg_match('/^(insert|update|delete|replace)/i', $query->sql)) {
				$logObj = app('logext');
				$logObj->init('sql');
				$logObj->log("[{$query->time}]" . $query->sql, $query->bindings);
				if (Auth::check()) {
					$logObj->log("uid: " . Auth::user()->uid . " - username: " . Auth::user()->username);
				}
				$logObj->log("IP: " . app('request')->ip());
				$logObj->log("URL: " . request()->fullUrl());
				$logObj->log("PARAMS: ", request()->all());
				$logObj->end();
			}
			// 记录SELECT语句日志
			if (env('SQL_LOG_SELECT') && preg_match('/^select/i', $query->sql)) {
				$logObj = app('logext');
				$logObj->init('sql_select');
				$logObj->log("[{$query->time}]" . $query->sql, $query->bindings);
				$logObj->log("URL: " . request()->fullUrl());
				$logObj->log("PARAMS: ", request()->all());
				$logObj->end();
			}
		});

		return $next($request);
	}
}
