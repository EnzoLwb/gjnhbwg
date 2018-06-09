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

