@extends('layout.master1')

@section('title') {{'笔记详情 西南财经大学－教材资料馆'}} @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="/static/js/lib/dragSort/jquery.dragsort.js"></script>
<script>
    function editSubmit(){
        $("#txtTitle").hide();
        $(".edit-wrap>h2").html($("#txtTitle").val()).show();
        var playlist_id = $("#playlist_id").val();
        var title = $("#txtTitle").val();
        $.post(
            '/note/editTitle',
            {playlist_id:playlist_id,title:title},
            function(data){
                var obj = $.parseJSON(data);
            }
        );
    }

    function editFragmentTitleSubmit(e){
        e.hide();
        e.siblings('h3').html(e.val()).show();
        var playlist_id = $("#playlist_id").val();
        var fragment_id = e.parent().parent().siblings(".note-action").children(".hiddenFragmentID").val();
        var title = e.val();
        $.post(
            '/note/editFragmentTitle',
            {playlist_id:playlist_id,fragment_id:fragment_id,title:title},
            function(data){
                var obj = $.parseJSON(data);
            }
        );
    }

    function editFragmentCommentSubmit(e){
        var content = e.val();
        e.hide();
        e.siblings("p").show().html(content);
        var playlist_id = $("#playlist_id").val();
        var fragment_id = e.parent().parent().siblings(".note-action").children(".hiddenFragmentID").val();
        $.post(
            '/note/editFragmentComment',
            {playlist_id:playlist_id,fragment_id:fragment_id,comment:content},
            function(data){
                var obj = $.parseJSON(data);
            }
        );
    }

    $(document).ready(function(){
        $("#header>nav>ul>li").eq(4).addClass("current");

        $("#btnTitle").click(function(){
            $(".edit-wrap>h2").hide();
            $("#txtTitle").show().focus().val($("#txtTitle").val());
        });
        $("#txtTitle").blur(function(){
            editSubmit();
        });
        $("#txtTitle").keydown(function(e){
            if(e.keyCode == 13){
                $("#txtTitle").blur();
            }
        });

        $(".editBtnFragmentTitle").click(function(){
            $(this).siblings("h3").hide();
            var content = $(this).siblings(".editText").val();
            $(this).siblings(".editText").show().focus().val(content);
        });
        $(".editText").blur(function(){
            editFragmentTitleSubmit($(this));
        });
        $(".editText").keydown(function(e){
            if(e.keyCode == 13){
                $(this).blur();
            }
        });

        $(".editBtnFragmentComment").click(function(){
            $(this).siblings("p").hide();
            var content = $(this).siblings("p").html();
            $(this).siblings(".textComment").show().focus().val(content);
        });
        $(".textComment").blur(function(){
            editFragmentCommentSubmit($(this));
        });
        $(".textComment").keydown(function(e){
            if(e.keyCode == 13){
                $(this).blur();
            }
        });

        $("#clickDelete").click(function(){
            if(confirm("确定要删除笔记吗？")){
                var id = $("#playlist_id").val();
                $.post(
                    '/note/delete',
                    {playlist_id:id},
                    function(data){
                        var obj = $.parseJSON(data);
                        if(obj.status == 0) {
                            location.href = '/my/note';
                        }
                        else{
                            alert(obj.message);
                        }
                    }
                );
                return false;
            }
            else return false;
        });

        $(".btnDelFragment").click(function(){
            if(confirm("确定要删除该剪辑吗？")){
                var fragment_id = $(this).siblings(".hiddenFragmentID").val();
                var playlist_id = $("#playlist_id").val();
                $.post(
                    '/note/deleteFragment',
                    {playlist_id:playlist_id,fragment_id:fragment_id},
                    function(data){
                        var obj = $.parseJSON(data);
                        if(obj.status == 0) {
                            location.reload();
                        }
                    }
                );
                return false;
            }
            else{
                return false;
            }
        });

        $("ul.dragList").dragsort({
            dragSelector:"li",
            dragBetween: false,
            placeHolderTemplate: '<li><div class="notes-item"></div></li>',
            dragEnd: function() {
                var ids = '';
                $(".note-action").each(function(){
                    var tmp = $(this).children(".hiddenFragmentID").val();
                    ids += (tmp + ',');
                });
                $.post(
                    '/note/sort',
                    {playlist_id:$("#playlist_id").val(),ids:ids}
                );
            }
        });
    });
</script>
@stop

@section('content')
<!-- 我的笔记头部标题 -->
<div class="note-header">
    <div class="content-inner">
        <div class="action">
            <a target="_blank" href="@if(count($playlist->playlistFragments)>0){{'/play/'.$playlist->playid}}@else{{'#'}} @endif">
                <i class="icon-play-c"></i>
                <span>播放笔记</span>
            </a>
            <a id="clickDelete" href="javascript:void(0);">
                <i class="icon-del-c"></i>
                <span>删除笔记</span>
            </a>
            <input type="hidden" id="playlist_id" value="{{$playlist->p->id}}"/>
        </div>
        <hgroup>
            <div class="edit-wrap">
                <h2>{{$playlist->p->title}}</h2>
                <!-- for edit mode -->
                <input id="txtTitle" class="hidden" type="text" value="{{$playlist->p->title}}">
                <a id="btnTitle" href="javascript:void(0);" class="edit">编辑</a>
            </div>
            <span>{{$playlist->p->totel_number}}个视频短片，共长{{gmdate('H时i分s秒', round($playlist->p->totel_district))}}</span>
        </hgroup>
    </div>
</div>

<div class="main note-main">
    <div class="content-inner">
        <div class="notes-wrap">
            @if(count($playlist->playlistFragments) > 0)
            <ul class="dragList">
                @foreach($playlist->playlistFragments as $fragment)
                @if($fragment->fragment)
                <li>
                    <!-- 笔记视频 -->
                    <div class="notes-item">
                        <div class="drag-btn">
<!--                            <span class="up"><i class="icon-angle-up"></i></span>-->
                            <span class="middle" style="padding-top: 45px;"><i class="icon-navicon"></i></span>
<!--                            <span class="down"><i class="icon-angle-down"></i></span>-->
                        </div>
                        <div class="note-thumb">
                            <div class="note-thumb-content">
                                <img src="{{{$fragment->fragment->cover or ''}}}" alt="">
                                <time>{{gmdate('H:i:s', (int)$fragment->fragment->end_time - (int)$fragment->fragment->start_time)}}</time>
                                <div class="mask">
                                    <a href="/play/{{$fragment->playid}}" target="_blank"><i class="icon-play-c"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="note-info">
                            <div class="edit-wrap edit-wrap-title">
                                <h3>{{$fragment->title}}</h3>
                                <!-- for edit mode -->
                                <input class="editText hidden" type="text" value="{{$fragment->title}}">
                                <a href="javascript:void(0);" class="edit editBtnFragmentTitle">编辑</a>
                                <!-- for edit mode -->
                                <!-- <a href="javascript:void(0);" class="edit">确定</a> -->
                            </div>
                            <time>{{ \Tool::dateFormat($fragment->created_at) }}</time>
                            <div class="edit-wrap edit-wrap-p">
                                <p>{{$fragment->comment}}</p>
                                <!-- for edit mode -->
                                <textarea class="textComment hidden"></textarea>
                                <a href="javascript:void(0);" class="edit editBtnFragmentComment">编辑</a>
                            </div>
                        </div>
                        <div class="note-action">
                            <a href="javascript:void(0);" class="btnDelFragment"><i class="icon-del-c"></i></a>
                            <input type="hidden" class="hiddenFragmentID" value="{{$fragment->fragment_id}}"/>
                        </div>
                    </div>
                </li>
                @endif
                @endforeach
            </ul>
            @else
            <div style="height: 62px;padding-left: 20px;padding-top: 40px;">
                {{'暂时还没有添加视频到笔记中'}}
            </div>
            @endif
        </div>

    </div>
</div>
@stop