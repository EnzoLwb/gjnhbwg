<?php
// 需要登录验证的路由
Route::group([
//	'middleware' => 'auth:api'
], function () {
	//加入群组
	Route::get('chat/create_group', 'GatewayController@create_group');
	//预约信息详情

});
