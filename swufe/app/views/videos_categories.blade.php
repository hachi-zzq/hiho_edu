@extends('layout.master1')

@section('title') {{'课程 西南财经大学－教材资料馆'}} @stop

@section('id') {{'id="courses"'}} @stop

@section('content')
<div class="top-title">
    <div class="content-inner">
    <ul class="sort">
        <li class="current select"><a href="javascript:void(0);" selectby="sort">最新</a></li>
        <li class="select"><a href="javascript:void(0);" selectby="sort" data="hot">最热</a></li>
    </ul>
    <h2 class="color-icon-title">
        <i class="color-icon color-icon-book"></i><span>西南财大视频资源</span>
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
                            <i class="color-icon color-icon-cate"></i><span>栏目分类</span>
                        </h3>
                        <ul class="filter-list">
                            <li class="current select"><a href="javascript:void(0);" selectby="cid">全部</a></li>
                            @foreach($selections['categories'] as $k => $category)
                            <li class="select"><a href="javascript:void(0);" selectby="cid" data="{{$category['id']}}">{{$category['name']}}</a></li>
                            @endforeach
                            <!-- <li><a href=""><i class="icon-ellipsis-h"></i></a></li> -->
                        </ul>
                    </div>
                    <div class="filter">
                        <h3 class="color-icon-title">
                            <i class="color-icon color-icon-book"></i><span>专业分类</span>
                        </h3>
                        <ul class="filter-list">
                            <li class="current select"><a href="javascript:void(0);" selectby="sid">全部</a></li>
                            @foreach($selections['specialities'] as $k => $speciality)
                            <li class="select"><a href="javascript:void(0);" selectby="sid" data="{{$speciality['id']}}">{{$speciality['name']}}</a></li>
                            @endforeach
                            <!-- <li><a href=""><i class="icon-ellipsis-h"></i></a></li> -->
                        </ul>
                    </div>
                    @if($selections['currentTopCategoryID'] == $selections['topCategories'][0]['id'])
                    <div class="filter">
                        <h3 class="color-icon-title">
                            <i class="color-icon color-icon-user"></i><span>授课老师</span>
                        </h3>
                        <ul class="filter-list">
                            <li class="current select"><a href="javascript:void(0);" selectby="tid">全部</a></li>
                            @foreach($selections['teachers'] as $k => $teacher)
                            <li class="select"><a href="javascript:void(0);" selectby="tid" data="{{$teacher['id']}}">{{$teacher['name']}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="filter">
                        <h3 class="color-icon-title">
                            <i class="color-icon color-icon-flag"></i><span>语言分类</span>
                        </h3>
                        <ul class="filter-list">
                            <li class="current select"><a href="javascript:void(0);" selectby="lang">全部</a></li>
                            <li class="select"><a href="javascript:void(0);" selectby="lang" data="zh_cn">中文</a></li>
                            <li class="select"><a href="javascript:void(0);" selectby="lang" data="en">英语</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- ad -->
            @if($ad4=\AdvertisementCall::render(4))
            <!-- ad -->
            <div class="index-ad">
                {{$ad4}}
            </div>
            @endif
            <!--/ ad -->
        </aside>
        <!-- 右侧内容 -->
        <div class="right">
            <div class="right-content">
                <div class="no-video hidden">
                    <p>暂无视频</p>
                </div>
                <div class="fillable">
                    @foreach($videos as $k => $video)
                    <!-- 横板视频卡片 -->
                    <div class="video-card-l">
                        <div class="thumb">
                            <a target="_blank" href="/play/{{$video['playID']}}">
                                <img width="250px" height="148px" src="{{ action('VideoImageController@getVideoImage', array($video['guid'])) }}" alt="">
                                <div class="hover">
                                    <i class="icon-play-c"></i>
                                </div>
                            </a>
                        </div>
                        <div class="content">
                            <span class="time">{{\Tool::dateFormat($video['created_at'])}}</span>
                            <h3><a target="_blank" href="/play/{{$video['playID']}}">{{ $video['video_title'] }}</a></h3>
                            <p>{{$video['video_description']}}</p>
                            <div class="meta">
                                <div class="teacher-avatar">
                                    <a href="{{action('TeacherController@detail', array($video['teacher_id']))}}"><img src="{{$video['teacher_avatar']}}" alt=""></a>
                                </div>
                                <div class="meta-info">
                                    <span>{{$video['teacher_name']}}</span>
                                    <span>{{$video['teacher_department_name']}}</span>
                                    <span>{{$video['speciality_name']}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(count($videos) >= \Config::get('app.videoItemsPerpage'))
                <a href="javascript:void(0);" class="load-more">加载更多</a>
                @endif
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="hiddenCurrentTopId" value="{{$selections['currentTopCategoryID']}}" />
@stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
$(document).ready(function(){
    var params = {};
    params.current = 0;

    var videosJson = '';

    function getVideosJson(params) {
        $.ajax({
            url:'/videos',
            type:'post',
            data:params,
            async:false,
            success:function(data){
                videosJson = data;
            }
        });
    }

    function dateFormat(str) {
        var reg = new RegExp("-", "g");
        var newstr = str.replace(reg, '/');
        return newstr.substr(0, 10);
    }

    function fillItemsContent(videosJson) {
        var appendHTML = '';
        var obj = $.parseJSON(videosJson);
        var videos = obj.videos;
        if(videos.length == 0) {
            console.log('no more videos');
            $(".no-video").show();
            $(".load-more").html("无更多视频").hide();
            return false;
        }
        else if(videos.length >= 6) {
            $(".load-more").html("加载更多").show();
        }
        else {
            $(".load-more").html("无更多视频").hide();
        }
        $(".no-video").hide();
        for (var i = 0; i < videos.length; i++) {
            // var title = '';
            // if (videos[i].video_title.length > 25) {
            //     console.log(videos[i].video_title.length);
            //     title = videos[i].video_title.substr(0, 25) + '...';
            // }
            // else {
            //     title = videos[i].video_title;
            // }
            var item =  '<div class="video-card-l">'
                     +      '<div class="thumb">'
                     +          '<a target="_blank" href="/play/'+ videos[i].playID + '">'
                     +              '<img width="250px" height="148px" src="' + videos[i].video_picture + '" alt="">'
                     +              '<div class="hover"><i class="icon-play-c"></i></div>'
                     +          '</a>'
                     +      '</div>'
                     +      '<div class="content">'
                     +          '<span class="time">' + dateFormat(videos[i].created_at) + '</span>'
                     +          '<h3><a target="_blank" href="/play/'+ videos[i].playID + '">' + videos[i].video_title + '</a></h3>'
                     +          '<p>' + videos[i].video_description + '</p>'
                     +          '<div class="meta">'
                     +              '<div class="teacher-avatar">'
                     +                  '<img src="' + videos[i].teacher_avatar + '" alt="">'
                     +              '</div>'
                     +              '<div class="meta-info">'
                     +                  '<span>'+ videos[i].teacher_name + '</span>'
                     +                  '<span>'+ videos[i].teacher_department_name + '</span>'
                     +                  '<span>'+ videos[i].speciality_name + '</span>'
                     +              '</div>'
                     +          '</div>'
                     +      '</div>'
                     +  '</div>';
            appendHTML += item;
        };
        $(".fillable").append(appendHTML);
    }

    $("li.select>a").click(function(){

        $(this).parent("li").siblings().removeClass("current").end().addClass("current");
        $(".load-more").html('加载更多');

        params.current = 0;
        params.top = $("#hiddenCurrentTopId").val();
        switch($(this).attr('selectby')){
            case 'sort':
                params.sort = $(this).attr("data");
                break;
            case 'cid':
                params.cid = $(this).attr("data");
                break;
            case 'sid':
                params.sid = $(this).attr("data");
                break;
            case 'tid':
                params.tid = $(this).attr("data");
                break;
            case 'lang':
                params.lang = $(this).attr("data");
                break;
            default:
                break;
        }
        // console.log(params);

        getVideosJson(params);
        $(".fillable").empty();
        // console.log(videosJson);
        fillItemsContent(videosJson);
    });
    $(".load-more").click(function(){
        params.current++;
        params.top = $("#hiddenCurrentTopId").val();
        // console.log(params);
        getVideosJson(params);
        // console.log(videosJson);
        fillItemsContent(videosJson);
    });
});
</script>
@stop