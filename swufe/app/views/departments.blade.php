@extends('layout.master1')

@section('title') {{'院系机构 西南财经大学－教材资料馆'}} @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
    $(document).ready(function(){
        $("#header>nav>ul>li").eq(2).addClass("current");
    });
</script>
@stop

@section('content')
<div class="top-title">
    <div class="content-inner">
        <h2 class="color-icon-title">
            <i class="color-icon color-icon-dept"></i><span>院系</span>
        </h2>
    </div>
</div>

<div class="main">
    <div class="content-inner">
        <div class="dept-card-wrap">
            @foreach($departments as $k => $d)
            <!-- 院系卡片 -->
            <div class="dept-card">
                <div class="title">
                    <a href="/department/{{$d->id}}" class="line-button">查看</a>
                    <h2>{{$d->name}}</h2>
                </div>
                <ul class="info">
                    @foreach($d->subDepartments as $sub)
                    <li><a href="/department/{{$sub->id}}">{{$sub->name}}</a></li>
                    @endforeach
                </ul>
                <div class="teacher-list">
                    <ul>
                        @foreach($d->teachers as $t)
                        <li>
                            <div class="avatar">
                                <a href="{{action('TeacherController@detail',$t->id)}}">
                                    <img src="{{$t->portrait_src}}" alt="{{$t->name}}">
                                </a>
                            </div>
                            <a href="{{action('TeacherController@detail',$t->id)}}">
                                <span class="name">{{$t->name}}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
        <!-- 翻页 -->
        @include('layout.pagination', array('data' => $departments))
    </div>
</div>
@stop