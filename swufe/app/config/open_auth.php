<?php

return array(

    'weibo' => array(

        'app_key' => '2166219604',

        'app_secret' => 'd54ae0c52d640fce43794968b3e7670d',

        'redirect_uri' => 'http://news.hiho.autotiming.com/download',

        'get_user_info_url' => 'https://api.weibo.com/2/users/show.json', //?source=...&access_token=...&uid=...

        'get_token_info_url' => 'https://api.weibo.com/oauth2/get_token_info'

    ),

    'Facebook' => array(

        'app_key' => '283078978536739', // 测试账号

        'app_secret' => 'da21484485f56fc0b97e1238495ac699', // 测试账号

        'app_access_token' => '283078978536739|N1gc0_SGKQXp0_CtOVbMniMXBck', // 测试账号

        'get_user_info_url' => 'https://graph.facebook.com/me',

        'get_token_info_url' => 'https://graph.facebook.com/debug_token'

    )

);