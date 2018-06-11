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
Route::get('xl_list', 'WenChuangController@xl_list');
Route::get('xl_detail', 'WenChuangController@xl_detail');
Route::get('product_detail', 'WenChuangController@product_detail');
Route::get('product_content', 'WenChuangController@product_content');
Route::get('xl_content', 'WenChuangController@xl_content');
//宣教活动
Route::get('xjhd_list', 'XjhdController@xjhd_list');
Route::get('xjhd_detail', 'XjhdController@xjhd_detail');
Route::get('xjhd_content', 'XjhdController@xjhd_content');

