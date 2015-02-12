<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoStatus extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // 视频入库后信息记录表，后台专用
        Schema::create('sewise_videos_infos', function ($table) {
            $table->increments('id');
            $table->string('source_id', 10)->index();
            $table->string('task_id', 10)->nullable()->index();
            $table->string('title', 256);
            $table->text('description');
            $table->bigInteger('bytesize');
            $table->float('length');
            $table->string('resource_path');
            $table->enum('language', array('en', 'zh_cn'));
            $table->string('status')->default('NORMAL');
            $table->timestamps();
            $table->softDeletes();
        });

        // 视频封面数据表(视频封面一对多)
        Schema::create('sewise_videos_pictures', function ($table) {
            $table->increments('id');
            $table->integer('video_id')->index();
            $table->string('src', 256);
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });

        // 字幕表（视频字幕一对多）
        Schema::create('sewise_videos_subtitles', function ($table) {
            $table->increments('id');
            $table->integer('video_id')->index();
            $table->string('type', 5)->index();
            $table->longtext('subtitle');
            $table->enum('language', array('en', 'zh_cn'));
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
        //roleback
        Schema::drop('sewise_videos_infos');
        Schema::drop('sewise_videos_pictures');
        Schema::drop('sewise_videos_subtitles');
    }

}
