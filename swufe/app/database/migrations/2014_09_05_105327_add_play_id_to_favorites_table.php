<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlayIdToFavoritesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 删除字段
        if (Schema::hasColumn('favorites', 'video_id')) {
            Schema::table('favorites', function ($table) {
                $table->dropColumn('video_id');
            });
        }

        // 删除字段
        if (Schema::hasColumn('favorites', 'fragments_id')) {
            Schema::table('favorites', function ($table) {
                $table->dropColumn('fragments_id');
            });
        }

        // 增加 play_id字段
        if (!Schema::hasColumn('favorites', 'play_id')) {
            Schema::table('favorites', function ($table) {
                $table->string('play_id')->nullable()->after('user_id')->index();
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
        //
    }

}
