<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminusersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// 处理密码
		$salt = Str::random(6);
		$password = get_password('hengda', $salt);
		$salt2 = Str::random(6);
		$password2 = get_password('hengda', $salt2);
		DB::table('admin_users')->insert([
			'groupid' => '1',
			'username' => 'hdroot',
			'salt' => $salt,
			'password' => $password
		]);
		DB::table('admin_users')->insert([
			'groupid' => '2',
			'username' => 'admin',
			'salt' => $salt2,
			'password' => $password2
		]);
	}
}
