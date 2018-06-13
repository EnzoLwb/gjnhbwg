<?php
//服务设施点位获取
Route::get('map_service_point', 'ServicePointController@map_service_point');
//服务设施列表
Route::get('service_point_list', 'ServicePointController@service_point_list');
//服务设施查询
Route::get('service_point_search', 'ServicePointController@service_point_search');

