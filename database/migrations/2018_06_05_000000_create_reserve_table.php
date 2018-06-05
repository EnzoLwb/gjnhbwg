<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateReserveTable extends Migration
{
	private $tableName = 'reserve';
	private $tableComment = '预约表-个人预约和团体预约';
	private $primaryKey = 'reserve_id';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function (Blueprint $table) {
			$table->increments($this->primaryKey);
			$table->string('contacts',20)->comment('联系人姓名');
			$table->string('phone',60)->comment('手机号');
			$table->date('visit_date')->comment('参观日期');
			$table->tinyInteger('language')->comment('语种1为中文 0为英语')->default(1);
			$table->string('visit_time')->comment('参观时间');
			$table->string('certificate_number',20)->comment('证件号码')->nullable();
			$table->integer('yzm',false,true)->comment('验证码')->nullable();
			$table->integer('guide',false,true)->comment('讲解员id')->nullable();
			$table->text('audit_opinion')->comment('审核意见')->nullable();
			$table->string('reserve_unit',50)->comment('预约单位')->nullable();
			$table->integer('reserve_cou',false,true)->comment('预约人数')->nullable();
			$table->tinyInteger('certificate_type')->comment('证件类型')->nullable();
			$table->tinyInteger('manning')->comment('人员组成')->nullable();
			$table->tinyInteger('reserve_status')->comment('审核中:0  预约失败:2  预约成功:1  已取消:3  进行中:4  已完成:5')->default(0);
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
