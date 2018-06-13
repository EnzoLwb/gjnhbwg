<?php
/**
 * 服务信息路由
 */


Route::group([
	'prefix' => 'interaction',
	'namespace' => 'Interaction'
], function () {
	//学习单
	Route::group([
		'prefix' => 'learn',
	], function () {
		Route::get('/question_list', 'LearnController@question_list')->name('admin.interaction.learn.question_list');
		Route::get('/add_question', 'LearnController@add_question')->name('admin.interaction.learn.add_question');
		Route::post('/save_question', 'LearnController@save_question')->name('admin.interaction.learn.save_question');
		Route::get('/delete_question/{id}', 'LearnController@delete_question')->name('admin.interaction.learn.delete_question');
		Route::get('/edit_question/{id}', 'LearnController@edit_question')->name('admin.interaction.learn.edit_question');
	});

});