@extends('layout.master1')

@section('title') {{'手机版 西南财经大学－教材资料馆'}} @stop

@section('content')
<div class="main app-main">
    <div class="content-inner">
        <div class="app-left">
            <h1>西南财经大学<br>手机教材资料馆</h1>
            <div class="app-down">
                <div class="qr-code">
                    <img src="{{\Config::get('app.pathToSource')}}/img/app_qrcode.png" alt="">
                </div>
                <div class="app-btn">
                    <a href="" class="ios">iPhone版下载</a>
                    <a href="" class="android">Android版下载</a>
                </div>
            </div>
        </div>
        <div class="app-right"></div>
    </div>
</div>
@stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
    $(document).ready(function(){
        var liDom = $("#header>nav>ul>li");
        $("#header>nav>ul>li").eq(liDom.length - 1).addClass("current");
    });
</script>
@stop