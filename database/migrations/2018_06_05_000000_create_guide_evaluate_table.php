<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateGuideEvaluateTable extends Migration
{
	private $tableName = 'guide_evaluate';
	private $tableComment = '讲解员评价表';//对讲解员的星级评价只计算此表的星数和数量
	private $primaryKey = 'eva_id';

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
			$table->integer('user_number',false,true)->comment('用户id');
			$table->integer('reserve_id',false,true)->comment('预约id');
			$table->tinyInteger('star',false,true)->comment('星数');
			$table->text('content')->comment('评价内容');
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
