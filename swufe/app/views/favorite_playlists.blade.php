@extends('layout.master1')

@section('title') {{'我收藏的笔记 西南财经大学－教材资料馆'}} @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
$(document).ready(function(){
    $(".btnDeleteFavorite").click(function(){
        var playid = $(this).parent().siblings(".hiddenPlayID").val();
        $.post(
            '/favorite/delete',
            {playid:playid,type:'PLAYLIST'},
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
                <li class="active"><a href="/favorite/notes">笔记</a></li>
                <li><a href="/favorite/clips">短视频</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="main">
    <div class="content-inner">
        <div class="card-wrap">
            @foreach($favorites as $k => $f)
            <?php //p($f);return;?>
            <!-- 视频卡片-笔记 -->
            <div class="video-card note-card">
                <div class="video-thumb">
                    <img src="@if(empty($f->fragment)) {{'/static/hiho-edu/img/note_default.png'}} @else {{ action('VideoImageController@getFragmentImageWithVideoGuid', array($f->video->guid,$f->fragment->start_time,$f->fragment->end_time,'THUMBNAIL')) }} @endif" alt="">
                    <time class="video-time">
                        {{gmdate('H:i:s', $f->length)}}
                    </time>
                    <!-- 鼠标悬浮状态 -->
                    <div class="video-action">
                        <ul class="video-action-btns btns-3">
                            <li><a href="javascript:void(0);"><i class="icon-info-c"></i></a></li>
                            <li><a href="/play/{{$f->play_id}}" target="_blank"><i class="icon-play-c"></i></a></li>
                            <li><a href="javascript:void(0);" class="btnDeleteFavorite"><i class="icon-love-c"></i></a></li>
                            <input type="hidden" class="hiddenPlayID" value="{{$f->play_id}}" />
                        </ul>
                    </div>
                </div>

                <div class="vidoe-info-wrap">
                    <div class="video-date">
                        <time>{{\Tool::dateFormat($f->playlist->created_at)}}</time>
                        <span>{{$f->listCount}} 段视频短片</span>
                    </div>
                    <div class="video-title">
                        <h3><a href="/play/{{$f->play_id}}">{{$f->playlist->title}}</a></h3>
                    </div>
                    <div class="video-meta">
                        <span class="num-view"><i class="icon-preview"></i><b>{{$f->viewed}}</b></span>
                        <span class="num-like"><i class="icon-love"></i><b>{{$f->count}}</b></span>
                    </div>
                </div>

                <!-- tips -->
                <div class="tips tips-note tips-top tips-top-l hidden">
                    <div class="tip-inner">
                        <div class="tip-title">
                            <a href="" class="close"><i class="icon-times"></i></a>
                            <h4>6 段视频</h4>
                        </div>
                        <div class="tip-content">
                            <ul>
                                <li>
                                    <a href="">
                                        <div class="thumb">
                                            <img src="img/thumb_64x40.png" alt="">
                                        </div>
                                        <div class="title">
                                            <h3>计算机辅助翻译原理与实践</h3>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- 翻页 -->
@include('layout.pagination', array('data' => $favorites))
@stop