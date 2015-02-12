<!DOCTYPE html>
<html lang="zh-CN"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    {{--title--}}
    <title>@yield('title','Hiho_edu')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{--description--}}
    <meta name="description" content="@yield('description','Hiho_edu')">
    {{--keywords--}}
    <meta name="keywords" content="@yield('keywords','Hiho_edu')" />
    <style type="text/css">
        body {
            padding-top: 60px;
            padding-bottom: 40px;
        }
    </style>

    {{-- css  --}}
    @section('css')
    <link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="/bootstrap/css//bootstrap-responsive.css" rel="stylesheet">
    @show

</head>

<body>

<div class="navbar navbar-inverse navbar-fixed-top">
    {{-- header --}}
    @include('admin.includes.header')
</div>

<div class="container">

@section('content')

@show

<footer>
{{-- footer --}}
@include('admin.includes.footer')
</footer>

</div> <!-- /container -->

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
{{-- js  --}}
@section('js')
<script src="/bootstrap/js/jquery.js"></script>
<script src="/bootstrap/js/bootstrap.js"></script>
<script>
    var url = window.location.href;
    function patternMatch(patternStr){
        var pattern = new RegExp(patternStr);
        return pattern.test(url);

    }

    switch (true){
        case patternMatch('.+user.*'):
            $('#user_nav').addClass('active');
            break;
        case patternMatch('.+playlist.*'):
            $('#playlist_nav').addClass('active');
            break;
        case patternMatch('.+categor.*'):
            $('#category_nav').addClass('active');
            break;
        case patternMatch('.+video.*'):
            $('#video_nav').addClass('active');
            break;
        case patternMatch('.+fragment.*'):
            $('#fragment_nav').addClass('active');
            break;
        case patternMatch('.+department.*'):
            $('#department_nav').addClass('active');
            break;
        case patternMatch('.+teacher.*'):
            $('#teacher_nav').addClass('active');
            break;
        case patternMatch('.+comment.*'):
            $('#comment_nav').addClass('active');
            break;
        case patternMatch('.+tag.*'):
            $('#tag_nav').addClass('active');
            break;

    }
</script>
@show


</body>
</html>
