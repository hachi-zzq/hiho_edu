@extends('layout.master1')

@section('title') {{'剪辑 西南财经大学－教材资料馆'}} @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
    $(document).ready(function(){
        var cur = parseInt($("#hiddenTopCategoriesCount").val()) + 1;
        $("#header>nav>ul>li").eq(cur).addClass("current");
        $(".icon-info-c").click(function(){
            $(this).parentsUntil(".video-card").siblings(".tips").show().css('z-index', 1);
        });
        $(".close").click(function(){
            $(this).parents(".tips").hide();
        });
    });
</script>
@stop

@section("content")
<div class="top-title">
    <div class="content-inner">
        <h2 class="color-icon-title">
            <i class="color-icon color-icon-video"></i><span>剪集</span>
        </h2>
        <div class="tab">
            <ul>
                <li class="active"><a href="/clips">短视频</a></li>
                <li><a href="/note">笔记</a></li>
            </ul>
        </div>
        <ul class="sort">
            <li {{$input ? '' : 'class="current"'}}><a href="/clips">最新</a></li>
            <li {{$input == 'hot' ? 'class="current"' : ''}}><a href="/clips?order=hot">最热</a></li>
        </ul>
    </div>
</div>

<div class="main">
    <div class="content-inner">
        <div class="card-wrap">
            @foreach($fragments as $m)
            <!-- 视频卡片-短视频 -->
            <div class="video-card clip-card">
                <div class="video-thumb">
                    <img src="{{ $m->cover }}" alt="">
                    <time class="video-time">
                        {{gmdate('H:i:s', $m->end_time - $m->start_time)}}
                    </time>

                    <!-- 鼠标悬浮状态 -->
                    <div class="video-action">
                        <ul class="video-action-btns">
                            <li><a href="javascript:void(0)"><i class="icon-info-c"></i></a></li>
                            <li><a href="/play/{{$m->playid}}" target="_blank"><i class="icon-play-c"></i></a></li>
                        </ul>
                    </div>
                </div>

                <div class="vidoe-info-wrap">
                    <div class="video-date">
                        <time>{{ \Tool::dateFormat($m->created_at) }}</time>
                        <!-- <span>6 段视频短片</span> -->
                    </div>
                    <div class="video-article">
                        <p>{{empty($m->videoInfo) ? '' : $m->videoInfo->description}}</p>
                    </div>
                    <div class="video-meta">
                        <span class="num-view"><i class="icon-preview"></i><b>{{$m->viewed}}</b></span>
                        <span class="num-like"><i class="icon-love"></i><b>{{$m->favoriteCount}}</b></span>
                    </div>
                </div>

                <!-- tips -->
                <div id="tips-show{{$m->playid}}" class="tips tips-top hidden">
                    <div class="tip-inner">
                        <div class="tip-title">
                            <a href="javascript:void(0)" class="close"><i class="icon-times"></i></a>
                            <h4>简介</h4>
                        </div>
                        <div class="tip-content">
                            <p>{{empty($m->videoInfo) ? '' : $m->videoInfo->description}}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<input type="hidden" id="hiddenTopCategoriesCount" value="{{count($topCategories)}}" />
<!-- 翻页 -->
@include('layout.pagination', array('data' => $fragments))

@stop
<!--/ container-->
@section("jlib")
<script>
    var JLib = JLib || {};
    JLib.config = JLib.config || {};
    JLib.config.pageName = 'clipsList';
</script>
@stop

