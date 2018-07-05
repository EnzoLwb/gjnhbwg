<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVisitRoadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('visit_road', function ($table) {
			$table->text('weight_exhibit_ids_all')->comment('展品排序集合（包含地图隐藏展品）');
			$table->text('weight_exhibit_ids1_all')->comment('1F展品排序集合（包含地图隐藏展品）')->nullable();
			$table->text('weight_exhibit_ids2_all')->comment('2F展品排序集合（包含地图隐藏展品）')->nullable();
			$table->text('weight_exhibit_ids3_all')->comment('3F展品排序集合（包含地图隐藏展品）')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
