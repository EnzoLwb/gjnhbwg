<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class CreateXjhdTable extends Migration
{
	private $tableName = 'xjhd';
	private $tableComment = '宣教活动表';
	private $primaryKey = 'id';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function (Blueprint $table) {
			$table->increments('id');
			$table->string('title',255)->comment('标题')->nullable();
			$table->string('title_1',255)->comment('副标题标题')->nullable();
			$table->string('img',255)->comment('图片')->nullable();
			$table->string('active_place',255)->comment('活动地点')->nullable();
			$table->string('active_start_date',255)->comment('活动开始时间')->nullable();
			$table->string('active_end_date',255)->comment('活动结束时间')->nullable();
			$table->string('active_price',255)->comment('活动价格')->nullable();
			$table->string('active_time',255)->comment('活动时长')->nullable();
			$table->text('content')->comment('简介')->nullable();
			$table->integer('is_show', false, true)->comment('是否显示 1显示 2不显示')->default(1);
			$table->integer('order_no', false, true)->comment('顺序')->default(255);
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
