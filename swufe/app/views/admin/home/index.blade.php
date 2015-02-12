@extends('layout.bs3_admin_layout')

@section('title')
总览 - 西南财经大学
@stop

@section('content-wrapper')

<ul class="breadcrumb breadcrumb-page">
    <div class="breadcrumb-label text-light-gray">你正在:</div>
    <li><a href="#">首页</a></li>
    <li class="active"><a href="#">总览</a></li>
</ul>
<div class="page-header">

    <div class="row">
        <!-- Page header, center on small screens -->
        <h1 class="col-xs-12 col-sm-4 text-center text-left-sm"><i class="fa fa-dashboard page-header-icon"></i>&nbsp;&nbsp;总览
        </h1>

        <div class="col-xs-12 col-sm-8">
            <div class="row">
                <hr class="visible-xs no-grid-gutter-h">
                <!-- "Create project" button, width=auto on desktops -->
                <div class="pull-right col-xs-12 col-sm-auto"><a href="{{route('adminVideoUpload')}}" class="btn btn-primary btn-labeled"
                                                                 style="width: 100%;"><span
                            class="btn-label icon fa fa-plus"></span>上传视频</a></div>

                <!-- Margin -->
                <div class="visible-xs clearfix form-group-margin"></div>

                <!-- Search field -->
                <form  class="pull-right col-xs-12 col-sm-6" method="get" action="{{route('adminVideoList')}}">
                    <div class="input-group no-margin">
                        <span class="input-group-addon"
                              style="border:none;background: #fff;background: rgba(0,0,0,.05);"><i
                                class="fa fa-search"></i></span>
                        <input type="text" placeholder="搜索..." class="form-control no-padding-hr" name="title"
                               style="border:none;background: #fff;background: rgba(0,0,0,.05);">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- / .page-header -->


<div class="row">
    <div class="col-md-8">

        <div class="stat-panel">
            <div class="stat-row">
                <!-- Small horizontal padding, bordered, without right border, top aligned text -->
                <div class="stat-cell col-sm-4 padding-sm-hr bordered no-border-r valign-top">
                    <!-- Small padding, without top padding, extra small horizontal padding -->
                    <h4 class="padding-sm no-padding-t padding-xs-hr"><i class="fa fa-cloud-upload text-primary"></i>&nbsp;&nbsp;已入库
                    </h4>
                    <!-- Without margin -->
                    <ul class="list-group no-margin">
                        <!-- Without left and right borders, extra small horizontal padding, without background, no border radius -->
                        <li class="list-group-item no-border-hr padding-xs-hr no-bg no-border-radius">
                            视频 <a href="/admin/video/list"><span class="label label-pa-purple pull-right">{{$videoCount}}</span></a>
                        </li>
                        <!-- / .list-group-item -->
                        <!-- Without left and right borders, extra small horizontal padding, without background -->
                        <li class="list-group-item no-border-hr padding-xs-hr no-bg">
                            短视频 <a href="/admin/fragments/index"><span class="label label-danger pull-right">{{$fragmentCount}}</span></a>
                        </li>
                        <!-- / .list-group-item -->
                        <!-- Without left and right borders, without bottom border, extra small horizontal padding, without background -->
                        <li class="list-group-item no-border-hr no-border-b padding-xs-hr no-bg">
                            笔记 <a href="/admin/playlists/index"><span class="label label-success pull-right">{{$playlistCount}}</span></a>
                        </li>
                        <!-- / .list-group-item -->
                    </ul>
                </div>
                <!-- /.stat-cell -->
                <!-- Primary background, small padding, vertically centered text -->

            </div>

        </div>
        <!-- /.stat-panel -->
        <!-- /5. $UPLOADS_CHART -->

        <!-- Javascript -->
        <script>
            init.push(function () {
                // Easy Pie Charts
                var easyPieChartDefaults = {
                    animate: 2000,
                    scaleColor: false,
                    lineWidth: 6,
                    lineCap: 'square',
                    size: 90,
                    trackColor: '#e5e5e5'
                }
                $('#easy-pie-chart-1').easyPieChart($.extend({}, easyPieChartDefaults, {
                    barColor: PixelAdmin.settings.consts.COLORS[1]
                }));
                $('#easy-pie-chart-2').easyPieChart($.extend({}, easyPieChartDefaults, {
                    barColor: PixelAdmin.settings.consts.COLORS[1]
                }));
                $('#easy-pie-chart-3').easyPieChart($.extend({}, easyPieChartDefaults, {
                    barColor: PixelAdmin.settings.consts.COLORS[1]
                }));
            });
        </script>
        <!-- / Javascript -->

    </div>
    <!-- /6. $EASY_PIE_CHARTS -->

    <div class="col-md-4">
        <div class="row">

            <div class="col-sm-4 col-md-12">
                <div class="stat-panel">
                    <!-- Success background. vertically centered text -->
                    <div class="stat-cell valign-middle">
                        <!-- Stat panel bg icon -->
                        <i class="fa fa-play bg-icon"></i>
                        <!-- Extra large text -->
                        <a href="/admin/fragments/index"><span class="text-xlg"><strong>{{$fragmentCount}}</strong></span></a><br>
                        <!-- Big text -->
                        <span class="text-bg">短视频</span><br>
                        <!-- Small text -->
                        <span class="text-sm">今日新增短视频</span>
                    </div>
                    <!-- /.stat-cell -->
                </div>
                <!-- /.stat-panel -->

                <div class="stat-panel">
                    <!-- Success background. vertically centered text -->
                    <div class="stat-cell bg-danger valign-middle">
                        <!-- Stat panel bg icon -->
                        <i class="fa fa-comments bg-icon"></i>
                        <!-- Extra large text -->
                        <a href="/admin/comments/index"><span class="text-xlg"><strong>{{\Comment::count()}}</strong></span></a><br>
                        <!-- Big text -->
                        <span class="text-bg">评论</span><br>
                        <!-- Small text -->
                        <span class="text-sm">今日新增评论</span>
                    </div>
                    <!-- /.stat-cell -->
                </div>
                <!-- /.stat-panel -->

            </div>

            <div class="col-sm-4 col-md-12">
                <div class="stat-panel">
                    <!-- Success background. vertically centered text -->
                    <div class="stat-cell bg-info valign-middle">
                        <!-- Stat panel bg icon -->
                        <i class="fa fa-users bg-icon"></i>
                        <!-- Extra large text -->
                        <a href="/admin/users"><span class="text-xlg"><strong>{{\User::count()}}</strong></span></a><br>
                        <!-- Big text -->
                        <span class="text-bg">注册用户</span><br>
                        <!-- Small text -->
                        <span class="text-sm">全部注册用户</span>
                    </div>
                    <!-- /.stat-cell -->
                </div>
                <!-- /.stat-panel -->
            </div>

        </div>
    </div>
</div>

<!-- Page wide horizontal line -->
<hr class="no-grid-gutter-h grid-gutter-margin-b no-margin-t">

<div class="row">

<!-- 10. $SUPPORT_TICKETS ==========================================================================

			Support tickets
-->
<!-- Javascript -->
<script>
    init.push(function () {
        $('#dashboard-support-tickets .panel-body > div').slimScroll({ height: 300, alwaysVisible: true, color: '#888', allowPageScroll: true });
    })
</script>
<!-- / Javascript -->

<div class="col-md-6">

    <div class="panel panel-dark panel-light-green">
        <div class="panel-heading">
            <span class="panel-title"><i class="panel-title-icon fa fa-smile-o"></i>新注册用户</span>
        </div>
        <!-- / .panel-heading -->
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>用户名</th>
                <th>昵称</th>
                <th>邮箱</th>
                <th></th>
            </tr>
            </thead>
            <tbody class="valign-middle">

            @if(!empty($objNewUser))
            @foreach($objNewUser as  $user)
            <tr>
                <td>{{$user->user_id}}</td>
                <td>
                    <img src="{{$user->avatar ? $user->avatar : '/static/hiho-edu/img/avatar_default.png'}}" alt="" style="width:26px;height:26px;"
                         class="rounded">&nbsp;&nbsp;<a
                        href="#" title=""></a>
                </td>
                <td>{{$user->nickname}}</td>
                <td>{{$user->email}}</td>
                <td></td>
            </tr>
            @endforeach
            @endif
            </tbody>
        </table>
    </div>
    <!-- / .panel -->
</div>

<!-- Javascript -->
<script>
    init.push(function () {
        $('#dashboard-recent .panel-body > div').slimScroll({ height: 300, alwaysVisible: true, color: '#888', allowPageScroll: true });
    })
</script>
<!-- / Javascript -->

<div class="col-md-6">


<div class="panel panel-warning" id="dashboard-recent">
<div class="panel-heading">
    <span class="panel-title"><i class="panel-title-icon fa fa-fire text-danger"></i>反馈</span>
    <ul class="nav nav-tabs nav-tabs-xs">
        <li class="active">
            <a href="#dashboard-recent-comments" data-toggle="tab">评论</a>
        </li>

    </ul>
</div>
<!-- / .panel-heading -->
<div class="tab-content">

<!-- Comments widget -->

<!-- Without padding -->
<div class="widget-comments panel-body tab-pane no-padding fade active in" id="dashboard-recent-comments">
<!-- Panel padding, without vertical padding -->
<div class="panel-padding no-padding-vr">
@if(!empty($objComment))
    @foreach($objComment as $comment)

<div class="comment">
    <img src="{{$comment->userInfo?$comment->userInfo->avatar:'/static/hiho-edu/img/avatar_default.png'}}" alt="" class="comment-avatar">

    <div class="comment-body">
        <div class="comment-by">
            <a href="#" title="">{{$comment->userInfo?$comment->userInfo->email:''}}</a> 评论了 <a href="#" title="">{{$comment->videoInfo?$comment->videoInfo->title:''}}</a>
        </div>
        <div class="comment-text">
           {{htmlspecialchars($comment->content)}}
        </div>
        <div class="comment-actions">
            <a href="{{route('adminCommentModify',$comment->id)}}"><i class="fa fa-pencil"></i>编辑</a>
            <a href="{{route('adminCommentDelete',$comment->id)}}"><i class="fa fa-times"></i>删除</a>
            <span class="pull-right">{{\Tool::timeFormat(strtotime($comment->created_at))}}</span>
        </div>
    </div>
    <!-- / .comment-body -->
</div>
<!-- / .comment -->
    @endforeach
@endif




</div>
</div>
<!-- / .widget-comments -->

<!-- Threads widget -->

<!-- Without padding -->
<!-- / .panel-body -->
</div>
</div>
<!-- / .widget-threads -->
</div>
</div>

<!-- Page wide horizontal line -->
<hr class="no-grid-gutter-h grid-gutter-margin-b no-margin-t">

<div class="row">

</div>

@stop