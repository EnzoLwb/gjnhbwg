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
	Route::group([
		'prefix' => 'question',
	], function () {
		// 问卷调查首页
		Route::get('/', 'QuestionController@index')->name('admin.interaction.question.index');
		//问卷调查更改状态
		Route::get('/ques_status', 'QuestionController@ques_status')->name('admin.interaction.question.ques_status');
		//获取详情
		Route::post('/ajax_ques', 'QuestionController@ajax_ques')->name('admin.interaction.question.ajax_ques');
		//编辑题目
		Route::match(['get','post'],'/edit_ques', 'QuestionController@edit_ques')->name('admin.interaction.question.edit_ques');
		Route::match(['get','post'],'/quesinfo_list', 'QuestionController@quesinfo_list')->name('admin.interaction.question.quesinfo_list');
		//ajax显示所有题目
		Route::match(['get','post'],'/ajax_quesinfo', 'QuestionController@ajax_quesinfo')->name('admin.interaction.question.ajax_quesinfo');
		//获取信息
		Route::match(['get','post'],'/ajax_forminfo', 'QuestionController@ajax_forminfo')->name('admin.interaction.question.ajax_forminfo');
		//修改
		Route::match(['get','post'],'/edit_quesinfo', 'QuestionController@edit_quesinfo')->name('admin.interaction.question.edit_quesinfo');
		//导出
		Route::match(['get','post'],'/ques_export', 'QuestionController@ques_export')->name('admin.interaction.question.ques_export');
		//问卷统计
		Route::match(['get','post'],'/ques_info', 'QuestionController@ques_info')->name('admin.interaction.question.ques_info');
		//问卷作答详情统计
		Route::match(['get','post'],'/ques_textinfo', 'QuestionController@ques_textinfo')->name('admin.interaction.question.ques_textinfo');

	});

});