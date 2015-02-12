<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccessLevelToCategoriesAndVideos extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('categories', 'access_level')) {
            Schema::table('categories', function ($table) {
                $table->smallInteger('access_level')->default(0);//访问等级
            });
        }
        if (!Schema::hasColumn('videos', 'access_level')) {
            Schema::table('videos', function ($table) {
                $table->smallInteger('access_level')->default(0);//访问等级
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
        if (!Schema::hasColumn('videos', 'access_level')) {
            Schema::table('videos', function ($table) {
                $table->dropColumn('access_level');
            });
        }
        if (!Schema::hasColumn('categories', 'access_level')) {
            Schema::table('categories', function ($table) {
                $table->dropColumn('access_level');
            });
        }
    }

}
