<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield("title")</title>
    <link rel="icon" href="{{\Config::get('app.pathToSource')}}/img/favicon.ico">
    <link rel="stylesheet" href="{{\Config::get('app.pathToSource')}}/stylesheets/icon.css">
    <link rel="stylesheet" href="{{\Config::get('app.pathToSource')}}/stylesheets/player.css">
    <!--[if lt IE 9]>
    <script src="js/html5shiv.min.js"></script>
    <script type="text/javascript" src="js/selectivizr-min.js"></script>
    <![endif]-->
    <!-- <script type="text/javascript" src="js/modernizr.2.8.2.js"></script> -->
    @yield("css_head")
    @yield("js_head")
</head>
<body id="@yield('body_id','player')">

@yield('content')

@section('js_foot')
@show

</body>
</html>