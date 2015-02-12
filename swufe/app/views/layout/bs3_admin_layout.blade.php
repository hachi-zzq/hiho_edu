<!DOCTYPE html>

<!--[if IE 8]>
<html class="ie8"> <![endif]-->
<!--[if IE 9]>
<html class="ie9 gt-ie8"> <![endif]-->
<!--[if gt IE 9]><!-->
<html lang="zh-CN" class="gt-ie8 gt-ie9 not-ie"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>@yield('title','Dashboard - HiHo')</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

    <!-- Open Sans font -->
    <link
        href="{{ asset('assets/stylesheets/fonts-family.css'); }}"
        rel="stylesheet" type="text/css">


    <!-- Pixel Admin's stylesheets -->
    <link href="{{ asset('assets/stylesheets/bootstrap.min.css'); }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/stylesheets/pixel-admin.min.css'); }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/stylesheets/widgets.min.css'); }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/stylesheets/rtl.min.css'); }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/stylesheets/themes.min.css'); }}" rel="stylesheet" type="text/css">
    <link href="/source/dist/stylesheets/outdatedBrowser.css" rel="stylesheet" type="text/css" />
    @yield('style')

    <!--[if lt IE 9]>
    <script src="{{ asset('assets/javascripts/ie.min.js'); }}"></script>
    <![endif]-->


</head>

<body class="theme-default main-menu-animated">

<script>var init = [];</script>
<!--[if !IE]> -->
<script
    type="text/javascript"> window.jQuery || document.write('<script src="/assets/javascripts/jquery.min.js">' + "<" + "/script>"); </script>
<!-- <![endif]-->
<!--[if lte IE 9]>
<script
    type="text/javascript"> window.jQuery || document.write('<script src="http://cdn.staticfile.org/jquery/1.8.3/jquery.min.js">' + "<" + "/script>"); </script>
<![endif]-->

<!-- Admin's javascripts -->
<script src="{{ asset('assets/javascripts/bootstrap.min.js'); }}"></script>
<script src="{{ asset('assets/javascripts/pixel-admin.min.js'); }}"></script>

<script type="text/javascript">
    init.push(function () {
        // Javascript code here
    })
    window.PixelAdmin.start(init);
</script>
<div id="main-wrapper">

<div id="main-navbar" class="navbar navbar-inverse" role="navigation">
    <!-- Main menu toggle -->
    <button type="button" id="main-menu-toggle"><i class="navbar-icon fa fa-bars icon"></i><span
            class="hide-menu-text">HIDE MENU</span>
    </button>

    <div class="navbar-inner">
        <!-- Main navbar header -->
        <div class="navbar-header">

            <!-- Logo -->
            <a href="/" class="navbar-brand">
                <div><img alt="西南财经大学" src="{{ asset('assets/swufe/logo-18px.png'); }}"></div>
                西南财大教材资料馆
            </a>

            <!-- Main navbar toggle -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#main-navbar-collapse"><i class="navbar-icon fa fa-bars"></i></button>

        </div>
        <!-- / .navbar-header -->

        <div id="main-navbar-collapse" class="collapse navbar-collapse main-navbar-collapse">
            <div>
                <ul class="nav navbar-nav">
                    <li>
                        <a href="/">返回前台</a>
                    </li>
                    <li>
                        <a href="/admin/dashboard">后台首页</a>
                    </li>
                </ul>
                <!-- / .navbar-nav -->

                <div class="right clearfix">
                    <ul class="nav navbar-nav pull-right right-navbar-nav">

                        <li class="nav-icon-btn nav-icon-btn-danger dropdown">
                            <a href="#notifications" class="dropdown-toggle" data-toggle="dropdown">
                                <!--                                <span class="label">0</span>-->
                                <i class="nav-icon fa fa-bullhorn"></i>
                                <span class="small-screen-text">通知</span>
                            </a>

                            <!-- Javascript -->
                            <script>
                                init.push(function () {
                                    $('#main-navbar-notifications').slimScroll({ height: 200 });
                                });
                            </script>
                            <!-- / Javascript -->

                            <div class="dropdown-menu widget-notifications no-padding" style="width: 300px">
                                <div class="notifications-list" id="main-navbar-notifications">
                                    <!-- notification -->
                                </div>
                                <!-- / .notifications-list -->
                                <a href="#" class="notifications-link">更多通知</a>
                            </div>
                            <!-- / .dropdown-menu -->
                        </li>

                        <li>
                            <form class="navbar-form pull-left" method="get" action="{{route('adminVideoList')}}">
                                <input type="text" class="form-control" name="title" placeholder="搜索...">
                            </form>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle user-menu" data-toggle="dropdown">
                                <img src="{{ \Session::get('objAdmin')->getAvatar() }}" alt="">
                                <span>{{\Session::get('objAdmin')->nickname?\Session::get('objAdmin')->nickname:\Session::get('objAdmin')->email}}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><span class="label label-warning pull-right">New</span>个人资料</a>
                                </li>
                                <li><a href="#"><span class="badge badge-primary pull-right">New</span>账户</a>
                                </li>
                                <li><a href="#"><i class="dropdown-icon fa fa-cog"></i>&nbsp;&nbsp;设置</a></li>
                                <li class="divider"></li>
                                <li><a href="{{route('adminUserLogout')}}"><i class="dropdown-icon fa fa-power-off"></i>&nbsp;&nbsp;登出</a>
                                </li>
                            </ul>
                        </li>
                        <!-- / .dropdown -->

                    </ul>
                    <!-- / .navbar-nav -->
                </div>
                <!-- / .right -->
            </div>
        </div>
        <!-- / #main-navbar-collapse -->
    </div>
    <!-- / .navbar-inner -->
</div>
<!-- / #main-navbar -->

<div id="main-menu" role="navigation">
    <div id="main-menu-inner">
        <div class="menu-content top" id="menu-content-demo">
            <div>
                <div class="text-bg"><span class="text-slim">欢迎回来</span> <br/><span class="text-semibold">{{\Session::get('objAdmin')->nickname?\Session::get('objAdmin')->nickname:\Session::get('objAdmin')->email}}</span>
                </div>

                <img src="{{\Session::get('objAdmin')->getAvatar()}}" alt="">
                <div class="btn-group">
                    <a href="#" class="btn btn-xs btn-primary btn-outline dark"><i class="fa fa-user"></i></a>
                    <a href="#" class="btn btn-xs btn-primary btn-outline dark"><i class="fa fa-cog"></i></a>
                    <a href="{{route('adminUserLogout')}}" class="btn btn-xs btn-danger btn-outline dark"><i
                            class="fa fa-power-off"></i></a>
                </div>
                <a href="#" class="close">&times;</a>
            </div>
        </div>
        <ul class="navigation">
            <li>
                <a href="/admin/dashboard"><i class="menu-icon fa fa-dashboard"></i><span class="mm-text">总览</span></a>
            </li>
            <li class="mm-dropdown">
                <a href="#"><i class="menu-icon fa fa-play-circle"></i><span class="mm-text">视频</span></a>
                <ul>
                    <li>
                        <a tabindex="-1" href="{{route('adminVideoUpload')}}"><i class="menu-icon fa fa-cloud-upload"></i><span
                                class="mm-text">上传新视频</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="{{route('adminUploadList')}}"><span class="mm-text">上传管理</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="{{route('adminVideoList')}}"><span class="mm-text">视频管理</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="{{route('adminFragments')}}"><span class="mm-text">短视频管理</span></a>
                    </li>
                    {{--
                    <li>
                        <a tabindex="-1" href="/admin/videos/"><span class="mm-text"><span class="badge badge-warning">TODO</span>字幕管理</span></a>
                    </li>
                    --}}


                </ul>
            </li>
            <li class="mm-dropdown">
                <a href="#"><i class="menu-icon fa fa-briefcase"></i><span class="mm-text">运营</span><span
                        class="badge badge-warning">TODO</span></a>
                <ul>
                    <li>
                        <a tabindex="-1" href="{{route('adminPositionAd')}}"><span class="mm-text">广告位管理</span></a>
                    </li>

                    <li>
                        <a tabindex="-1" href="{{route('adminPositionIndex')}}"><span class="mm-text">推荐位管理</span></a>
                    </li>

                    <li>
                        <a tabindex="-1" href="{{route('adminRecommendVideoList')}}"> <span class="mm-text">视频推荐管理</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="{{route('adminRecommendTeacherList')}}"> <span class="mm-text">讲师推荐管理</span></a>
                    </li>
<!--                    <li>-->
<!--                        <a tabindex="-1" href="/"><span class="mm-text">热门搜索关键词</span></a>-->
<!--                    </li>-->
                    <li>
                        <a tabindex="-1" href="{{route('adminAppDownload')}}"><i class="menu-icon fa fa-apple"></i><span
                                class="mm-text">移动端 App</span></a>
                    </li>
<!--                    <li>-->
<!--                        <a tabindex="-1" href="#"><i class="menu-icon fa fa-ban"></i><span-->
<!--                                class="mm-text">内容举报与黑名单</span></a>-->
<!--                    </li>-->
                </ul>
            </li>
            <li class="mm-dropdown">
                <a href="/admin/tags"><i class="menu-icon fa fa-tags"></i><span class="mm-text">归类索引</span> </a>
                <ul>
                    <li>
                        <a tabindex="-1" href="{{route('adminCategories')}}"><span class="mm-text">分类管理</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="{{route('adminTags')}}"><span class="mm-text">Tags 管理</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="{{route('adminTopics')}}"><span class="mm-text">主题管理</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="/admin/specialities"><span class="mm-text">专业管理</span></a>
                    </li>
                    <li class="mm-dropdown open">
                        <a href="#"><i class="menu-icon fa fa-flask"></i><span class="mm-text">教师与机构</span></a>
                        <ul class="" style="">
                            <li>
                                <a tabindex="-1" href="/admin/departments/create"><span class="mm-text">创建新机构</span></a>
                            </li>
                            <li>
                                <a tabindex="-1" href="/admin/departments"><span class="mm-text">院系机构管理</span></a>
                            </li>
                            <li>
                                <a tabindex="-1" href="/admin/teachers/create"><span class="mm-text">创建新教师</span></a>
                            </li>
                            <li>
                                <a tabindex="-1" href="/admin/teachers"><span class="mm-text">教师管理</span></a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="mm-dropdown">
                <a href="#"><i class="menu-icon fa fa-comments"></i><span class="mm-text">用户内容</span> </a>
                <ul>
                    <li>
                        <a tabindex="-1" href="{{route('adminCommentList')}}"><span class="mm-text">评论管理</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="{{route('adminPlayLists')}}"><span class="mm-text">笔记管理</span></a>
                    </li>
                </ul>
            </li>
            <li class="mm-dropdown">
                <a href="#"><i class="menu-icon fa fa-users"></i><span class="mm-text">用户</span></a>
                <ul>
                    <li>
                        <a tabindex="-1"
                           href="/admin/users?is_admin=1"><span
                                class="mm-text">全部管理员</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="/admin/users"><span class="mm-text">全部用户</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="/admin/users/find"><span class="mm-text">按条件查找用户</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="/admin/users/create"><span class="mm-text">创建新用户</span></a>
                    </li>
                    <li>
                        <a tabindex="-1" href="/admin/roles"><span class="mm-text">角色管理</span></a>
                    </li>
                </ul>
            </li>
            <li class="mm-dropdown">
                <a href="/admin/system"><i class="menu-icon fa fa-cogs"></i><span class="mm-text">系统</span><span
                        class="badge badge-warning">TODO</span></a>
                <ul>
                    <li>
                        <a tabindex="-1" href="{{route('adminSystemSetting')}}"><span class="mm-text">全局设置</span></a>
                    </li>
<!--                    <li>-->
<!--                        <a tabindex="-1" href="#"><span class="mm-text">数据统计</span></a>-->
<!--                    </li>-->
<!--                    <li>-->
<!--                        <a tabindex="-1" href="#"><span class="mm-text">搜索引擎</span></a>-->
<!--                    </li>-->
<!--                    <li>-->
<!--                        <a tabindex="-1" href="#"><span class="mm-text">缓存</span></a>-->
<!--                    </li>-->
                </ul>
            </li>
        </ul>
        <!-- / .navigation -->
        <div class="menu-content">
            <span>Power By <a href="http://www.eastiming.com" target="_blank">Eastiming</a></span>
        </div>
    </div>
    <!-- / #main-menu-inner -->
</div>
<!-- / #main-menu -->

<div id="content-wrapper">
    @section('content-wrapper')
    @show
</div>
<!-- / #content-wrapper -->
<div id="main-menu-bg"></div>
</div>
<!-- / #main-wrapper -->

<!-- ============= Outdated Browser ============= -->
<div id="outdated-wrap">
    <div id="outdated">
        <h6>哇哦，您的浏览器太旧啦!</h6>
        <p>为了更好的体验我们的网站，请升级您的浏览器吧:) <a id="btnUpdateBrowser" href="http://browsehappy.com/">升级我的浏览器 </a></p>
        <p class="last"></p>
    </div>
</div>
<script>
    $(function () {
        var Sys = {};
        var ua = navigator.userAgent.toLowerCase();
        var s;
        var outdated = document.getElementById('outdated-wrap');
        (s = ua.match(/rv:([\d.]+)\) like gecko/)) ? Sys.ie = s[1] :
            (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
                (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
                    (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
                        (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
                            (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;

        if (Sys.ie < 8) {
            outdated.style.display = 'block';
       };
    });
</script>
@section('js')
@show

</body>
</html>