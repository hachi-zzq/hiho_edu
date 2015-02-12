<!DOCTYPE html>
<!--[if lt IE 7]>
<html lang="en" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html lang="en" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html lang="en" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html lang="zh-CN" class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>@yield("title")</title>
    <!-- <link href="/static/css/default/v3/common.css" rel="stylesheet" type="text/css" /> -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="/source/dist/stylesheets/icon.css">
    <link rel="stylesheet" href="/source/dist/stylesheets/style.css">
    <link rel="stylesheet" href="/source/dist/stylesheets/avatar.css">
    <link rel="stylesheet" href="/source/dist/stylesheets/outdatedBrowser.css">
    @yield("css")
    <!-- <script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
    <script src="/static/js/lib/modernizr/2.7.1/modernizr.js"></script> -->
    @yield("js")
</head>

<body
@yield("id")>
<div class="wrap">

    <!-- 头部 -->
    <header id="header">
        <div class="brand">
            <a href="/">教材资料馆</a>
        </div>
        
        @include('layout.header_nav')

        <!-- 头部搜索框 -->
        <div class="header-serach">
            <form action="/search" name="siteSearch" id="site_search">
                <input type="text" name="keywords" placeholder="搜索...">
                <button><i class="icon-search"></i></button>
            </form>
        </div>
    </header>

    @yield('content')

    @include('layout.footer')
    @yield('modal')
</div>
<!-- <script src="/static/js/lib/jQueryUI/js/jquery-ui-1.10.4.custom.js"></script> -->
@yield("jlib")

<!-- 成功 -->
<div class="notification success hidden">
    <div class="inner">
        <div class="notify-icon"><i class="icon-ok-c"></i></div>
        <h2>操作成功</h2>
        <p><span id="num">5</span>秒后将自动跳转到首页 <a href="/">立即跳转</a></p>
        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
    </div>
</div>
<!--  错误 -->
<div class="notification error" style="display: none;">
    <div class="inner">
        <div class="notify-icon"><i class="icon-del-c"></i></div>
        <h2>登录失败</h2>
        <p id="errorMsg">请检查输入信息</p>
        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
    </div>
</div>
</body>
</html>