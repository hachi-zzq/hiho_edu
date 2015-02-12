<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFragmentsTimeToDecimal extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `fragments` CHANGE COLUMN `start_time` `start_time` DECIMAL(8,3) NOT NULL');
        DB::statement('ALTER TABLE `fragments` CHANGE COLUMN `end_time` `end_time` DECIMAL(8,3) NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `fragments` CHANGE COLUMN `start_time` `start_time` FLOAT(10,3)');
        DB::statement('ALTER TABLE `fragments` CHANGE COLUMN `end_time` `end_time` FLOAT(10,3)');
    }

}
