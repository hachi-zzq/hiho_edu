<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAdPositionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//修改广告位，新增状态字段
        DB::statement("ALTER TABLE  `ad_positions` ADD  `status` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `system` ;");
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
