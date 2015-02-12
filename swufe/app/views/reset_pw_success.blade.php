@extends('layout.reset')

@section('title') 重置密码 @stop

@section('content')
<div class="signup-step">
    <div class="step-w step1 step-progressed">
        <div class="step">
            <span class="step-dot"></span>
            <span class="step-label">输入Email或者手机号</span>
        </div>
    </div>
    <div class="step-w step2 step-progressed">
        <div class="step">
            <span class="step-dot"></span>
            <span class="step-label">重设密码</span>
        </div>
        <span class="step-line"></span>
    </div>
    <div class="step-w step3 step-progressed">
        <div class="step">
            <span class="step-dot"></span>
            <span class="step-label">完成</span>
        </div>
        <span class="step-line"></span>
    </div>
</div>

<div class="success">
    <h1><i class="icon-ok-c"></i> <span>密码修改成功！</span></h1>
    <p class="desc">点击<a href="/login">这里</a>重新登录</p>
</div>
@stop