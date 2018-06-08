<?php
Route::group([
	'prefix' => 'approad',
	'namespace' => 'AppRoad'
], function () {
	//路线列表
	Route::get('/road_list', 'AppRoadController@road_list')->name('admin.approad.road_list');
	// 路线编辑
	Route::match([
		'get',
		'post'
	],'/edit/{id}', 'AppRoadController@edit')->name('admin.approad.edit');
	// 路线删除
	Route::get('/delete/{id}', 'AppRoadController@delete')->name('admin.approad.delete');
});
