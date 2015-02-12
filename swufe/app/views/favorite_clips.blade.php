@extends('layout.master1')

@section('title') {{'我收藏的剪辑 西南财经大学－教材资料馆'}} @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
$(document).ready(function(){
    $(".btnDeleteFavorite").click(function(){
        var playid = $(this).parent().siblings(".hiddenPlayID").val();
        $.post(
            '/favorite/delete',
            {playid:playid,type:'FRAGMENT'},
            function(data){
                var obj = $.parseJSON(data);
                if (obj.status == 0) {
                    location.reload();
                }
                else {
                    $("#errorMsg").html("操作失败，请稍后再试");
                    $(".error").show(200).delay(5000).hide(200);
                }
            }
        );
    });
});
</script>
@stop

@section('content')
<div class="top-title">
    <div class="content-inner">
        <h2 class="color-icon-title">
            <i class="color-icon color-icon-like"></i><span>我的收藏</span>
        </h2>
        <div class="tab tab-3">
            <ul>
                <li><a href="/favorite/videos">课程</a></li>
                <li><a href="/favorite/notes">笔记</a></li>
                <li class="active"><a href="/favorite/clips">短视频</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="main">
    <div class="content-inner">
        <div class="card-wrap">
            @foreach($favorites as $f)
            <!-- 视频卡片-课程 -->
            <div class="video-card clip-card">
                <div class="video-thumb">
                    <img src="{{ action('VideoImageController@getFragmentImageWithVideoGuid', array($f->fragment->guid,$f->fragment->start_time,$f->fragment->end_time,'THUMBNAIL')) }}" alt="">
                    <time class="video-time">
                        {{gmdate('H:i:s', $f->fragment->end_time - $f->fragment->start_time)}}
                    </time>
                    <!-- 鼠标悬浮状态 -->
                    <div class="video-action">
                        <ul class="video-action-btns btns-3">
                            <li><a href="javascript:void(0)"><i class="icon-info-c"></i></a></li>
                            <li><a href="/play/{{$f->playid}}" target="_blank"><i class="icon-play-c"></i></a></li>
                            <li><a href="javascript:void(0)" class="btnDeleteFavorite"><i class="icon-love-c"></i></a></li>
                            <input type="hidden" class="hiddenPlayID" value="{{$f->playid}}" />
                        </ul>
                    </div>
                </div>

                <div class="vidoe-info-wrap">
                    <div class="video-date">
                        <time>{{ \Tool::dateFormat($f->fragment->created_at) }}</time>
                    </div>
                    <div class="video-article">
                        <p>{{$f->fragment->title}}</p>
                    </div>
                    <div class="video-meta">
                        <span class="num-view"><i class="icon-preview"></i><b>{{$f->fragment->viewed}}</b></span>
                        <span class="num-like"><i class="icon-love"></i><b>{{$f->fragment->liked}}</b></span>
                    </div>
                </div>

                <!-- tips -->
                <div class="tips tips-note tips-top tips-top-l hidden">
                    <div class="tip-inner">
                        <div class="tip-title">
                            <a href="" class="close"><i class="icon-times"></i></a>
                            <h4>简介</h4>
                        </div>
                        <div class="tip-content">
                            <p>{{''}}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <!-- 翻页 -->
        @include('layout.pagination', array('data' => $favorites))
    </div>
</div>
@stop