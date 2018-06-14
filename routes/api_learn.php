<?php
//学习单详情页
Route::get('learn_content_info', 'LearnController@learn_content_info');
Route::post('save_answer', 'LearnController@save_answer')->name('api.learn.save_answer');
Route::get('answer_list', 'LearnController@answer_list')->name('api.learn.answer_list');
