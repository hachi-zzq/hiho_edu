@extends('layout.master')

@section('title') {{'西南财经大学－教材资料馆'}} @stop

@section('content')
<div class="main index-main">
    <div class="content-inner">
        <!-- 西南财大视频资源 -->
        <section id="course-recommend">
            <div class="section-header">
                <a href="/videos?top={{$topCategories[0]->id}}" class="more">更多</a>

                <h2 class="color-icon-title">
                    <i class="color-icon color-icon-book"></i><span>课程资源</span>
                </h2>
            </div>

            <div class="section-content">
                @foreach($swufeVideos as $k => $swufeVideo)
                <!-- 视频卡片 -->
                <div class="video-card course">
                    <div class="video-thumb">
                        <img src="{{ action('VideoImageController@getVideoImage', array($swufeVideo->guid)) }}" alt="">

                        <!-- 鼠标悬浮状态 -->
                        <div class="video-action">
                            <ul class="video-action-btns">
                                <li><a href="javascript:void(0);"><i class="icon-info-c"></i></a></li>
                                <li><a href="{{'/play/'.$swufeVideo->playID}}" target="_blank"><i class="icon-play-c"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="video-author">
                        <div class="avatar">
                            <a href="{{action('TeacherController@detail',array($swufeVideo->teacher_id))}}">
                                <img src="{{$swufeVideo->teacher_avatar}}" alt="">
                            </a>
                        </div>
                        <div class="author-info">
                            <a href="{{action('TeacherController@detail',array($swufeVideo->teacher_id))}}">
                                <span class="author-name">{{empty($swufeVideo->teacher_name) ? '抱歉，暂无信息' : $swufeVideo->teacher_name}}</span>
                            </a>
                            <a href="{{action('DepartmentController@detail',array($swufeVideo->teacher_department_id))}}">
                                <span class="author-department">{{$swufeVideo->teacher_department_name}}</span>
                            </a>
                        </div>
                    </div>
                    <div class="vidoe-info-wrap">
                        <div class="video-title">
                            <h3><a href="{{'play/'.$swufeVideo->playID}}" target="_blank">{{$swufeVideo->video_title}}</a>
                            </h3>
                        </div>
                        <div class="video-meta">
                            <span class="num-view"><i class="icon-preview"></i><b>{{$swufeVideo->viewed}}</b></span>
                            <span class="num-like"><i class="icon-love"></i><b>{{$swufeVideo->favoriteCount}}</b></span>
                        </div>
                    </div>

                    <!-- tips -->
                    <div id="tips-show{{$swufeVideo->playID}}" class="tips tips-top hidden">
                        <div class="tip-inner">
                            <div class="tip-title">
                                <a href="javascript:void(0);" class="close"
                                   onclick="$('#tips-show'+'{{$swufeVideo->playID}}').addClass('hidden')"><i
                                        class="icon-times"></i></a>
                                <h4>简介</h4>
                            </div>
                            <div class="tip-content">
                                <p>{{$swufeVideo->video_description}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- 新闻 -->
        <section id="course-recent">
            <div class="section-header">
                <a href="/videos" class="more">更多</a>
                <h2 class="color-icon-title">
                    <i class="color-icon color-icon-cup"></i><span>校园新闻</span>
                </h2>
            </div>
            <div class="section-content">
                @foreach($newsVideos as $k => $newsVideo)
                <!-- 视频卡片 -->
                <div class="video-card course">
                    <div class="video-thumb">
                        <img src="{{ action('VideoImageController@getVideoImage', array($newsVideo->guid)) }}" alt="">

                        <!-- 鼠标悬浮状态 -->
                        <div class="video-action">
                            <ul class="video-action-btns">
                                <li><a href="javascript:void(0)"><i class="icon-info-c icon-info-cx"></i></a></li>
                                <li><a href="/play/{{$newsVideo->playID}}" target="_blank"><i class="icon-play-c"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="video-author">
                        <div class="avatar">
                            <a href="{{action('TeacherController@detail', $newsVideo->teacher_id)}}">
                                <img
                                    src="{{{$newsVideo->teacher_avatar or \Config::get('app.pathToSource') . '/img/avatar_no_name.png'}}}"
                                    alt="">
                            </a>
                        </div>
                        <div class="author-info">
                            <a href="{{action('TeacherController@detail', $newsVideo->teacher_id)}}">
                                <span class="author-name">{{{empty($newsVideo->teacher_name) ? '抱歉，暂无信息' : $newsVideo->teacher_name}}}</span>
                            </a>
                            <a href="{{empty($newest->department) ? '##' :action('DepartmentController@detail',$newsVideo->teacher_department_id)}}">
                                <span class="author-department">{{$newsVideo->teacher_department_name}}</span>
                            </a>
                        </div>
                    </div>
                    <div class="vidoe-info-wrap">
                        <div class="video-title">
                            <h3><a href="{{'play/'.$newsVideo->playID}}" target="_blank">{{$newsVideo->video_title}}</a></h3>
                        </div>
                        <div class="video-meta">
                            <span class="num-view"><i class="icon-preview"></i><b>{{$newsVideo->viewed}}</b></span>
                            <span class="num-like"><i class="icon-love"></i><b>{{$newsVideo->favoriteCount}}</b></span>
                        </div>
                    </div>

                    <!-- tips -->
                    <div id="tips-show{{$newsVideo->playID}}" class="tips tips-top hidden">
                        <div class="tip-inner">
                            <div class="tip-title">
                                <a href="javascript:void(0);" class="close"
                                   onclick="$('#tips-show'+{{$newsVideo->playID}}).addClass('hidden')"><i
                                        class="icon-times"></i></a>
                                <h4>简介</h4>
                            </div>
                            <div class="tip-content">
                                <p>{{$newsVideo->title_description}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>


        <!--/ ad -->

        <!-- TED -->
        <section id="course-recent">
            <div class="section-header">
                <a href="/videos?top={{$topCategories[1]->id}}" class="more">更多</a>

                <h2 class="color-icon-title">
                    <i class="color-icon color-icon-cup"></i><span>TED</span>
                </h2>

            </div>
            <div class="section-content">
                @foreach($tedVideos as $k => $tedVideo)
                <!-- 视频卡片 -->
                <div class="video-card course">
                    <div class="video-thumb">
                        <img src="{{ action('VideoImageController@getVideoImage', array($tedVideo->guid)) }}" alt="">

                        <!-- 鼠标悬浮状态 -->
                        <div class="video-action">
                            <ul class="video-action-btns">
                                <li><a href="javascript:void(0)"><i class="icon-info-c icon-info-cx"></i></a></li>
                                <li><a href="{{'play/'.$tedVideo->playID}}" target="_blank"><i class="icon-play-c"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="video-author">
                        <div class="avatar">
                            <a href="{{action('TeacherController@detail',$tedVideo->teacher_id)}}">
                                <img
                                    src="{{{$tedVideo->teacher_avatar or \Config::get('app.pathToSource') . '/img/avatar_no_name.png'}}}"
                                    alt="">
                            </a>
                        </div>
                        <div class="author-info">
                            <a href="{{action('TeacherController@detail',$tedVideo->teacher_id)}}">
                                <span class="author-name">{{empty($tedVideo->teacher_name) ? '抱歉，暂无信息' : $tedVideo->teacher_name}}</span>
                            </a>
                            <a href="{{action('DepartmentController@detail',$tedVideo->teacher_department_id)}}">
                                <span class="author-department">{{$tedVideo->teacher_department_name}}</span>
                            </a>
                        </div>
                    </div>
                    <div class="vidoe-info-wrap">
                        <div class="video-title">
                            <h3><a href="{{'play/'.$tedVideo->playID}}" target="_blank">{{$tedVideo->video_title}}</a></h3>
                        </div>
                        <div class="video-meta">
                            <span class="num-view"><i class="icon-preview"></i><b>{{$tedVideo->viewed}}</b></span>
                            <span class="num-like"><i class="icon-love"></i><b>{{$tedVideo->favoriteCount}}</b></span>
                        </div>
                    </div>

                    <!-- tips -->
                    <div id="tips-show{{$tedVideo->playID}}" class="tips tips-top hidden">
                        <div class="tip-inner">
                            <div class="tip-title">
                                <a href="javascript:void(0);" class="close"
                                   onclick="$('#tips-show'+{{$tedVideo->playID}}).addClass('hidden')"><i
                                        class="icon-times"></i></a>
                                <h4>简介</h4>
                            </div>
                            <div class="tip-content">
                                <p>{{$tedVideo->title_description}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($ad2=\AdvertisementCall::render(2))
            <!-- ad -->
            <div class="index-ad">
                {{$ad2}}
            </div>
            @endif
        </section>

        <!-- ad -->


        <!--/ ad -->

        <!-- 其他院校视频资源 -->
        <section id="course-recent">
            <div class="section-header">
                <a href="/videos?top={{$topCategories[2]->id}}" class="more">更多</a>

                <h2 class="color-icon-title">
                    <i class="color-icon color-icon-cup"></i><span>其他院校视频资源</span>
                </h2>
            </div>
            <div class="section-content">
                @foreach($otherVideos as $k => $otherVideo)
                <!-- 视频卡片 -->
                <div class="video-card course">
                    <div class="video-thumb">
                        <img src="{{ action('VideoImageController@getVideoImage', array($otherVideo->guid)) }}" alt="">

                        <!-- 鼠标悬浮状态 -->
                        <div class="video-action">
                            <ul class="video-action-btns">
                                <li><a href="javascript:void(0)"><i class="icon-info-c icon-info-cx"></i></a></li>
                                <li><a href="{{'play/'.$otherVideo->playID}}" target="_blank"><i class="icon-play-c"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="video-author">
                        <div class="avatar">
                            <a href="{{action('TeacherController@detail',$otherVideo->teacher_id)}}">
                                <img
                                    src="{{{$otherVideo->teacher_avatar or \Config::get('app.pathToSource') . '/img/avatar_no_name.png'}}}"
                                    alt="">
                            </a>
                        </div>
                        <div class="author-info">
                            <a href="{{action('TeacherController@detail',$otherVideo->teacher_id)}}">
                                <span class="author-name">{{empty($otherVideo->teacher_name) ? '抱歉，暂无信息' : $otherVideo->teacher_name}}</span>
                            </a>
                            <a href="{{empty($newest->department) ? '##' :action('DepartmentController@detail',$otherVideo->teacher_department_id)}}">
                                <span class="author-department">{{$otherVideo->teacher_department_name}}</span>
                            </a>
                        </div>
                    </div>
                    <div class="vidoe-info-wrap">
                        <div class="video-title">
                            <h3><a href="{{'play/'.$otherVideo->playID}}" target="_blank">{{$otherVideo->video_title}}</a></h3>
                        </div>
                        <div class="video-meta">
                            <span class="num-view"><i class="icon-preview"></i><b>{{$otherVideo->viewed}}</b></span>
                            <span class="num-like"><i class="icon-love"></i><b>{{$otherVideo->favoriteCount}}</b></span>
                        </div>
                    </div>

                    <!-- tips -->
                    <div id="tips-show{{$otherVideo->playID}}" class="tips tips-top hidden">
                        <div class="tip-inner">
                            <div class="tip-title">
                                <a href="javascript:void(0);" class="close"
                                   onclick="$('#tips-show'+{{$otherVideo->playID}}).addClass('hidden')"><i
                                        class="icon-times"></i></a>
                                <h4>简介</h4>
                            </div>
                            <div class="tip-content">
                                <p>{{$otherVideo->title_description}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($ad3=\AdvertisementCall::render(3))
            <div class="index-ad">
                {{$ad3}}
            </div>
            @endif
        </section>

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
        $(".icon-info-c").mouseenter(function(){
            $(this).parentsUntil(".video-card").siblings(".tips").show().css('z-index', 1);
        });
        $(".icon-info-c").mouseleave(function(){
            $(this).parentsUntil(".video-card").siblings(".tips").hide();
        });
    });
</script>
@stop