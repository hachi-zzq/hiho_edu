@extends('layout.master')

@section('title') {{'西南财经大学－教材资料馆'}} @stop

@section('content')

<div class="main index-main">
    <div class="content-inner">
        <!-- 推荐课程 -->
        <section id="course-recommend">
            <div class="section-header">
                <a href="/videos" class="more">更多</a>

                <h2 class="color-icon-title">
                    <i class="color-icon color-icon-book"></i><span>推荐课程</span>
                </h2>
            </div>

            <div class="section-content">
                @foreach($recommends as $k => $recommend )
                <!-- 视频卡片 -->
                <div class="video-card course">
                    <div class="video-thumb">
                        <img src="{{ action('VideoImageController@getVideoImage', array($recommend->guid)) }}" alt="">

                        <!-- 鼠标悬浮状态 -->
                        <div class="video-action">
                            <ul class="video-action-btns">
                                <li><a href="javascript:void(0);"><i class="icon-info-c"
                                                                     onmouseover="$('#tips-show'+'{{$recommend->playid}}').removeClass('hidden')"
                                                                     onmouseout="$('#tips-show'+'{{$recommend->playid}}').addClass('hidden')"></i></a>
                                </li>
                                <li><a href="{{'/play/'.$recommend->playid}}" target="_blank"><i
                                            class="icon-play-c"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="video-author">
                        <div class="avatar">
                            <a href="{{empty($recommend->teacher) ? '##' :action('TeacherController@detail',$recommend->teacher->id)}}">
                                <img
                                    src="{{empty($recommend->teacher) ? \Config::get('app.pathToSource') . '/img/avatar_no_name.png' : $recommend->teacher->portrait_src}}"
                                    alt="">
                            </a>
                        </div>
                        <div class="author-info">
                            <a href="{{empty($recommend->teacher) ? '##' :action('TeacherController@detail',$recommend->teacher->id)}}">
                                <span class="author-name">{{empty($recommend->teacher) ? '抱歉，暂无信息' : $recommend->teacher->name}}</span>
                            </a>
                            <a href="{{empty($recommend->department) ? '##' :action('DepartmentController@detail',$recommend->department->id)}}">
                                <span class="author-department">{{empty($recommend->department) ? '' : $recommend->department->name}}</span>
                            </a>
                        </div>
                    </div>
                    <div class="vidoe-info-wrap">
                        <div class="video-title">
                            <h3><a href="{{'play/'.$recommend->playid}}" target="_blank">{{$recommend->info->title}}</a>
                            </h3>
                        </div>
                        <div class="video-meta">
                            <span class="num-view"><i class="icon-preview"></i><b>{{$recommend->viewed}}</b></span>
                            <span class="num-like"><i class="icon-love"></i><b>{{$recommend->favoriteCount}}</b></span>
                        </div>
                    </div>

                    <!-- tips -->
                    <div id="tips-show{{$recommend->playid}}" class="tips tips-top hidden">
                        <div class="tip-inner">
                            <div class="tip-title">
                                <a href="javascript:void(0);" class="close"
                                   onclick="$('#tips-show'+'{{$recommend->playid}}').addClass('hidden')"><i
                                        class="icon-times"></i></a>
                                <h4>简介</h4>
                            </div>
                            <div class="tip-content">
                                <p>{{$recommend->info->description}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- ad -->
        <div class="index-ad">
          <a href="/"><img src="http://placehold.it/1196x100" alt=""></a>
        </div>
        <!--/ ad -->

        <!-- 最新课程 -->
        <section id="course-recent">
            <div class="section-header">
                <a href="/videos" class="more">更多</a>

                <h2 class="color-icon-title">
                    <i class="color-icon color-icon-cup"></i><span>最新课程</span>
                </h2>
            </div>
            <div class="section-content">
                @foreach($newests as $k => $newest)
                <!-- 视频卡片 -->
                <div class="video-card course">
                    <div class="video-thumb">
                        <img src="{{ action('VideoImageController@getVideoImage', array($newest->guid)) }}" alt="">

                        <!-- 鼠标悬浮状态 -->
                        <div class="video-action">
                            <ul class="video-action-btns">
                                <li><a href="javascript:void(0)"><i class="icon-info-c icon-info-cx"
                                                                    onmouseover="$('#tips-show'+'{{$newest->playid}}').removeClass('hidden')"
                                                                    onmouseout="$('#tips-show'+'{{$newest->playid}}').addClass('hidden')"></i></a>
                                </li>
                                <li><a href="{{'play/'.$newest->playid}}" target="_blank"><i
                                            class="icon-play-c"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="video-author">
                        <div class="avatar">
                            <a href="{{empty($newest->teacher) ? '##' :action('TeacherController@detail',$newest->teacher->id)}}">
                                <img
                                    src="{{empty($newest->teacher) ? \Config::get('app.pathToSource') . '/img/avatar_no_name.png' : $newest->teacher->portrait_src}}"
                                    alt="{{empty($newest->teacher) ? 'anonymous' : $newest->teacher->name}}">
                            </a>
                        </div>
                        <div class="author-info">
                            <a href="{{empty($newest->teacher) ? '##' :action('TeacherController@detail',$newest->teacher->id)}}">
                                <span
                                    class="author-name">{{empty($newest->teacher) ? '抱歉，暂无信息' : $newest->teacher->name}}</span>
                            </a>
                            <a href="{{empty($newest->department) ? '##' :action('DepartmentController@detail',$newest->department->id)}}">
                                <span class="author-department">{{empty($newest->department) ? '' : $newest->department->name}}</span>
                            </a>
                        </div>
                    </div>
                    <div class="vidoe-info-wrap">
                        <div class="video-title">
                            <h3><a href="{{'play/'.$newest->playid}}" target="_blank">{{$newest->info->title}}</a></h3>
                        </div>
                        <div class="video-meta">
                            <span class="num-view"><i class="icon-preview"></i><b>{{$newest->viewed}}</b></span>
                            <span class="num-like"><i class="icon-love"></i><b>{{$newest->favoriteCount}}</b></span>
                        </div>
                    </div>

                    <!-- tips -->
                    <div id="tips-show{{$newest->playid}}" class="tips tips-top hidden">
                        <div class="tip-inner">
                            <div class="tip-title">
                                <a href="javascript:void(0);" class="close"
                                   onclick="$('#tips-show'+{{$newest->playid}}).addClass('hidden')"><i
                                        class="icon-times"></i></a>
                                <h4>简介</h4>
                            </div>
                            <div class="tip-content">
                                <p>{{$newest->info->description}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- ad -->
        <div class="index-ad">
          <a href=""><img src="http://placehold.it/1196x100" alt=""></a>
        </div>
        <!--/ ad -->

        <!-- 明星讲师 -->
        <section id="star-teacher">
            <div class="section-header">
                <a href="/departments" class="more">更多</a>

                <h2 class="color-icon-title">
                    <i class="color-icon color-icon-user"></i><span>明星讲师</span>
                </h2>
            </div>
            <div class="section-content">
                @foreach($stars as $k => $star)
                <!-- 讲师卡片 -->
                <div class="teacher-card">
                    <div class="teacher-avatar">
                        <div class="teacher-avatar-wrap">
                            <a href="/teacher/{{$star->id}}"><img src="{{$star->portrait_src}}"
                                                                  alt="{{$star->name}}"></a>
                        </div>
                        <a href="/teacher/{{$star->id}}" class="line-button">查看</a>
                    </div>
                    <div class="teacher-info">
                        <hgroup>
                            <h4><a href="/teacher/{{$star->id}}" style="color: #232323;line-height: 20px;">{{$star->name}}</a>
                            </h4>
                            @if(!empty($star->departments))
                            @foreach($star->departments as $department)
                            <small>@if($department) {{$department->name}} @else {{''}} @endif</small>
                            @endforeach
                            @endif
                        </hgroup>
                        <p>{{$star->description}}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function(){
        $("#header>nav>ul>li").eq(0).addClass("current");
    });
</script>
@stop