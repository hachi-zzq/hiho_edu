@extends('layout.master1')

@section('title') {{'课程 西南财经大学－教材资料馆'}} @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
    $(document).ready(function(){
        $("#header>nav>ul>li").eq(1).addClass("current");

        $(".icon-info-c").click(function(){
            $(this).parentsUntil(".video-card").siblings(".tips").show();
        });
        $(".close").click(function(){
            $(this).parents(".tips").hide();
        });
    });
</script>
@stop

@section('id') {{'id="courses"'}} @stop

@section('content')
<div class="top-title">
    <div class="content-inner">
        <ul class="sort">
            <li @if(!$select['order']){{'class="current"'}} @endif><a href="/videos">最新</a></li>
            <li @if($select['order'] == 'view'){{'class="current"'}} @endif><a href="/videos?order=view">最热</a></li>
        </ul>
        <h2 class="color-icon-title">
            <i class="color-icon color-icon-book"></i><span>全部课程</span>
        </h2>
    </div>
</div>
<div class="main side-main">
    <div class="content-inner">
    <!-- 左侧边栏 -->
    <aside class="left">
        <!-- 分类过滤 -->
        <div class="filter-wrap">
            <div class="filter-group">
                <div class="filter">
                    <h3 class="color-icon-title">
                        <i class="color-icon color-icon-cate"></i><span>科目</span>
                    </h3>
                    <ul class="filter-list">
                        <li @if(!$select['cid']) {{'class="current"'}}  @endif><a href="/videos?{{$select['year'] ? 'year='.$select['year'] : ''}}{{$select['lang'] ? '&lang='.$select['lang'] : ''}}{{$select['order'] ? '&order='.$select['order'] : ''}}{{$select['topic'] ? '&topic='.$select['topic']: ''}}">全部</a>
                        @foreach($categories as $category)
                            <li @if($category->id == $select['cid']){{'class="current"'}} @endif><a href="/videos?cid={{$category->id}}{{$select['year'] ? '&year='.$select['year'] : ''}}{{$select['lang'] ? '&lang='.$select['lang'] : ''}}{{$select['order'] ? '&order='.$select['order'] : ''}}{{$select['topic'] ? '&topic='.$select['topic']: ''}}"> {{$category->name}}</a></li>
                        <!-- <li><a href=""><i class="icon-ellipsis-h"></i></a></li> -->
                        @endforeach
                    </ul>
                </div>
                <div class="filter">
                    <h3 class="color-icon-title">
                        <i class="color-icon color-icon-cal"></i><span>年份</span>
                    </h3>
                    <ul class="filter-list">
                        <li @if(!$select['year']) {{'class="current"'}} @endif><a href="/videos?{{$select['cid'] ? 'cid='.$select['cid'] : ''}}{{$select['lang'] ? '&lang='.$select['lang'] : ''}}{{$select['order'] ? '&order='.$select['order'] : ''}}{{$select['topic'] ? '&topic='.$select['topic']: ''}}">全部</a></li>
                        <li @if($select['year'] == '2014') {{'class="current"'}} @endif><a href="/videos?{{$select['cid'] ? 'cid='.$select['cid'] : ''}}&year=2014{{$select['lang'] ? '&lang='.$select['lang'] : ''}}{{$select['order'] ? '&order='.$select['order'] : ''}}{{$select['topic'] ? '&topic='.$select['topic']: ''}}">2014</a></li>
                        <li @if($select['year'] == '2013') {{'class="current"'}} @endif><a href="/videos?{{$select['cid'] ? 'cid='.$select['cid'] : ''}}&year=2013{{$select['lang'] ? '&lang='.$select['lang'] : ''}}{{$select['order'] ? '&order='.$select['order'] : ''}}{{$select['topic'] ? '&topic='.$select['topic']: ''}}">2013</a></li>
                        <li @if($select['year'] == '2012') {{'class="current"'}} @endif><a href="/videos?{{$select['cid'] ? 'cid='.$select['cid'] : ''}}&year=2012{{$select['lang'] ? '&lang='.$select['lang'] : ''}}{{$select['order'] ? '&order='.$select['order'] : ''}}{{$select['topic'] ? '&topic='.$select['topic']: ''}}">2012</a></li>
                        <li @if($select['year'] == '2011') {{'class="current"'}} @endif><a href="/videos?{{$select['cid'] ? 'cid='.$select['cid'] : ''}}&year=2011{{$select['lang'] ? '&lang='.$select['lang'] : ''}}{{$select['order'] ? '&order='.$select['order'] : ''}}{{$select['topic'] ? '&topic='.$select['topic']: ''}}">2011</a></li>
                        <li @if($select['year'] == '2010') {{'class="current"'}} @endif><a href="/videos?{{$select['cid'] ? 'cid='.$select['cid'] : ''}}&year=2010{{$select['lang'] ? '&lang='.$select['lang'] : ''}}{{$select['order'] ? '&order='.$select['order'] : ''}}{{$select['topic'] ? '&topic='.$select['topic']: ''}}">2010</a></li>
                        <!-- <li><a href=""><i class="icon-ellipsis-h"></i></a></li> -->
                    </ul>
                </div>
                <div class="filter">
                    <h3 class="color-icon-title">
                        <i class="color-icon color-icon-flag"></i><span>语言</span>
                    </h3>
                    <ul class="filter-list">
                        <li @if(!$select['lang']){{'class="current"'}} @endif><a href="/videos?{{$select['cid'] ? 'cid='.$select['cid'] : ''}}{{$select['year'] ? '&year='.$select['year'] : ''}}{{$select['order'] ? '&order='.$select['order'] : ''}}{{$select['topic'] ? '&topic='.$select['topic']: ''}}">全部</a></li>
                        <li @if($select['lang'] == 'zh_cn'){{'class="current"'}} @endif><a href="/videos?{{$select['cid'] ? 'cid='.$select['cid'] : ''}}{{$select['year'] ? '&year='.$select['year'] : ''}}&lang=zh_cn{{$select['order'] ? '&order='.$select['order'] : ''}}{{$select['topic'] ? '&topic='.$select['topic']: ''}}">中文</a></li>
                        <li @if($select['lang'] == 'en'){{'class="current"'}} @endif><a href="/videos?{{$select['cid'] ? 'cid='.$select['cid'] : ''}}{{$select['year'] ? '&year='.$select['year'] : ''}}&lang=en{{$select['order'] ? '&order='.$select['order'] : ''}}{{$select['topic'] ? '&topic='.$select['topic']: ''}}">英语</a></li>
                    </ul>
                </div>
            </div>
            <div class="filter-group">
                <div class="filter">
                    <h3 class="color-icon-title">
                        <i class="color-icon color-icon-talk"></i><span>主题</span>
                    </h3>
                    <ul class="filter-topic">
                        <li @if(!$select['topic']) {{'class="current"'}}  @endif>
                        <a href="/videos?{{$select['cid'] ? 'cid='.$select['cid'] : ''}}{{$select['year'] ? '&year='.$select['year'] : ''}}{{$select['lang'] ? '&lang='.$select['lang'] : ''}}{{$select['order'] ? '&order='.$select['order'] : ''}}">全部</a>
                        @foreach($topics as $topic)
                        <li @if($topic->id == $select['topic']){{'class="current"'}} @endif>
                        <a href="/videos?{{$select['cid'] ? 'cid='.$select['cid'] : ''}}{{$select['year'] ? '&year='.$select['year'] : ''}}{{$select['lang'] ? '&lang='.$select['lang'] : ''}}{{$select['order'] ? '&order='.$select['order'] : ''}}&topic={{$topic->id}}"> {{$topic->name}}</a>
                        </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>
    </aside>
    <!-- 右侧内容 -->
    <div class="right">
        <div class="right-content">
            @foreach($videos as $k => $v)
                <!-- 视频卡片 -->
                <div class="video-card course">
                    <div class="video-thumb">
                        <img src="{{ action('VideoImageController@getVideoImage', array($v->guid)) }}" alt="">

                        <!-- 鼠标悬浮状态 -->
                        <div class="video-action">
                            <ul class="video-action-btns">
                                <li><a href="javascript:void(0)"><i class="icon-info-c"></i></a></li>
                                <li><a href="/play/{{$v->playid}}" target="_blank"><i class="icon-play-c"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="video-author">
                        <div class="avatar">
                            <a href="{{empty($v->teacher) ? '#' :action('TeacherController@detail',$v->teacher->id)}}" >
                                <img src="{{empty($v->teacher) ? '/static/hiho-edu/img/avatar_default.png' : $v->teacher->portrait_src}}" alt="{{empty($v->teacher) ? '' : $v->teacher->name}}">
                            </a>
                        </div>
                        <div class="author-info">
                            <a href="{{empty($v->teacher) ? '#' :action('TeacherController@detail',$v->teacher->id)}}">
                                <span class="author-name">{{empty($v->teacher) ? '' : $v->teacher->name}}</span>
                            </a>
                            <a href="{{empty($v->department) ? '#' :action('DepartmentController@detail',$v->department->id)}}">
                                <span class="author-department">{{empty($v->department) ? '' : $v->department->name}}</span>
                            </a>
                        </div>
                    </div>
                    <div class="vidoe-info-wrap">
                        <div class="video-title">
                            <h3><a href="/play/{{$v->playid}}" target="_blank">{{$v->videoInfo->title}}</a></h3>
                        </div>
                        <div class="video-meta">
                            <span class="num-view"><i class="icon-preview"></i><b>{{$v->viewed}}</b></span>
                            <span class="num-like"><i class="icon-love"></i><b>{{$v->favoriteCount}}</b></span>
                        </div>
                    </div>

                    <!-- tips -->
                    <div id="tips-show{{$v->playid}}" class="tips tips-top hidden">
                        <div class="tip-inner">
                            <div class="tip-title">
                                <a href="javascript:void(0)" class="close"><i class="icon-times"></i></a>
                                <h4>简介</h4>
                            </div>
                            <div class="tip-content">
                                <p>{{$v->videoInfo->description}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- 翻页 -->
        @include('layout.pagination', array('data' => $videos, 'append' => $select))
    </div>
</div>
@stop