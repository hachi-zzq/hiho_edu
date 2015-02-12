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
    <link rel="stylesheet" href="/source/dist/stylesheets/icon.css">
    <link rel="stylesheet" href="/source/dist/stylesheets/style.css">
    <link rel="stylesheet" href="/source/dist/stylesheets/outdatedBrowser.css">
    @yield("css")
    <script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
    <script src="/static/js/lib/modernizr/2.7.1/modernizr.js"></script>
</head>
<body id="home">

<div class="wrap">
    <!-- 头部 -->
    <header id="header">
        <div class="brand">
            <a href="/">教材资料馆</a>
        </div>
        @include('layout.header_nav')
</div>
</header>

<div id="search" style="@if(\AdvertisementCall::render(1,true)) background:url('{{\AdvertisementCall::render(1,true)}}') @endif" >

    <div class="content-inner">
        <div>
            <h1 class="logo"><a href="/">西南财经大学－教材资料馆</a></h1>
        </div>
        <div>
            <form action="/search" name="siteSearch" id="site_search">
                <div class="search-input">
                    <input type="text" name="keywords" placeholder="搜索视频内容" value="{{\Input::get('keywords')}}">
                    <button type="submit"><i class="icon-search"></i></button>
                </div>
            </form>

        </div>
    </div>
</div>

@yield('content')

@include('layout.footer')
</div>

<!-- <script src="/static/js/lib/jQueryUI/js/jquery-ui-1.10.4.custom.js"></script> -->
@yield("jlib")
@yield("js")
</body>
</html>