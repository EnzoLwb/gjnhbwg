<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// 创建超级管理员组
		$this->call(AdmingroupTableSeeder::class);
		// 创建后台管理员 admin 111111
		$this->call(AdminusersTableSeeder::class);
		// 创建默认附件类型
		$this->call(UploadedTypeTableSeeder::class);
		// 创建默认文章分类
		$this->call(AcategoryTableSeeder::class);
		// 默认配置
		$this->call(SettingTableSeeder::class);
		// 地区库
		$this->call(RegionTableSeeder::class);
		//证件类型
		$this->call(CertificateTypeTableSeeder::class);
		//人员构成
		$this->call(ManningListTableSeeder::class);
		//参观时间
		$this->call(VisitTimeListTableSeeder::class);
	}
}
