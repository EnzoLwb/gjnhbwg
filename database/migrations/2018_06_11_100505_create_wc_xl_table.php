<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class CreateWcXlTable extends Migration
{
	private $tableName = 'wc_xl';
	private $tableComment = '文创系列表';
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
			$table->string('img',255)->comment('图片（列表和详情中背景）')->nullable();
			$table->string('img_1',255)->comment('详情中小图')->nullable();
			$table->text('content')->comment('简介')->nullable();
			$table->integer('is_show', false, true)->comment('是否显示 1显示 2不显示')->default(1);
			$table->integer('order_no', false, true)->comment('顺序')->default(1);
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
