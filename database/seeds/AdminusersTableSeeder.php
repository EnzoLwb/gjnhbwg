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
		$password = get_password('111111', $salt);

		DB::table('admin_users')->insert([
			'groupid' => '1',
			'username' => env('ADMIN_USERNAME', 'admin'),
			'salt' => $salt,
			'password' => $password
		]);

		$salt_admin = Str::random(6);
		$password_admin = get_password('111111', $salt_admin);
		DB::table('admin_users')->insert([
			'groupid' => '2',
			'username' => 'admin',
			'salt' => $salt_admin,
			'password' => $password_admin
		]);
	}
}
