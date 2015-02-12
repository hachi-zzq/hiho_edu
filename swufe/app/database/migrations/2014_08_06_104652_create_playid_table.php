<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayidTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // PlayID
        // EntityType
        // EntityId
        // SoftDelete

        // 索引1: PlayID
        // 索引2: IndexId + Type (唯一)

        // 访问: http://www.hiho.com/play/[PlayID]

        Schema::create('playid', function ($table) {
            $table->string('play_id', 8)->primary();
            $table->string('entity_type');
            $table->integer('entity_id')->unsigned();
            $table->unique(array('entity_type', 'entity_id')); // 复合唯一索引
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playid');
    }

}
