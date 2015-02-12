@extends('layout.master1')

@section('title') {{'我的笔记 西南财经大学－教材资料馆'}} @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script src="/static/js/lib/artDialog/dialog.js"></script>
<script>
    $(document).ready(function(){
        var current_playlist_id = '';

        var cur = parseInt($("#hiddenTopCategoriesCount").val()) + 2;
        $("#header>nav>ul>li").eq(cur).addClass("current");
        $("#createPlaylist").click(function(){
            var d = dialog({
                title: 'message',
                ok: function(){
                    var title = $("#pl_title").val();
                    $.post(
                        '/note/create',
                        {title:title},
                        function(data){
                            var obj = $.parseJSON(data);
                            if(obj.status == 0) {
                                location.reload();
                            }
                        }
                    );
                    return false;
                },
                content: '<div class="modal new-note">'
                    +       '<div class="modal-title">'
                    +           '<a i="close" href="javascript:void(0);" class="close">'
                    +               '<i class="icon-times"></i>'
                    +           '</a>'
                    +           '<h2>新建笔记</h2>'
                    +       '</div>'
                    +       '<div class="modal-content">'
                    +           '<div class="mc-row">'
                    +               '<div class="mc-label">'
                    +                   '<span>笔记名称</span>'
                    +               '</div>'
                    +               '<div class="mc-input">'
                    +                   '<input id="pl_title" maxlength="64" type="text">'
                    +               '</div>'
                    +           '</div>'
                    +       '</div>'
                    +       '<div class="modal-btns">'
                    +           '<button data-id="ok" id="btnConfirm" class="button">确认</button>'
                    +           '<button data-id="cancel" class="button cancel">取消</button>'
                    +       '</div>'
                    +   '</div>'
            });
            d.showModal();
            return false;
        });
        $(".icon-info-c").click(function(){
            $(this).parentsUntil(".video-card").siblings(".tips").show();
        });
        $(".close").click(function(){
            $(this).parents(".tips").hide();
        });

        $(".btnEdit").click(function(){
            var playlist_id = $(this).parent().siblings(".hiddenPlaylistID").val();
            var title = $(this).parent().parent().parent().parent()
                .siblings(".vidoe-info-wrap").children(".video-title")
                .children("h3").children("a").html();
            var description = $(this).parent().siblings(".hiddenPlaylistDescription").val();
            var d = dialog({
                title: 'message',
                ok: function(){
                    var m_title = $("#pl_title_edit").val();
                    var m_playlist_id = playlist_id;
                    var m_playlist_desc = $("#pl_desc_edit").val();
                    $.post(
                        '/note/edit',
                        {playlist_id:m_playlist_id,title:m_title,description:m_playlist_desc},
                        function(data){
                            var obj = $.parseJSON(data);
                            if(obj.status == 0) {
                                location.reload();
                            }
                        }
                    );
                    return false;
                },
                content: '<div class="modal new-note">'
                    +       '<div class="modal-title">'
                    +           '<a i="close" href="javascript:void(0);" class="close">'
                    +               '<i class="icon-times"></i>'
                    +           '</a>'
                    +           '<h2>编辑笔记</h2>'
                    +       '</div>'
                    +       '<div class="modal-content">'
                    +           '<div class="mc-row">'
                    +               '<div class="mc-label">'
                    +                   '<span>笔记名称</span>'
                    +               '</div>'
                    +               '<div class="mc-input">'
                    +                   '<input id="pl_title_edit" maxlength="64" type="text" value="' + title + '">'
                    +               '</div>'
                    +           '</div>'
                    +           '<div class="mc-row">'
                    +               '<div class="mc-label">'
                    +                   '<span>描述</span>'
                    +               '</div>'
                    +               '<div class="mc-input">'
                    +                   '<textarea id="pl_desc_edit">' + description + '</textarea>'
                    +               '</div>'
                    +           '</div>'
                    +       '</div>'
                    +       '<div class="modal-btns">'
                    +           '<button data-id="ok" id="btnConfirm" class="button">确认</button>'
                    +           '<button data-id="cancel" class="button cancel">取消</button>'
                    +       '</div>'
                    +   '</div>'
            });
            d.showModal();
            return false;
        });

        $(".icon-del-c").click(function(){
            if(confirm("确定要删除笔记吗？")){
                var playlist_id = $(this).parent().parent().siblings(".hiddenPlaylistID").val();
                $.post(
                    "/note/delete",
                    {playlist_id:playlist_id},
                    function(data){
                        var obj = $.parseJSON(data);
                        if(obj.status == 0){
                            location.reload();
                        }
                        else{
                            alert('failed');
                        }
                    }
                );
                return false;
            }
            return false;
        });
    });
</script>
@stop

@section('content')
<div class="top-title">
    <div class="content-inner">
        <h2 class="color-icon-title">
            <i class="color-icon color-icon-note"></i><span>我的笔记</span>
        </h2>
        <div class="action">
            <a href="" id="createPlaylist" class="button btn-green">新建笔记</a><!-- /note/create -->
        </div>
    </div>
</div>

<div class="main">
    <div class="content-inner">
        <div class="card-wrap">
            @foreach($data as $k => $playlist)
            <!-- 视频卡片-笔记 -->
            <div class="video-card note-card">
                <div class="video-thumb">
                    <img src="@if(empty($playlist->fragments)){{'/static/hiho-edu/img/note_default.png'}}@else{{$playlist->first->cover}}@endif" alt="">
                    <time class="video-time">{{gmdate('H:i:s', $playlist->totel_district)}}</time>
                    <!-- 鼠标悬浮状态 -->
                    <div class="video-action">
                        <ul class="video-action-btns btns-4">
                            <li><a href="javascript:void(0);"><i class="icon-info-c"></i></a></li>
                            <li><a href="@if($playlist->count>0){{'/play/'.$playlist->playid}}@else{{'#'}}@endif"><i class="icon-play-c"></i></a></li>
                            <li><a href="/note/{{$playlist->id}}" class=""><i class="icon-edot-c"></i></a></li>
                            <li><a id="clickDelete" href="javascript:void(0);"><i class="icon-del-c"></i></a></li>
                            <input type="hidden" class="hiddenPlaylistID" value="{{$playlist->id}}"/>
                            <input type="hidden" class="hiddenPlaylistDescription" value="{{$playlist->description}}"/>
                        </ul>
                    </div>
                </div>

                <div class="vidoe-info-wrap">
                    <div class="video-date">
                        <time>{{\Tool::dateFormat($playlist->created_at)}}</time>
                        <span>{{$playlist->count}} 段视频短片</span>
                    </div>
                    <div class="video-title">
                        <h3><a href="@if($playlist->count>0){{'/play/'.$playlist->playid}}@else{{'#'}}@endif">{{$playlist->title}}</a></h3>
                    </div>
                    <div class="video-meta">
                        <span class="num-view"><i class="icon-preview"></i><b>{{$playlist->viewed}}</b></span>
                        <span class="num-like"><i class="icon-love"></i><b>{{$playlist->liked}}</b></span>
                    </div>
                </div>

                <!-- tips -->
                <div id="tips-show{{$playlist->id}}" class="tips tips-note tips-top tips-top-xl hidden">
                    <div class="tip-inner">
                        <div class="tip-title">
                            <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
                            <h4>{{$playlist->totel_number}} 段视频</h4>
                        </div>
                        <div class="tip-content">
                            <ul>
                                @foreach($playlist->fragments as $dk => $fragment)
                                @if($fragment)
                                <li>
                                    <a target="_blank" href="/play/{{$fragment->playid}}">
                                        <div class="thumb">
                                            <img src="@if($playlist->videos){{action('VideoImageController@getFragmentImageWithVideoGuid', array($playlist->videos[$dk]->guid,$fragment->start_time,$fragment->end_time,'THUMBNAIL'))}} @else {{action('VideoImageController@getFragmentImageWithVideoGuid', array(0,$fragment->start_time,$fragment->end_time,'THUMBNAIL'))}} @endif" alt="">
                                        </div>
                                        <div class="title" style="width: 185px;">
                                            <h3 style="line-height: 20px;">@if($playlist->videoInfos){{$playlist->videoInfos[$dk]->title}}@endif</h3>
                                        </div>
                                    </a>
                                </li>
                                @endif
                                @endforeach
                            </ul>
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
<input type="hidden" id="hiddenTopCategoriesCount" value="{{count($topCategories)}}" />
@stop
