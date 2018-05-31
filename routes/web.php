<?php

// 默认登录后台
Route::get('/', function () {
	return redirect('/admin');
});

// 前台登录注册路由
Auth::routes();

// 前台需要登录验证的路由
Route::group([
	'middleware' => 'auth'
], function () {

});

// 后台登录路由
Route::group([
	'prefix' => 'admin',
	'namespace' => 'Admin',
], function () {
	Route::get('login', 'LoginController@showLoginForm')->name('admin.login');
	Route::post('login', 'LoginController@login');
	Route::post('logout', 'LoginController@logout');
});

// 后台需要登录验证的路由
Route::group([
	'prefix' => 'admin',
	'namespace' => 'Admin',
	'middleware' => 'auth.admin',
], function () {
	// Ajax上传图片 - 后台通用
	Route::post('upload', 'UploadController@uploadimg');
	// 管理员修改密码
	Route::match([
		'get',
		'post'
	], 'setting/adminusers/password', 'Setting\AdminUsersController@password');
	// 管理员修改账户信息
	Route::match([
		'get',
		'post'
	], 'setting/adminusers/edit_userinfo', 'Setting\AdminUsersController@edit_userinfo');
	// 更新缓存
	Route::get('setting/basesetting/clearcache', 'Setting\BaseSettingController@clearcache');
	//
	Route::group([
		'prefix' => 'file',
		'namespace' => 'File'
	], function () {
		// 上传资源
		Route::get('/file/upload_resource', 'FileController@upload_resource');
		// 上传资源详情页
		Route::get('/file/upload_resource_html/{uploaded_type}/{file_id}/{type}/{now_num}', 'FileController@upload_resource_html');
	});
});

// 后台需要登录验证和权限验证的路由
Route::group([
	'prefix' => 'admin',
	'namespace' => 'Admin',
	'middleware' => [
		'auth.admin',
		'priv.admin'
	]
], function () {
	Route::get('/', 'HomeController@index')->name('admin.index');

	Route::group([
		'prefix' => 'user',
		'namespace' => 'User'
	], function () {
		Route::group([
			'prefix' => 'users',
		], function () {
			// 用户管理列表
			Route::get('/', 'UsersController@index');
			// 添加用户
			Route::get('/add', 'UsersController@add');
			// 编辑用户
			Route::get('/edit/{uid}', 'UsersController@edit');
			// 保存用户信息
			Route::post('/save', 'UsersController@save');
			// 删除用户
			Route::get('/delete/{uid}', 'UsersController@delete');
		});
	});

	Route::group([
		'prefix' => 'setting',
		'namespace' => 'Setting'
	], function () {
		Route::group([
			'prefix' => 'adminusers',
		], function () {
			// 管理员列表
			Route::get('/', 'AdminUsersController@index');
			// 添加管理员
			Route::get('/add', 'AdminUsersController@add');
			// 编辑管理员
			Route::get('/edit/{uid}', 'AdminUsersController@edit');
			// 保存用户信息
			Route::post('/save', 'AdminUsersController@save');
			// 删除管理员
			Route::get('/delete/{uid}', 'AdminUsersController@delete');
		});

		Route::group([
			'prefix' => 'admingroup',
		], function () {
			// 管理员用户组列表
			Route::get('/', 'AdminGroupController@index');
			// 添加用户组
			Route::get('/add', 'AdminGroupController@add');
			// 编辑用户组
			Route::get('/edit/{groupid}', 'AdminGroupController@edit');
			// 保存用户组
			Route::post('/save', 'AdminGroupController@save');
			// 删除用户组
			Route::get('/delete/{groupid}', 'AdminGroupController@delete');
		});

		// 网站设置
		Route::match([
			'get',
			'post'
		], '/basesetting', 'BaseSettingController@index');
	});

	Route::group([
		'prefix' => 'article',
		'namespace' => 'Article'
	], function () {
		Route::group([
			'prefix' => 'acategory',
		], function () {
			// 文章分类列表
			Route::get('/', 'AcategoryController@index');
			// 添加文章分类
			Route::get('/add', 'AcategoryController@add');
			// 编辑文章分类
			Route::get('/edit/{cate_id}', 'AcategoryController@edit');
			// 删除文章分类
			Route::get('/delete/{cate_id}', 'AcategoryController@delete');
			// 保存文章分类
			Route::post('/save', 'AcategoryController@save');
		});

		Route::group([
			'prefix' => 'article',
		], function () {
			// 文章列表
			Route::get('/', 'ArticleController@index');
			// 添加文章
			Route::get('/add', 'ArticleController@add');
			// 编辑文章
			Route::get('/edit/{id}', 'ArticleController@edit');
			// 删除文章
			Route::get('/delete/{id}', 'ArticleController@delete');
			// 保存文章
			Route::post('/save', 'ArticleController@save');
			// 文章批量操作
			Route::get('/batch/{field}/{value}/{ids}', 'ArticleController@batch');
		});

		Route::group([
			'prefix' => 'comment',
		], function () {
			// 评论列表
			Route::get('/{status?}', 'CommentController@index');
			// 删除评论
			Route::get('/delete/{id}', 'CommentController@delete');
			// 评论批量审核
			Route::get('/pass/{ids}', 'CommentController@pass');
		});
	});

	Route::group([
		'prefix' => 'file',
		'namespace' => 'File'
	], function () {
		// 文件列表
		Route::get('/file', 'FileController@index');
		// 下载文件
		Route::get('/file/download/{file_id}', 'FileController@download');
		// 删除文件
		Route::get('/file/delete/{file_ids}', 'FileController@delete');
		// 上传文件
		Route::match([
			'get',
			'post'
		], '/file/upload', 'FileController@upload');
		// 分片上传文件
		Route::match([
			'get',
			'post'
		], '/file/multiupload', 'FileController@multiUpload');
		// 检查文件分片
		Route::get('/file/checkmfile', 'FileController@checkMfile');
	});
});

// 验证码显示 - 通用
Route::get('cpt/show', 'CptController@show');
// 验证码验证 - 通用
Route::get('cpt/check', 'CptController@check');
// UEditor
Route::get('/ueditor/config', 'UeditorController@config');
Route::post('/ueditor/uploadimage', 'UeditorController@uploadimage');
Route::post('/ueditor/uploadfile', 'UeditorController@uploadfile');
Route::post('/ueditor/uploadvideo', 'UeditorController@uploadvideo');

Route::get('ip', function () {
	dd(request()->ip());
});

Route::match([
	'get',
	'post'
], 'phpcode', function () {
	$show = '';
	if (request('code')) {
		$show = m_phpcode_format(request('code'));
	}
	return view('phpcode', [
		'show' => $show,
		'code' => request('code')
	]);
});

// Test
Route::get('test', function () {
});
