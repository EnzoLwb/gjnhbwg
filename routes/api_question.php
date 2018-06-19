<?php
//问卷调查页面
Route::match([
	'get',
	'post'
], 'quesinfo', 'QuestionController@quesinfo')->name('api.question.info');
//问卷调查列表
Route::match([
	'get',
	'post'
], 'question_list', 'QuestionController@question_list')->name('api.question.list');