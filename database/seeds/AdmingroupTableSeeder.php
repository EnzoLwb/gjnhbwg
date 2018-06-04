<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdmingroupTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('admin_group')->insert([
			'groupname' => 'HD管理员',
			'privs' => 'all'
		]);
		DB::table('admin_group')->insert([
			'groupname' => '超级管理员',
			'privs' => '["home","setting","admin-setting-basesetting","user","admin-user-users","admin-setting-adminusers","admin-setting-admingroup","file","admin-file-file","admin-file-file:upload","admin-file-file:multiupload","admin-file-file:multiupload","article","admin-article-article","admin-article-acategory","admin-article-comment"]'
		]);
	}
}
