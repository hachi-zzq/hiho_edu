@extends('layout.master1')

@section('title') {{'讲师详情 西南财经大学－教材资料馆'}} @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
    $(document).ready(function () {
        $(".icon-info-c").mouseenter(function () {
            $(this).parentsUntil(".video-card").siblings(".tips").show();
        });
        $(".icon-info-c").mouseleave(function () {
            $(this).parentsUntil(".video-card").siblings(".tips").hide();
        });
        $(".close").click(function () {
            $(this).parents(".tips").hide();
        });
    });
</script>
@stop

@section('content')
<div class="dept-header">
    <div class="dept-header-bg">
        <img src="/static/hiho-edu/img/department_header_bg.jpg" alt="">
    </div>
    <div class="content-inner">
        <div class="teacher-header-content">
            <div class="avatar">
                <img src="{{$teacher->portrait_src}}" alt="">
            </div>
            <div class="info">
                <hgroup>
                    <h2>{{$teacher->name}}</h2>
                    @foreach($teacher->departments as $dt)
                    <small>{{$dt->name}}</small>
                    @endforeach
                </hgroup>
                <p>{{$teacher->description}}</p>
            </div>
            <div class="course-num">
                <p>{{$teacher->videosCount}}</p>
                <span>视频课程</span>
            </div>
        </div>
    </div>
</div>

<div class="teacher-title">
    <div class="content-inner">
        <ul class="sort">
            <li
            {{$input ? '' : 'class="current"'}}><a href="/teacher/{{$teacher->id}}">最新</a></li>
            <li
            {{$input == 'A-Z' ? 'class="current"' : ''}}><a href="/teacher/{{$teacher->id}}?order=A-Z">A-Z</a></li>
        </ul>
        <h2 class="color-icon-title">
            <i class="color-icon color-icon-book"></i><span>TA的课程</span>
        </h2>
    </div>
</div>

<div class="main teacher-wrap">
    <div class="content-inner">
        @foreach($teacher->videos as $k => $tv)
        <!-- 视频卡片 -->
        <div class="video-card course">
            <div class="video-thumb">
                <img
                    src="{{empty($tv->video) ? '/static/img/video_default.png' : action('VideoImageController@getVideoImage', array($tv->video->guid)) }}"
                    alt="">

                <!-- 鼠标悬浮状态 -->
                <div class="video-action">
                    <ul class="video-action-btns">
                        <li><a class="video-info-c" href="#"><i class="icon-info-c"></i></a></li>
                        <li><a href="{{'/play/'.$tv->playid}}"><i class="icon-play-c"></i></a></li>
                    </ul>
                </div>
            </div>

            <div class="vidoe-info-wrap">
                <div class="video-title">
                    <h3><a href="{{'/play/'.$tv->playid}}">{{empty($tv->info) ? '' : $tv->info->title}}</a></h3>
                </div>
                <div class="video-meta">
                    <span class="num-view"><i class="icon-preview"></i><b>{{empty($tv->video) ? 0 :
                            $tv->video->viewed}}</b></span>
                    <span class="num-like"><i class="icon-love"></i><b>{{$tv->favoriteCount}}</b></span>
                </div>
            </div>

            <!-- tips -->
            <div class="tips tips-top hidden">
                <div class="tip-inner">
                    <div class="tip-title">
                        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
                        <h4>简介</h4>
                    </div>
                    <div class="tip-content">
                        <p>{{empty($tv->info) ? '' : $tv->info->description}}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- 翻页 -->
@include('layout.pagination', array('data' => $teacher->videos))
@stop