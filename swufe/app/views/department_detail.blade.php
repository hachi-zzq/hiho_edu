@extends('layout.master1')

@section('title') {{'院系详情 西南财经大学－教材资料馆'}} @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
    $(document).ready(function(){
        $("#header>nav>ul>li").eq(2).addClass("current");
    });
</script>
@stop

@section('content')
<div class="dept-header">
    <div class="dept-header-bg">
        <img src="/static/hiho-edu/img/department_header_bg.jpg" alt="">
    </div>

    <div class="content-inner">
        <div class="dept-header-content">
            <h2>{{$department->name}}</h2>
            <p>{{$department->description}}</p>
        </div>
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
                            <i class="color-icon color-icon-cate"></i><span>系分类</span>
                        </h3>
                        <ul class="filter-topic">
                            @if($department->parent != 0)
                            <li ><a href="/department/{{$department->parent}}" >整个学院</a></li>
                            @endif

                            @if(count($department->departments))
                                @foreach($department->departments as $d)
                                    <li @if($d->id == $department_id) class="current"  @endif><a href="/department/{{$d->id}}" >{{$d->name}}</a></li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </aside>

        <!-- 右侧内容 -->
        <div class="right">
            <div class="right-content">
                @foreach($teachers as $k => $teacher)
                <!-- 讲师卡片 -->
                <div class="teacher-card teacher-card-w">
                    <div class="teacher-avatar">
                        <div class="teacher-avatar-wrap">
                            <img src="{{$teacher->portrait_src}}" alt="{{$teacher->name}}">
                        </div>
                        <a href="/teacher/{{$teacher->id}}" class="line-button">查看</a>
                    </div>
                    <div class="teacher-info">
                        <!-- 老师视频计数 -->
                        <div class="teacher-video">
                            <a><i class="icon-video"></i> <span class="count">{{$teacher->videoCount}}</span></a>
                            <div class="tips tips-video">
                                <div class="tip-inner">
                                    <ul>
                                        @if(!empty($teacher->videos))
                                            @foreach($teacher->videos as $kk => $tv)
                                            <li>
                                                <a href="/play/{{$tv->playid}}">
                                                    <div class="thumb">
                                                        <img src="{{action('VideoImageController@getVideoImage', array($tv->guid))}}" alt="">
                                                    </div>
                                                    <div class="title">
                                                        <h3>{{$tv->title}}</h3>
                                                    </div>
                                                </a>
                                            </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <hgroup>
                            <h4>{{$teacher->name}}</h4>
                            @foreach($teacher->departments as $department)
                            <small>{{$department->name}}</small>
                            @endforeach
                        </hgroup>
                        <p>{{$teacher->description}}</p>

                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop