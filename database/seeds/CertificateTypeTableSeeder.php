<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CertificateTypeTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('certificate_type')->insert([
			'type_name' => '身份证',
		]);
		DB::table('certificate_type')->insert([
			'type_name' => '驾驶证',
		]);
	}
}
