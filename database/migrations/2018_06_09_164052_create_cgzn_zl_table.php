<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class CreateCgznZlTable extends Migration
{
	private $tableName = 'cgzn_zl';
	private $tableComment = '参观指南租赁多语种表';
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
			$table->integer('language_id', false, true)->comment('多语种id');
			$table->string('step1',255)->comment('租赁步骤1')->nullable();
			$table->string('step2',255)->comment('租赁步骤2')->nullable();
			$table->string('step3',255)->comment('租赁步骤3')->nullable();
			$table->string('step4',255)->comment('租赁步骤4')->nullable();
			$table->string('step5',255)->comment('租赁步骤5')->nullable();
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
