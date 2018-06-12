<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateVisitRoadTable extends Migration
{
	private $tableName = 'visit_road';
	private $tableComment = '路线列表';
	private $primaryKey = 'id';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function (Blueprint $table) {
			$table->increments($this->primaryKey);
			$table->unsignedInteger('type')->comment('路线类别1后台设定2自定义路线');
			$table->text('road_list')->comment('展品列表');
			$table->text('road_info')->comment('路线详情');
			$table->text('weight_exhibit_ids')->comment('展品排序集合');

			$table->string('road_long', 100)->comment('线路游览时长')->nullable();
			$table->string('road_img', 255)->comment('线路图存储路径')->nullable();

			$table->text('road_list1')->comment('1F展品列表')->nullable();
			$table->text('road_info1')->comment('1F路线详情')->nullable();
			$table->text('weight_exhibit_ids1')->comment('1F展品排序集合')->nullable();
			$table->text('road_info1_cache')->comment('生成路线计算结果')->nullable();

			$table->text('road_list2')->comment('2F展品列表')->nullable();
			$table->text('road_info2')->comment('2F路线详情')->nullable();
			$table->text('weight_exhibit_ids2')->comment('2F展品排序集合')->nullable();
			$table->text('road_info2_cache')->comment('生成路线计算结果')->nullable();

			$table->text('road_list3')->comment('3F展品列表')->nullable();
			$table->text('road_info3')->comment('3F路线详情')->nullable();
			$table->text('weight_exhibit_ids3')->comment('3F展品排序集合')->nullable();
			$table->text('road_info3_cache')->comment('生成路线计算结果')->nullable();

			$table->integer('uid', false, true)->comment('用户id')->default(0);
			$table->timestamps();

			if (env('DB_CONNECTION') == 'oracle') {
				$table->comment = $this->tableComment;
			}
		});

		if (env('DB_CONNECTION') == 'mysql') {
			DB::statement("ALTER TABLE `" . DB::getTablePrefix() . $this->tableName . "` comment '{$this->tableComment}'");
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists($this->tableName);
		if (env('DB_CONNECTION') == 'oracle') {
			$sequence = DB::getSequence();
			$sequence->drop(strtoupper($this->tableName . '_' . $this->primaryKey . '_SEQ'));
		}
	}
}

