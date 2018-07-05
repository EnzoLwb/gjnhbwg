<?php
//统计管理
	Route::group([
		'prefix' => 'stat',
		'namespace' => 'Stat'
	], function () {
		Route::group([
			'prefix' => 'stat',
		], function () {
			// 展品热度统计
			Route::get('/exhibit_hot', 'ExhibitHotController@exhibit_hot')->name('admin.stat.stat.exhibit_hot');
			//导出热度统计
			Route::get('/export_exhibit_hot', 'ExhibitHotController@export_exhibit_hot')->name('admin.stat.stat.export_exhibit_hot');

		});

	});
