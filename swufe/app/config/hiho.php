<?php

return array(

    'is_cloud_center' => true,

    'cloud_center_url' => 'http://hiho.autotiming.com',

    'videoimage_root_url' => 'http://hiho.autotiming.com/vi',

    'web' => array(
        'version' => 'v140708',
    ),

    'restapi' => array(
        'version' => '0.3.4',
    ),

    'pushapi' => array(
        'version' => '0.1.0',
    ),

    'sewise' => array(
        'pull_api_url' => 'http://219.232.161.206/service/api/', // TODO
        'pull_language' => array('en', 'en_US', 'zh_CN'), // TODO
        'storage_api'=>'http://171.221.3.200/service/api/?do=videostorage',
        'video_status_api'=>'http://171.221.3.200/service/api/?do=index&op=getstatus'
    ),

//    'search_engine' => array(
//        'solr_server' => 'solr.autotiming.com',
//        'solr_port' => 80,
//        'solr_version' => 4,
//        'solr_core' => '/solr/swufe-video/',
//    )

    'search_engine' => array(
        'solr_server' => 'localhost',
        'solr_port' => 8983,
        'solr_version' => 4,
        'solr_core' => '/solr/swufe-video/'
    ),

    'security_policy' =>array(
        'login_fail_interval' => 10, //min 登录失间隔时间
        'login_fail_need_verification_code_max_times' => 3, //登录失败次数阈值，-1表示关闭验证码

        'login_fail_lock_max_times' => -1, //登录失败次数阈值 -1表示关闭登录失败次数过多锁定
        'lock_user_duration' => 60, //min 账户锁定时间

        'password_forgot_post_interval' => 5, //min 发送充值密码邮件时间间隔
        'password_forgot_post_max_times' => 10, // -1表示关闭找回限制
    )

);