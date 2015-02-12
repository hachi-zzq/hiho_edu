<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirstTables extends Migration
{

    /**
     * 创建 HiHo-EDU 初始数据库
     */

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('apps', function ($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('name')->nullable();
            $table->string('access_key', 64);
            $table->string('access_secret', 64);
            $table->string('status')->default('NORMAL');
            $table->boolean('is_system')->dafault(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('apps_allowed_categories', function ($table) {
            $table->increments('id');
            $table->integer('app_id');
            $table->integer('category_id');
            $table->timestamps();
        });

        Schema::create('apps_allowed_tags', function ($table) {
            $table->increments('id');
            $table->integer('app_id');
            $table->integer('tag_id');
            $table->timestamps();
        });

        // 实体表, 分类
        Schema::create('categories', function ($table) {
            $table->increments('id');
            $table->string('permalink', 128)->unique();
            $table->integer('parent')->nullable();
            $table->smallInteger('level')->default(0); // CHANGE
            $table->string('name', 32);
            $table->timestamps();
            $table->softDeletes();
        });

        // 实体表, 评论
        Schema::create('comments', function ($table) {
            $table->increments('id');
            $table->string('guid', 36)->unique();
            $table->integer('user_id')->index();
            $table->integer('video_id')->index();
            $table->integer('fragment_id')->nullable();
            $table->float('playing_time')->nullable(); // CHANGE
            $table->text('content');
            $table->integer('reply_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 实体表, 国家
        Schema::create('countries', function ($table) {
            $table->string('id', 2);
            $table->string('name');
        });

        // 关系表, 收藏
        Schema::create('favorites', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('video_id');
            $table->integer('fragments_id')->nullable();
            $table->timestamps();
        });

        // 实体表, 碎片
        Schema::create('fragments', function ($table) {
            $table->increments('id');
            $table->string('guid', 36)->unique();
            $table->integer('video_id')->index();
            $table->float('start_time', 9, 3)->index();
            $table->float('end_time', 9, 3)->index();
            $table->string('title')->nullable();
            $table->string('cover')->nullable();
            $table->integer('user_id')->nullable(); // 创建者用户 ID
            $table->integer('liked')->default(0); // 被赞, CHANGE
            $table->integer('viewed')->default(0); // 被播, CHANGE
            $table->timestamps();
            $table->softDeletes();
        });

        // 关系表, 碎片 Tag
        Schema::create('fragments_tags', function ($table) {
            $table->increments('id');
            $table->integer('fragment_id');
            $table->integer('tag_id');
            $table->timestamps();
            $table->softDeletes();
        });

        // 实体表, 碎片播放地址.
        Schema::create('fragments_resources', function ($table) {
            $table->increments('id');
            $table->integer('video_id');
            $table->boolean('is_original')->default(0);
            $table->float('start_time', 9, 3);
            $table->float('end_time', 9, 3);
            $table->string('type', 5); // 视频类型...
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('src');
            $table->string('status')->default('NORMAL');
            $table->timestamps();
            $table->softDeletes();
        });

        // 带 LBS 碎片分享记录...
        Schema::create('fragments_shares', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->index();
            $table->integer('fragment_id');
            $table->double('longitude')->nullable();
            $table->double('latitude')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 实体表, 语言
        Schema::create('languages', function ($table) {
            $table->string('id');
            $table->string('name');
        });

        // 实体表, RestAPI 日志
        Schema::create('rest_logs', function ($table) {
            $table->increments('id');
            $table->text('request')->nullable();
            $table->string('request_route')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->text('client_useragent')->nullable();
            $table->string('client_ip', 15);
            $table->string('msgcode', 6)->nullable();
            $table->text('message')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 搜索历史
        Schema::create('search_histories', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->index();
            $table->string('keywords');
            $table->string('country', 2);
            $table->string('language', 5);
            $table->timestamps();
            $table->softDeletes();
        });

        // 搜索热门关键词, 手工控制
        Schema::create('search_hot_keywords', function ($table) {
            $table->increments('id');
            $table->string('keywords');
            $table->integer('rank')->default(0);
            $table->string('country', 2);
            $table->string('language', 5);
            $table->timestamps();
            $table->softDeletes();
        });

        // 来源, 使用不明确
        Schema::create('sources', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // 字幕
        Schema::create('subtitles', function ($table) {
            $table->increments('id');
            $table->integer('video_id');
            $table->boolean('is_original')->default(0);
            $table->string('language', 5);
            $table->string('type', 5);
            $table->smallInteger('accuracy')->default(0); // 精度
            $table->string('url')->nullable();
            $table->longText('content');
            $table->string('at_id', 16)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 字幕, 全文索引表
        Schema::create('subtitles_fulltext', function ($table) {
            $table->increments('id');
            $table->integer('video_id');
            $table->integer('subtitle_id')->index();
            $table->boolean('is_original')->default(0);
            $table->string('language', 5);
            $table->longText('content');
            $table->longText('timeline');
            $table->timestamp('indexed_at')->nullable();
            $table->timestamps();
        });

        // 实体表, 标签
        Schema::create('tags', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        // 用户表
        Schema::create('users', function ($table) {
            $table->increments('user_id');
            $table->string('guid', 36)->unique()->index();
            $table->string('email', 64)->unique();
            $table->string('password', 128);
            $table->string('avatar', 255)->nullable();
            $table->string('nickname', 32)->nullable();
            $table->string('remember_token', 255)->nullable();
            $table->timestamp('last_time');
            $table->string('last_ip', 15);
            $table->string('created_ip', 15);
            $table->boolean('is_admin')->default(0);
            $table->string('status')->default('NORMAL');
            $table->timestamps();
            $table->softDeletes();
        });

        // 用户 KV
        Schema::create('users_kv', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('key', 64);
            $table->text('value')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 用户 Token
        Schema::create('users_tokens', function ($table) {
            $table->increments('id');
            $table->string('token', 32)->unique()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->string('client', 64); // 客户端名称
            $table->dateTime('caycle_at'); // 生命周期截至
            $table->timestamps();
        });

        /**
         * 第三方登录
         * 同步到 HiHo
         */
        Schema::create('users_oauth', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('open_access_token', 256)->index();
            $table->string('open_uid', 64)->index();
            $table->string('open_username', 128)->nullable();
            $table->string('open_nickname', 64)->nullable();
            $table->string('vendor', 64); // Mode
            $table->timestamp('expire');
            $table->text('extra')->nullable();
            $table->string('status')->default('NORMAL');
            $table->dateTime('last_login_at');
            $table->timestamps();
            $table->softDeletes();
        });

        // 实体表, 视频
        Schema::create('videos', function ($table) {
            $table->increments('video_id');
            $table->string('guid', 36)->unique()->index();
            $table->string('country', 2);
            $table->string('language', 5);
            $table->float('length'); // CHANGE
            $table->string('copyright')->nullable();
            $table->string('aspect_ratio')->nullable();
            $table->integer('source_id');
            $table->string('origin_id')->nullable();
            $table->integer('liked')->default(0); // 被赞
            $table->integer('viewed')->default(0); // 被播
            $table->boolean('is_display')->default(0);
            $table->longText('keyframes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('videos_categories', function ($table) {
            $table->increments('id');
            $table->integer('video_id');
            $table->integer('category_id');
            $table->timestamps();
        });

        Schema::create('videos_content_rating', function ($table) {
            $table->increments('id');
            $table->integer('video_id');
            $table->string('country', 2);
            $table->string('rating', 8);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('videos_info', function ($table) {
            $table->increments('id');
            $table->integer('video_id')->index();
            $table->boolean('is_original')->default(0);
            $table->string('language', 5);
            $table->string('title');
            $table->string('author');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('videos_pictures', function ($table) {
            $table->increments('id');
            $table->integer('video_id')->index();
            $table->boolean('is_original')->default(0);
            $table->string('key', 64);
            $table->string('type', 5);
            $table->integer('width');
            $table->integer('height');
            $table->string('src');
            $table->time('occurrence')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('videos_resources', function ($table) {
            $table->increments('id');
            $table->integer('video_id')->index();
            $table->boolean('is_original')->default(0);
            $table->string('type', 5);
            $table->integer('width');
            $table->integer('height');
            $table->string('src');
            $table->string('status')->default('NORMAL');
            $table->timestamps();
            $table->softDeletes();
        });

        // 关系表, 视频 - Tag
        Schema::create('videos_tags', function ($table) {
            $table->increments('id');
            $table->integer('video_id');
            $table->integer('tag_id');
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * 播放列表(笔记\备课单), playlist(s)
         * - 分类 topics
         */
        Schema::create('playlists', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->index(); // 创建者用户 ID
            $table->string('title');
            $table->text('description')->nullable();
            $table->float('totel_district')->default(0); // 总计长度
            $table->integer('totel_number')->default(0); // 总计数量
            $table->integer('liked')->default(0); // 被赞
            $table->integer('viewed')->default(0); // 被播
            $table->string('type')->default('NOTE'); // 笔记 or 播单
            $table->string('status')->default('NORMAL');
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * 播放列表 - 视频关系
         */
        Schema::create('playlists_fragments', function ($table) {
            $table->increments('id');
            $table->integer('playlist_id')->index();
            $table->integer('video_id');
            $table->integer('fragment_id');
            $table->string('title')->nullable(); // 碎片标题(仅笔记内)
            $table->text('description')->nullable(); // 碎片描述(仅笔记内)
            $table->integer('rank')->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        /**
         * 播放列表收藏
         */
        Schema::create('playlists_favorites', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('playlist_id');
            $table->boolean('update_notification')->dafault(0);
            $table->timestamps();
        });

        // 实体表, 讲师
        Schema::create('teachers', function ($table) {
            $table->increments('id');
            $table->string('permalink', 128)->unique();
            $table->string('name', 48); // 姓名
            $table->string('title', 48)->nullable(); // 头衔
            $table->text('description')->nullable();
            $table->string('portrait_src', 128)->nullable(); // 头像
            $table->integer('user_id')->nullable()->index(); // 绑定的用户 ID
            $table->string('email', 128)->nullable(); // 接收邮件的 Email 地址
            $table->string('website', 128)->nullable(); // 个人主页
            $table->timestamps();
            $table->softDeletes();
        });

        // 关系表, 讲师 - 视频
        Schema::create('teachers_videos', function ($table) {
            $table->increments('id');
            $table->integer('teacher_id')->index();
            $table->integer('video_id')->index();
            $table->timestamps();
        });

        // 实体表, 院系机构
        Schema::create('departments', function ($table) {
            $table->increments('id');
            $table->string('permalink', 128)->unique();
            $table->integer('parent')->nullable();
            $table->smallInteger('level')->default(0); // CHANGE
            $table->string('name', 32);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 关系表, 院系 - 讲师
        Schema::create('departments_teachers', function ($table) {
            $table->increments('id');
            $table->integer('teacher_id')->index();
            $table->integer('department_id')->index();
            $table->timestamps();
        });

        // 实体表, 话题, 类 Tag
        Schema::create('topics', function ($table) {
            $table->increments('id');
            $table->string('permalink', 128)->unique();
            $table->string('name', 32);
            $table->timestamps();
            $table->softDeletes();
        });

        // 关系表, 话题 - 视频
        Schema::create('topics_videos', function ($table) {
            $table->increments('id');
            $table->integer('topic_id')->index();
            $table->integer('video_id')->index();
            $table->timestamps();
        });

        // TODO: 关键词搜索联想 keywords, item to item
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_oauth');
    }

}
