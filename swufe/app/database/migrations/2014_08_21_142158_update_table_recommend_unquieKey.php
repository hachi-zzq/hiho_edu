<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableRecommendUnquieKey extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//recommend table索引
        DB::statement("ALTER TABLE  `recommends` DROP INDEX  `recommends_type_content_id_unique` ,ADD UNIQUE  `recommends_type_content_id_unique` (  `type` ,  `content_id` ,  `position_id` )");
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
