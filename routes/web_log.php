<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8
 * Time: 15:10
 */
Route::group([
	'prefix' => 'log',
	'namespace' => 'Log'
], function () {
	// 用户登录日志
	Route::get('/login_log', 'LoginLogController@login_log')->name('admin.log.login_log');
});