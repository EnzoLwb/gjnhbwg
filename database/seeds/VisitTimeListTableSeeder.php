<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VisitTimeListTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('visit_time_list')->insert([
			'start_time' => '08:00',
			'end_time' => '09:00',
		]);
		DB::table('visit_time_list')->insert([
			'start_time' => '09:00',
			'end_time' => '10:00',
		]);
		DB::table('visit_time_list')->insert([
			'start_time' => '10:00',
			'end_time' => '11:00',
		]);
		DB::table('visit_time_list')->insert([
			'start_time' => '11:00',
			'end_time' => '12:00',
		]);
	}
}
