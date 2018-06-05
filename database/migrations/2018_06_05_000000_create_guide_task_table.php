<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateGuideTaskTable extends Migration
{
	private $tableName = 'guide_task';
	private $tableComment = '讲解员任务排班表';//对讲解员的星级评价只计算此表的星数和数量
	private $primaryKey = 'task_id';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function (Blueprint $table) {
			$table->increments($this->primaryKey);
			$table->integer('guide_id',false,true)->comment('讲解员id');
			$table->dateTime('start_time')->comment('开始时间');
			$table->dateTime('end_time')->comment('结束时间');
			$table->integer('reserve_id',false,true)->comment('预约id');
			$table->tinyInteger('status')->comment('1为进行中 0为未开始 2为已经结束')->default(0);
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
