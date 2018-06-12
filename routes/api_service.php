<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 11:16
 */


// 场馆简介
Route::get('intro', 'IntroController@intro');
// 场馆简介页面
Route::get('intro_html', 'IntroController@intro_html');
//参观指南
Route::get('cgzn_html', 'CgznController@cgzn_html');
Route::get('cgzn', 'CgznController@cgzn');

//文创系列
Route::get('xl_list', 'WenchuangController@xl_list');
Route::get('xl_detail', 'WenchuangController@xl_detail');
Route::get('xl_detail_a', 'WenchuangController@xl_detail_a');
Route::get('product_detail', 'WenchuangController@product_detail');
Route::get('product_content', 'WenchuangController@product_content');
Route::get('xl_content', 'WenchuangController@xl_content');
//宣教活动
Route::get('xjhd_list', 'XjhdController@xjhd_list');
Route::get('xjhd_detail', 'XjhdController@xjhd_detail');
Route::get('xjhd_content', 'XjhdController@xjhd_content');

