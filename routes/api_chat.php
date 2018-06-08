<?php
// 需要登录验证的路由
Route::group([
//	'middleware' => 'auth:api'
], function () {
	//绑定用户
	Route::get('gateway/bind', 'GatewayController@bind');
	//绑定用户
	Route::get('gateway/device_info', 'GatewayController@device_info');
	//创建群组
	Route::get('gateway/create_group', 'GatewayController@create_group');
	//加入群组
	Route::get('gateway/join_group', 'GatewayController@join_group');
});
