<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManningListTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('manning_list')->insert([
			'type_name' => '民营企业',
		]);
		DB::table('manning_list')->insert([
			'type_name' => '国营企业',
		]);
	}
}
