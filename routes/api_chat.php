<?php
// 需要登录验证的路由
Route::group([
//	'middleware' => 'gateway_rent'
], function () {
	//绑定用户
	Route::get('gateway/bind', 'GatewayController@bind');
	Route::get('gateway/getAllUid', 'GatewayController@getAllUid');//获得所有uid  测试用
	//返回到主页面(断开连接)/退还导览机
	Route::get('gateway/close_client', 'GatewayController@close_client');

	//创建群组
	Route::get('gateway/create_group', 'GatewayController@create_group');
	//加入群组
	Route::get('gateway/join_group', 'GatewayController@join_group');
	//退出群聊
	Route::get('gateway/exit_group', 'GatewayController@exit_group');
	//获取聊天记录
	Route::get('gateway/chat_message', 'GatewayController@chat_message');
	//获取当前位置
	Route::get('gateway/get_gps', 'GatewayController@get_gps');
	Route::group([
		'middleware' => 'gateway_rent'
	], function () {
		//私聊
		Route::post('gateway/send_msg', 'GatewayController@send_msg');
		//发送语音信息
		Route::post('gateway/upload_audio', 'GatewayController@upload_audio');
		//是否加入群组
		Route::get('gateway/getGroupList', 'GatewayController@getGroupList');
		//获取群组用户列表
		Route::get('gateway/users_list', 'GatewayController@users_list');
	});
});
