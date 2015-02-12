@extends('layout.master1')

@section('title') {{'笔记 西南财经大学－教材资料馆'}} @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
    $(document).ready(function(){
        var cur = parseInt($("#hiddenTopCategoriesCount").val()) + 1;
        $("#header>nav>ul>li").eq(cur).addClass("current");
        $(".icon-info-c").click(function(){
            $(this).parentsUntil(".video-card").siblings(".tips").show();
        });
        $(".close").click(function(){
            $(this).parents(".tips").hide();
            // console.log(dom);
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
                <li><a href="/clips">短视频</a></li>
                <li class="active"><a href="/note">笔记</a></li>
            </ul>
        </div>
        <ul class="sort">
            <li {{$input ? '' : 'class="current"'}}><a href="/note">最新</a></li>
            <li {{$input == 'hot' ? 'class="current"' : ''}}><a href="/note?order=hot">最热</a></li>
        </ul>
    </div>
</div>

<div class="main">
    <div class="content-inner">
        <div class="card-wrap">
            @foreach($playlists as $k => $playlist)
            <!-- 视频卡片-笔记 -->
            <div class="video-card note-card">
                <div class="video-thumb">
                    <img src="@if(empty($playlist->fragments[0])){{'/static/hiho-edu/img/note_default.png'}}@else{{ action('VideoImageController@getFragmentImageWithVideoGuid', array($playlist->videos[0]->guid,$playlist->fragments[0]->start_time,$playlist->fragments[0]->end_time,'THUMBNAIL')) }}@endif" alt="">
                    <time class="video-time">
                        {{gmdate('H:i:s', $playlist->totel_district)}}
                    </time>
                    <!-- 鼠标悬浮状态 -->
                    <div class="video-action">
                        <ul class="video-action-btns">
                            <li><a href="javascript:void(0);"><i class="icon-info-c"></i></a></li>
                            <li><a href="/play/{{$playlist->playid}}" target="_blank"><i class="icon-play-c"></i></a></li>
                        </ul>
                    </div>
                </div>

                <div class="vidoe-info-wrap">
                    <div class="video-date">
                        <time>{{ \Tool::dateFormat($playlist->created_at) }}</time>
                        <span>{{$playlist->count}} 段视频短片</span>
                    </div>
                    <div class="video-title">
                        <h3><a href="/play/{{$playlist->playid}}">{{$playlist->title}}</a></h3>
                    </div>
                    <div class="video-meta">
                        <span class="num-view"><i class="icon-preview"></i><b>{{$playlist->viewed}}</b></span>
                        <span class="num-like"><i class="icon-love"></i><b>{{$playlist->favoriteCount}}</b></span>
                    </div>
                </div>

                <!-- tips -->
                <div class="tips tips-note tips-top hidden">
                    <div class="tip-inner">
                        <div class="tip-title">
                            <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
                            <h4>{{$playlist->totel_number}} 段视频</h4>
                        </div>
                        <div class="tip-content">
                            <ul>
                                @foreach($playlist->fragments as $k => $fragment)
                                <li>
                                    <a href="/play/{{$fragment->playid}}" target="_blank">
                                        <div class="thumb">
                                            <img src="{{ action('VideoImageController@getFragmentImageWithVideoGuid', array($playlist->videos[$k]->guid,$fragment->start_time,$fragment->end_time,'THUMBNAIL')) }}" alt="">
                                        </div>
                                        <div class="title" style="width: 188px;height: 40px;overflow: hidden;text-overflow: ellipsis;">
                                            <h3 style="width: 188px;height: 40px;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;">{{$playlist->videoInfos[$k]->title}}</h3>
                                        </div>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
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
@include('layout.pagination', array('data' => $playlists))
@stop