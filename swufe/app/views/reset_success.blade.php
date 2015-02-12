@extends('layout.reset')

@section('title') 重置密码 @stop

@section('content')
<div class="content">
    <h1><i class="icon-ok-c"></i> <span> 密码重置成功。</span></h1>
    <p class="desc"><b>{{$user->email}}</b>账户密码已经修改成功，<a href="/login">点击这里重新登录</a>。</p>
</div>
@stop