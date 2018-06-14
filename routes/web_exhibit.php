<?php
/**
 * 数据管理路由
 */
Route::group([
	'prefix' => 'data',
	'namespace' => 'Data'
], function () {
	//展览管理
	Route::group([
		'prefix' => 'exhibition',
	], function () {
		// 展厅列表
		Route::get('/', 'ExhibitionController@exhibition_list')->name('admin.data.exhibition');
		// 展厅编辑
		Route::match([
			'get',
			'post'
		], '/edit/{id}', 'ExhibitionController@edit')->name('admin.data.exhibition.edit');
		// 展厅删除
		Route::get('/delete/{id}', 'ExhibitionController@delete')->name('admin.data.exhibition.delete');
		// 设为轮播
		Route::get('/set_lb/{id}', 'ExhibitionController@set_lb')->name('admin.data.exhibition.set_lb');
		// 取消轮播
		Route::get('/unset_lb/{id}', 'ExhibitionController@unset_lb')->name('admin.data.exhibition.unset_lb');
		// 展厅评论审核列表
		Route::get('/exhibition_comment_list', 'ExhibitionController@exhibition_comment_list')->name('admin.data.exhibition.exhibition_comment_list');
		// 通过审核
		Route::get('/pass_check/{type}/{ids}', 'ExhibitionController@pass_check')->name('admin.data.exhibition.pass_check');
		// 审核不通过
		Route::get('/unpass_check/{type}/{ids}', 'ExhibitionController@unpass_check')->name('admin.data.exhibition.unpass_check');
		// 删除
		Route::get('/del_check/{type}/{ids}', 'ExhibitionController@del_check')->name('admin.data.exhibition.del_check');
		//添加学习单题目
		Route::get('/add_learn/{id?}', 'ExhibitionController@add_learn')->name('admin.data.exhibition.add_learn');
		Route::post('/save_learn', 'ExhibitionController@save_learn')->name('admin.data.exhibition.save_learn');
		Route::post('/del_learn', 'ExhibitionController@del_learn')->name('admin.data.exhibition.del_learn');
	});

	//展品管理
	Route::group([
		'prefix' => 'exhibit',
	], function () {
		// 展厅列表
		Route::get('/', 'ExhibitController@exhibit_list')->name('admin.data.exhibit');
		// 展厅编辑
		Route::match([
			'get',
			'post'
		], '/edit/{id}', 'ExhibitController@edit')->name('admin.data.exhibit.edit');
		// 展厅删除
		Route::get('/delete/{id}', 'ExhibitController@delete')->name('admin.data.exhibit.delete');
		// 设为轮播
		Route::get('/set_lb/{id}', 'ExhibitController@set_lb')->name('admin.data.exhibit.set_lb');
		// 取消轮播
		Route::get('/unset_lb/{id}', 'ExhibitController@unset_lb')->name('admin.data.exhibit.unset_lb');
		// 展品评论审核列表
		Route::get('/exhibit_comment_list', 'ExhibitController@exhibit_comment_list')->name('admin.data.exhibit.exhibit_comment_list');
		// 通过审核
		Route::get('/pass_check/{type}/{ids}', 'ExhibitController@pass_check')->name('admin.data.exhibit.pass_check');
		// 审核不通过
		Route::get('/unpass_check/{type}/{ids}', 'ExhibitController@unpass_check')->name('admin.data.exhibit.unpass_check');
		// 删除
		Route::get('/del_check/{type}/{ids}', 'ExhibitController@del_check')->name('admin.data.exhibit.del_check');

		//资源打包更新
		Route::match([
			'get',
			'post'
		], '/resource_zip', 'ExhibitController@resource_zip')->name('admin.data.exhibit.resource_zip');
		Route::post('/update_zip', 'ExhibitController@update_zip')->name('admin.data.exhibit.update_zip');
		Route::post('/end_zip', 'ExhibitController@end_zip')->name('admin.data.exhibit.end_zip');
		//资源文件下载
		Route::get('/down_file', 'ExhibitController@down_file')->name('admin.data.exhibit.down_file');
		//添加学习单
		Route::get('/add_learn/{id?}', 'ExhibitController@add_learn')->name('admin.data.exhibit.add_learn');
		Route::post('/save_learn', 'ExhibitController@save_learn')->name('admin.data.exhibit.save_learn');
		Route::post('/del_learn', 'ExhibitController@del_learn')->name('admin.data.exhibit.del_learn');
	});

	//蓝牙关联设置
	Route::group([
		'prefix' => 'autonum',
	], function () {
		//蓝牙关联列表
		Route::get('/', 'AutonumController@autonum_list')->name('admin.data.autonum');
		// 蓝牙关联编辑
		Route::match([
			'get',
			'post'
		], '/edit/{id}', 'AutonumController@edit')->name('admin.data.autonum.edit');
		// 蓝牙关联删除
		Route::get('/delete/{id}', 'AutonumController@delete')->name('admin.data.autonum.delete');
	});

});