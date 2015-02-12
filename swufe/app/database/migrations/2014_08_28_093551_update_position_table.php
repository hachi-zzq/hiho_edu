<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePositionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//推荐位，广告位新增系统设置，禁止用户删除
        DB::statement("ALTER TABLE  `ad_positions` ADD  `system` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `type` ;");
        DB::statement("ALTER TABLE  `recommend_positions` ADD  `system` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `type` ;");

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
