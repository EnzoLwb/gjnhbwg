<?php
/**
 * 服务信息路由
 */


Route::group([
	'prefix' => 'service',
	'namespace' => 'Service'
], function () {
	//场馆简介
	Route::group([
		'prefix' => 'intro',
	], function () {
		Route::get('/', 'IntroController@index')->name('admin.service.intro');
		Route::post('/save', 'IntroController@save')->name('admin.service.intro.save');
	});
	//参观指南
	Route::group([
		'prefix' => 'cgzn',
	], function () {
		Route::get('/', 'CgznController@index')->name('admin.service.cgzn');
		Route::post('/save', 'CgznController@save')->name('admin.service.cgzn.save');
		Route::get('/cgxz', 'CgznController@cgxz')->name('admin.service.cgzn.cgxz');
		Route::post('/cgxz_save', 'CgznController@cgxz_save')->name('admin.service.cgzn.cgxz_save');
		Route::get('/sbzl', 'CgznController@sbzl')->name('admin.service.cgzn.sbzl');
		Route::post('/sbzl_save', 'CgznController@sbzl_save')->name('admin.service.cgzn.sbzl_save');
	});

	//文创相关处理
	//文创系列
	Route::group([
		'prefix' => 'wenchuangxl',
	], function () {
		// 文创列表
		Route::get('/', 'WenchuangxlController@index')->name('admin.service.wenchuangxl');
		// 添加文创
		Route::get('/add', 'WenchuangxlController@add')->name('admin.service.wenchuangxl.add');
		// 编辑文创
		Route::get('/edit/{id}', 'WenchuangxlController@edit')->name('admin.service.wenchuangxl.edit');
		// 保存文创
		Route::post('/save', 'WenchuangxlController@save')->name('admin.service.wenchuangxl.save');
		// 删除文创
		Route::get('/delete/{id}', 'WenchuangxlController@delete')->name('admin.service.wenchuangxl.delete');
		Route::get('/unset_show/{id}', 'WenchuangxlController@unset_show')->name('admin.service.wenchuangxl.unset_show');
		Route::get('/set_show/{id}', 'WenchuangxlController@set_show')->name('admin.service.wenchuangxl.set_show');
	});
	//文创产品
	Route::group([
		'prefix' => 'wenchuang',
	], function () {
		// 文创列表
		Route::get('/', 'WenchuangController@index')->name('admin.service.wenchuang');
		// 添加文创
		Route::get('/add', 'WenchuangController@add')->name('admin.service.wenchuang.add');
		// 编辑文创
		Route::get('/edit/{id}', 'WenchuangController@edit')->name('admin.service.wenchuang.edit');
		// 保存文创
		Route::post('/save', 'WenchuangController@save')->name('admin.service.wenchuang.save');
		// 删除文创
		Route::get('/delete/{id}', 'WenchuangController@delete')->name('admin.service.wenchuang.delete');
		Route::get('/unset_show/{id}', 'WenchuangController@unset_show')->name('admin.service.wenchuang.unset_show');
		Route::get('/set_show/{id}', 'WenchuangController@set_show')->name('admin.service.wenchuang.set_show');
	});
	//宣教活动处理
	Route::group([
		'prefix' => 'xjhd',
	], function () {
		// 文创列表
		Route::get('/', 'XjhdController@index')->name('admin.service.xjhd');
		// 添加文创
		Route::get('/add', 'XjhdController@add')->name('admin.service.xjhd.add');
		// 编辑文创
		Route::get('/edit/{id}', 'XjhdController@edit')->name('admin.service.xjhd.edit');
		// 保存文创
		Route::post('/save', 'XjhdController@save')->name('admin.service.xjhd.save');
		// 删除文创
		Route::get('/delete/{id}', 'XjhdController@delete')->name('admin.service.xjhd.delete');
		Route::get('/unset_show/{id}', 'XjhdController@unset_show')->name('admin.service.xjhd.unset_show');
		Route::get('/set_show/{id}', 'XjhdController@set_show')->name('admin.service.xjhd.set_show');
	});
});