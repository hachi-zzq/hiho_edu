@extends('layout.master_play')

@section('title')
没有访问权限 - 西南财经大学－教材资料馆
@stop

@section('js_head')
<!-- <script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script> -->
@stop

@section("content")

<header class="player-header">
    <!-- 返回首页 -->
    <div class="back back-home">
        <a href="/" title="返回首页">
            <i class="play-logo"></i>
            <span>返回首页</span>
        </a>
    </div>
    <div class="title">
        <h2>没有访问权限</h2>
    </div>
    <!-- <div class="search"></div> -->
    @if(Auth::guest())

    <div class="user-signin">
        <a href="/login">登录</a>
    </div>

    @else
    <div class="user">
        <div class="avatar">
            <img src="{{ Auth::user()->getAvatar() }}" alt="My User Avatar">
        </div>
        <div class="name">
            <span>{{ Auth::user()->nickname ? Auth::user()->nickname : Auth::user()->email }}</span>
        </div>
        <div class="user-downarrow">
            <i class="icon-caret-down"></i>
        </div>
        <!-- user dropdown -->
        <div class="user-dropdown">
            <ul>
                <li><a href="/my/note" target="_blank">我的笔记</a></li>
                <li><a href="/favorite/videos" target="_blank">我的收藏</a></li>
                <li><a href="/logout" target="_blank">退出账户</a></li>
            </ul>
        </div>
        <!--/ user dropdown -->
    </div>
    @endif
</header>
<!-- 访问权限 -->
<div class="locked">
    <div class="lock-img"></div>
    <h2>抱歉！您无权限访问本视频，您可以<a href="/login">登录</a>有权限的账户或者<a href="/">返回首页</a></h2>
</div>
<!--/ 访问权限 -->
@stop
@section('js_foot')
<script type="text/template" id="tplHighlightItem">
    <li class="emphasis-item">
        <a href="javascript:;" data-start="{start}" class="hiliteLink">
            <div class="number">
                <span>{number}</span>
            </div>
            <div class="thumb">
                <img src="{thumb}" alt="{heading}">
            </div>
            <div class="content">
                <h4>{heading}</h4>
                <time>{start} - {end}</time>
            </div>
        </a>
    </li>
</script>
<script type="text/template" id="tplChoice">
    <li class="choiceItem" data-value="{value}"><a href="javascript:;"><span class="choice-label">{value}</span>{description}</a>
    </li>
</script>
<script src="{{\Config::get('app.pathToSource')}}/scripts/lib/require.js"
        data-main="{{\Config::get('app.pathToSource')}}/scripts/play.js"></script>
@stop