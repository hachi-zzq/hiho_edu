<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpecialitiesTableAndVideosSpecialitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        // 实体表,专业
        Schema::create('specialities', function ($table) {
            $table->increments('id');
            $table->string('permalink', 128)->unique();
            $table->integer('parent')->nullable();
            $table->string('name', 32);
            $table->string('path')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
        //视频-专业 关系表
        Schema::create('videos_specialities', function ($table) {
            $table->increments('id');
            $table->integer('video_id');
            $table->integer('speciality_id');
            $table->timestamps();
        });
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
