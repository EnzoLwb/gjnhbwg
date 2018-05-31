<?php

Route::get('/', function () {
});

// 前台相关路由
//include_once 'web_frontend.php';

// 后台相关路由
include_once 'web_backend.php';

// 通用路由
include_once 'web_common.php';
