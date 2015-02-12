<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *机构化课程表结构修改
 * @author zhuzhengqian
 */
class CreateStructCourseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        //参考文献/推荐阅读表
        Schema::create('videos_references',function($table){
            $table->increments('id');
            $table->integer('video_id')->index();
            $table->integer('user_id')->nullable()->index();
            $table->longtext('content'); //内容，富文本
            $table->timestamps();
            $table->softDeletes();
        });

        //视频附件表
        Schema::create('videos_attachments', function($table)
        {
            $table->increments('id');
            $table->integer('video_id')->index();
            $table->integer('user_id')->nullable()->index();
            $table->string('title',256);     //文件标题
            $table->string('server',100)->nullable();
            $table->string('path',256);      //文件上传后存储地址，用于下载，兼容内部资源和外部资源的path路径
            $table->integer('size');      //文件大小,字节
            $table->integer('downloaded')->default(0);//下载次数
            $table->integer('sort');
            $table->string('ext',5)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        //问题表
        Schema::create('questions',function($table){
            $table->increments('id');
            $table->integer('video_id')->index();
            $table->float('time_point',9,3);
            $table->integer('sort');
            $table->string('title',256);
            $table->string('type',10);
            $table->string('answers',256);
            $table->string('correct_answers',256);
            $table->string('correct_operation',10)->default('go_on'); // go_on | go_to | tips
            $table->string('error_operation',10);           // go_on | go_to | tips
            $table->integer('target_highlight_id')->nullable();     //go_to时的跳转点
            $table->text('tips_content')->nullable();               //tips时的tips内容
            $table->timestamps();
            $table->softDeletes();
        });

        //答题记录表
            Schema::create('question_records',function($table){
            $table->increments('id');
            $table->integer('user_id')->nullable()->index();
            $table->integer('question_id')->nullable()->index();
            $table->string('status',10);   //用于记录回答是否正确错误标志
            $table->timestamps();
        });

        //片段重点表
        Schema::create('highlights',function($table){
            $table->increments('id');
            $table->integer('video_id')->index();
            $table->integer('user_id')->nullable()->index();
            $table->float('st',9,3);
            $table->float('et',9,3);
            $table->string('title',256);
            $table->text('description');
            $table->string('thumbnail',256);
            $table->longText('subtitle');
            $table->text('remark');
            $table->timestamps();
            $table->softDeletes();
        });

        //注释表
        Schema::create('annotations',function($table){
            $table->increments('id');
            $table->integer('video_id')->index();
            $table->integer('user_id')->nullable()->index();
            $table->float('st',9,3);
            $table->float('et',9,3);
            $table->longText('content');  //注释内容，富文本
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
