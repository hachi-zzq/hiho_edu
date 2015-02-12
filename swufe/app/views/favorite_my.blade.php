@extends('layout.master1')

@section('title') {{'我收藏的课程 西南财经大学－教材资料馆'}} @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
$(document).ready(function(){
    $(".icon-info-c").click(function(){
        $(this).parentsUntil(".video-card").siblings(".tips").show();
    });
    $(".close").click(function(){
        $(this).parents(".tips").hide();
    });
    $(".btnDeleteFavorite").click(function(){
        var playid = $(this).parent().siblings(".hiddenPlayID").val();
        $.post(
            '/favorite/delete',
            {playid:playid,type:'VIDEO'},
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
                <li class="active"><a href="/favorite/videos">课程</a></li>
                <li><a href="/favorite/notes">笔记</a></li>
                <li><a href="/favorite/clips">短视频</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="main">
    <div class="content-inner">
        <div class="card-wrap">
            @foreach($favorites as $k => $f)
            @if(isset($f->v))
            <!-- 视频卡片-课程 -->
            <div class="video-card">
                <div class="video-thumb">
                    <?php //p($f->v);return;?>
                    <img src="{{ ($f->v) ? action('VideoImageController@getVideoImage', array($f->v->guid)) : '/static/img/video_default.png' }}" alt="">
                    <time class="video-time">
                        {{ ($f->v) ? gmdate('H:i:s', $f->v->length) : '00:00:00'}}
                    </time>
                    <!-- 鼠标悬浮状态 -->
                    <div class="video-action">
                        <ul class="video-action-btns btns-3">
                            <li><a href="javascript:void(0)"><i class="icon-info-c"></i></a></li>
                            <li><a href="/play/{{$f->play_id}}"><i class="icon-play-c"></i></a></li>
                            <li><a href="javascript:void(0)" class="btnDeleteFavorite"><i class="icon-love-c"></i></a></li>
                            <input type="hidden" class="hiddenPlayID" value="{{$f->play_id}}" />
                        </ul>
                    </div>
                </div>

                <div class="video-author">
                    <div class="avatar">
                        <img src="{{empty($f->teacher) ? '/static/hiho-edu/img/avatar_default.png' : $f->teacher->portrait_src}}" alt="">
                    </div>
                    <div class="author-info">
                        <a href="{{empty($f->teacher) ? '#' :action('TeacherController@detail',$f->teacher->id)}}">
                            <span class="author-name">{{empty($f->teacher) ? '' : $f->teacher->name}}</span>
                        </a>
                        <a href="{{empty($f->department) ? '#' :action('DepartmentController@detail',$f->department->id)}}">
                            <span class="author-department">{{empty($f->department) ? '' : $f->department->name}}</span>
                        </a>
                    </div>
                </div>
                <div class="vidoe-info-wrap">
                    <div class="video-title">
                        <h3><a href="/play/{{$f->play_id}}">{{ ($f->info) ? $f->info->title : ''}}</a></h3>
                    </div>
                    <div class="video-meta">
                        <span class="num-view"><i class="icon-preview"></i><b>{{ ($f->v) ? $f->v->viewed : 0}}</b></span>
                        <span class="num-like"><i class="icon-love"></i><b>{{$f->count}}</b></span>
                    </div>
                </div>

                <!-- tips -->
                <div id="tips-show{{$f->play_id}}" class="tips tips-note tips-top tips-top-l hidden">
                    <div class="tip-inner">
                        <div class="tip-title">
                            <a href="javascript:void(0)" class="close"><i class="icon-times"></i></a>
                            <h4>简介</h4>
                        </div>
                        <div class="tip-content">
                            <p>{{ ($f->videoInfo) ? $f->videoInfo->description : ''}}</p>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <?php continue;?>
            @endif
            @endforeach
        </div>
        <!-- 翻页 -->
        @include('layout.pagination', array('data' => $favorites))
    </div>
</div>
@stop