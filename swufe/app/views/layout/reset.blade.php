<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield("title") 西南财经大学－教材资料馆</title>
    <script type="text/javascript" src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
    <!--[if lt IE 9]>
    <script src="/static/hiho-edu/js/html5shiv.min.js"></script>
    <script type="text/javascript" src="/static/hiho-edu/js/selectivizr-min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="/static/hiho-edu/js/modernizr.2.8.2.js"></script>
    @yield("js")

<!--    <link rel="icon" href="/static/hiho-edu/img/favicon.ico">-->
    <link rel="stylesheet" href="/source/dist/stylesheets/icon.css">
    <link rel="stylesheet" href="/source/dist/stylesheets/style.css">
</head>
<body id="signup" class="sign-in-up-w">
<div class="wrap">
    <div class="sign-in-up">
        <div class="header">
            <div class="brand">
                <a href="/">西南财经大学-视频资料馆<!--<img src="/static/hiho-edu/img/logo_white@2x.png" alt="">--></a>
            </div>
        </div>
        @yield("content")
    </div>
</div>

<!-- notification -->
<!-- 成功 -->
<div class="notification success" style="display: none;">
    <div class="inner">
        <div class="notify-icon"><i class="icon-ok-c"></i></div>
        <h2>登录成功</h2>
        <p>5 秒后将自动跳转到首页(其他描述信息) <a href="">立即跳转</a></p>
        <a href="" class="close"><i class="icon-times"></i></a>
    </div>
</div>
<!--  错误 -->
<div class="notification error" style="display: none;">
    <div class="inner">
        <div class="notify-icon"><i class="icon-del-c"></i></div>
        <h2>操作失败</h2>
        <p></p>
        <a href="" class="close"><i class="icon-times"></i></a>
    </div>
</div>

</body>
</html>