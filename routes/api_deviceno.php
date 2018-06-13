<?php

//机器号请求接口
Route::get('request_deviceno', 'DevicenoController@request_deviceno');
//定位上传接口
Route::post('positions', 'DevicenoController@positions');
