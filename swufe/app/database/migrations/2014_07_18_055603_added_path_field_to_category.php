<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddedPathFieldToCategory
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class AddedPathFieldToCategory extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 删除 Level 字段
        if (Schema::hasColumn('categories', 'level')) {
            Schema::table('categories', function ($table) {
                $table->dropColumn('level');
            });
        }

        // 增加 Path 作为树形路径
        if (!Schema::hasColumn('categories', 'path')) {
            Schema::table('categories', function ($table) {
                $table->string('path')->nullable()->index();
            });
        }

        // 删除 Level 字段, 用于机构
        if (Schema::hasColumn('departments', 'level')) {
            Schema::table('departments', function ($table) {
                $table->dropColumn('level');
            });
        }

        // 增加 Path 作为树形路径, 用于机构
        if (!Schema::hasColumn('departments', 'path')) {
            Schema::table('departments', function ($table) {
                $table->string('path')->nullable()->index();
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
    }

}
