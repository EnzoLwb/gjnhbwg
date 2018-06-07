<?php
Route::group([
	'prefix' => 'navigation',
	'namespace' => 'Navigation'
], function () {
	// 地图列表显示
	Route::get('/show/{id?}', 'NavigationController@show')->name('admin.navigation.show');
	// 地图点添加
	Route::post('/add_point', 'NavigationController@add_point')->name('admin.navigation.add_point');
	// 地图点信息获取
	Route::post('/ajax_map2', 'NavigationController@ajax_map2')->name('admin.navigation.ajax_map2');
	// 地图点编辑
	Route::get('/edit_point/{map_id}/{auto_num}', 'NavigationController@edit_point')->name('admin.navigation.edit_point');
	// 地图点编辑保存
	Route::post('/edit_point_save', 'NavigationController@edit_point_save')->name('admin.navigation.edit_point_save');
	// 地图点关联编辑
	Route::get('/edit_navigation/{map_id}/{auto_num}', 'NavigationController@edit_navigation')->name('admin.navigation.edit_navigation');
	// 地图点关联编辑保存
	Route::post('/edit_navigation_save', 'NavigationController@edit_navigation_save')->name('admin.navigation.edit_navigation_save');
	Route::post('/ajax_axis', 'NavigationController@ajax_axis')->name('admin.navigation.ajax_axis');
});
