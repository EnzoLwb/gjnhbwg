<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateChatMessageTable extends Migration
{
	private $tableName = 'chat_message';
	private $tableComment = '聊天记录';
	private $primaryKey = 'message_id';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function (Blueprint $table) {
			$table->increments($this->primaryKey);
			$table->text('send_msg')->comment('发送内容')->nullable();
			$table->string('from_user_number')->comment('发送者id')->nullable();
			$table->string('to_user_number')->comment('接收者id')->nullable();
			$table->string('to_client_id')->comment('接收者client_id')->nullable();
			$table->tinyInteger('send_type')->comment('发送类型 1为文本 2为语音')->default(1);
			$table->tinyInteger('device_type')->comment(' 1为手机 2为导览机')->default(1);
			$table->tinyInteger('is_read')->comment(' 1为已读 0为未读')->default(1);
			$table->string('audio_duration')->comment('语音时间长度')->nullable();
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
