<?php
// 需要登录验证的路由
Route::group([
	'middleware' => 'auth:api'
], function () {
	//我的预约
	Route::get('my_reserve_list', 'ReserveController@my_reserve_list');
	//预约信息详情
	Route::get('reserve_detail', 'ReserveController@reserve_detail');
	//个人讲解预约提交
	Route::post('personal_reserve', 'ReserveController@personal_reserve');
	//团队讲解预约提交
	Route::post('team_reserve', 'ReserveController@team_reserve');
	//取消预约
	Route::get('cancel_reserve', 'ReserveController@cancel_reserve');
	//讲解评价提交
	Route::post('explain_evaluate', 'ReserveController@explain_evaluate');
	//证件类型
	Route::get('certificate_type_list', 'ReserveController@certificate_type_list');
	//参观时间
	Route::get('visit_time_list', 'ReserveController@visit_time_list');
	//人员构成
	Route::get('manning_list', 'ReserveController@manning_list');
});
