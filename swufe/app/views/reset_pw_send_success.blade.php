@extends('layout.reset')

@section('title') 忘记密码 @stop

@section('content')

<div class="content">
    <h1><i class="icon-ok-c"></i> <span> 发送成功，请查看您的邮箱。</span></h1>
    <p class="desc">我们给<b>{{$data['email']}}</b>发送了一封密码重置邮件。<br>没有收到？请检查您的垃圾邮件。</p>
</div>

@stop