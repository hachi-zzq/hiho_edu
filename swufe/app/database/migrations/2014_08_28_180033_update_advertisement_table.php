<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAdvertisementTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        DB::statement("ALTER TABLE  `advertisements` CHANGE  `description`  `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,
                        CHANGE  `fromtime`  `fromtime` INT( 11 ) NOT NULL DEFAULT  '0',
                        CHANGE  `totime`  `totime` INT( 11 ) NOT NULL DEFAULT  '0',
                        CHANGE  `img_src`  `img_src` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,
                        CHANGE  `text_name`  `text_name` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,
                        CHANGE  `href`  `href` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,
                        CHANGE  `code`  `code` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,
                        CHANGE  `sort`  `sort` SMALLINT( 6 ) NOT NULL DEFAULT  '0',
                        CHANGE  `status`  `status` SMALLINT( 6 ) NOT NULL DEFAULT  '0'");
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
