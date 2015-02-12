<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecommendPlace extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//新增推荐位表
        Schema::create('recommend_positions',function($table){
            $table->increments('id');
            $table->string('name',128)->unique();
            $table->string('class',128)->unique();
            $table->smallInteger('max_num');
            $table->string('type',10)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        //删除旧的hot_recommend
        Schema::drop('hot_recommend');
//
        //新的recommend
        Schema::create('recommends',function($table){
            $table->increments('id');
            $table->integer('position_id')->index();
            $table->integer('content_id');
            $table->smallInteger('sort')->default(0);
            $table->string('type',10);
            $table->unique(array('type', 'content_id'));
            $table->timestamps();
            $table->softDeletes();
        });

        //广告位表
        Schema::create('ad_positions',function($table){
            $table->increments('id');
            $table->string('name',100);
            $table->text('description');
            $table->string('type',10);
            $table->timestamps();
            $table->softDeletes();
        });

        //广告表
        Schema::create('advertisements',function($table){
            $table->increments('id');
            $table->string('name',100);
            $table->text('description');
            $table->string('type',10);
            $table->integer('fromtime');
            $table->integer('totime');
            $table->string('img_src',256);
            $table->string('text_name',256);
            $table->string('href',256);
            $table->text('code');
            $table->smallInteger('sort');
            $table->smallInteger('status');
            $table->timestamps();
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
		//
	}

}
