<?php
// 需要登录验证的路由
Route::group([
//	'middleware' => 'auth:api'
], function () {
	//绑定用户
	Route::get('gateway/bind', 'GatewayController@bind');
	//返回到主页面(断开连接)/退还导览机
	Route::get('gateway/close_client', 'GatewayController@close_client');
	//创建群组
	Route::get('gateway/create_group', 'GatewayController@create_group');
	//加入群组
	Route::get('gateway/join_group', 'GatewayController@join_group');
	//私聊
	Route::post('gateway/send_msg', 'GatewayController@send_msg');
});
