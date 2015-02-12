<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMysqlTimeformat extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        DB::statement("ALTER TABLE  `annotations` CHANGE  `st`  `st` DECIMAL( 9, 3 ) NOT NULL");
        DB::statement("ALTER TABLE  `annotations` CHANGE  `et`  `et` DECIMAL( 9, 3 ) NOT NULL");


        DB::statement("ALTER TABLE  `highlights` CHANGE  `st`  `st` DECIMAL( 9, 3 ) NOT NULL");
        DB::statement("ALTER TABLE  `highlights` CHANGE  `et`  `et` DECIMAL( 9, 3 ) NOT NULL");


        DB::statement("ALTER TABLE  `questions` CHANGE  `time_point`  `time_point` DECIMAL( 9, 3 ) NOT NULL");
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
