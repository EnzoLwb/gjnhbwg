<?php

namespace App\Http\Controllers\Admin;


class HomeController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 后台首页
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('admin.home');
	}

	public function welcome()
	{
		echo '<h1>系统主页</h1>';
	}
}
