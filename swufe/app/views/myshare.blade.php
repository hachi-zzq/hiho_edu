@extends('layout.master1')

@section('title') {{'我的分享 西南财经大学－教材资料馆'}} @stop

@section('content')
<div class="top-title">
    <div class="content-inner">
        <h2 class="color-icon-title">
            <i class="color-icon color-icon-share"></i><span>我的分享</span>
        </h2>
    </div>
</div>

<div class="main">
    <div class="content-inner">
        <div class="card-wrap">
            @foreach($data as $k => $d)
            <!-- 视频卡片-短视频 -->
            <div class="video-card clip-card">
                <div class="video-thumb">
                    <img src="{{ action('VideoImageController@getFragmentImageWithVideoGuid', array($d->fragment->guid,$d->fragment->start_time,$d->fragment->end_time,'THUMBNAIL')) }}" alt="">
                    <time class="video-time">
                        {{gmdate('H:i:s', (int)($d->fragment->end_time - $d->fragment->start_time))}}
                    </time>

                    <!-- 鼠标悬浮状态 -->
                    <div class="video-action">
                        <ul class="video-action-btns">
                            <li><a href=""><i class="icon-info-c"></i></a></li>
                            <li><a href=""><i class="icon-play-c"></i></a></li>
                        </ul>
                    </div>
                </div>

                <div class="vidoe-info-wrap">
                    <div class="video-date">
                        <time>{{\Tool::dateFormat($d->info->created_at)}}</time>
                        <span>6 段视频短片</span>
                    </div>
                    <div class="video-article">
                        <p>{{$d->info->description}}</p>
                    </div>
                    <div class="video-meta">
                        <span class="num-view"><i class="icon-preview"></i><b>{{$d->fragment->viewed}}</b></span>
                        <span class="num-like"><i class="icon-love"></i><b>{{$d->fragment->liked}}</b></span>
                    </div>
                </div>

                <!-- tips -->
                <div class="tips tips-top hidden">
                    <div class="tip-inner">
                        <div class="tip-title">
                            <a href="" class="close"><i class="icon-times"></i></a>
                            <h4>简介</h4>
                        </div>
                        <div class="tip-content">
                            <p>在这个信息社会，如果有效获取信息、管理信息和高效利用信息逐渐成为人们生存的必备技能。中国科学技术大学开设的《文献管理与信息分析》课程着眼于现代人的信息需求，特别是大学生和科研工作者的信息需求。围绕信息获取、信息管理、信息分析以及信息利用四个主题，展示如何高效利用信息，如何借助工具提升工作效率，以及在这个内容背后传递的思想、方法和理念。</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <!-- 翻页 -->
        @include('layout.pagination', array('data' => $data))
    </div>
</div>
@stop