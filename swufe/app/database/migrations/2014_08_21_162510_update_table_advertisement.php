<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableAdvertisement extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//修改表结构，新增position_id
        DB::statement("ALTER TABLE  `advertisements` ADD  `position_id` SMALLINT( 10 ) UNSIGNED NOT NULL AFTER  `id` ;");
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
