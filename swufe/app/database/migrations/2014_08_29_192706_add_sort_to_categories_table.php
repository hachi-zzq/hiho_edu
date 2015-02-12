<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSortToCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasColumn('categories', 'sort')) {
            Schema::table('categories', function ($table) {
                $table->smallInteger('sort')->default(0);//访问等级
            });
        }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        if (!Schema::hasColumn('categories', 'sort')) {
            Schema::table('categories', function ($table) {
                $table->dropColumn('sort');
            });
        }
	}

}
