@extends('layout.reset')

@section('title') 重置密码 @stop

@section('js')
<script src="/static/js/lib/blockUI/jquery.blockUI.js"></script>
<script>
    function countDown(count) {
        window.setTimeout(function(){
            count--;
            if(count > 0) {
                $("#btnSendSms").html("重新获取短信验证码(" + count + ")");
                countDown(count);
            } else {
                $("#btnSendSms").removeClass("disable").html("获取短信验证码");
            }
        }, 1000);
    }

    $(document).ready(function(){
        $("#btnSendSms").click(function(){
            var mobile = $("#txtPhone").val();
            var expressMobile = /^1[3|4|5|7|8][0-9]\d{8}$/;
            if(!expressMobile.test(mobile)) {
                console.log('mobile format error');
                return false;
            }

            $.post(
                '/sendSms',
                {mobile:mobile,type:'reset'},
                function(data){
                    var obj = $.parseJSON(data);
                    if(obj.status == -3) {
                        $(".input-phone").addClass("input-error").children("p").html("该手机号还未注册").show();
                    }
                    else if(obj.status == 0) {
                        $(".input-phone").removeClass("input-error").addClass("input-ok");
                        $("#btnSendSms").addClass("disable");
                        countDown(60);
                    }
                    return false;
                }
            );
        });

        $("#btnSubmit").click(function(){
            var mobile = $("#txtPhone").val();
            var code = $("#txtCode").val();
            console.log(code);
            if(code.length == 0) {
                $(".input-code").addClass("input-error").children("p").html("验证码不能为空").show();
                return false;
            }

            var password = $("#txtPassword").val();
            var passwordConfirm = $("#txtPasswordConfirm").val();
            if(password.length < 8 || password.length > 16) {
                $(".input-pw").addClass("input-error").children("p").html("密码长度不能低于8位").show();
                return false;
            }
            if(password != passwordConfirm) {
                $(".input-pw-confirm").addClass("input-error").children("p").html("两次密码不一致").show();
                return false;
            }

            $.blockUI({message:'<img src="/static/hiho-edu/img/busy.gif"/>'});
            $.post(
                '/reset/mobile',
                {mobile:mobile,code:code,password:password},
                function(data){
                    $.unblockUI();
                    var obj = $.parseJSON(data);
                    if(obj.status == 0) {
                        location.href = '/reset/success';
                    }
                    else {
                        $(".error>.inner>p").html(obj.message);
                        $(".error").show(200).delay(5000).hide(200);
                    }
                }
            );
        });
    });
</script>
@stop

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
    <div class="step-w step3">
        <div class="step">
            <span class="step-dot"></span>
            <span class="step-label">完成</span>
        </div>
        <span class="step-line"></span>
    </div>
</div>

<div class="form">
    <div class="form-row">
        <!-- 如果表单验证正确请添加 "input-ok" class  -->
        <div class="input-field input-text input-phone">
            <input id="txtPhone" type="text" maxlength="11" value="{{$mobile}}" readonly="readonly">
        </div>
    </div>
    <div class="form-row sms-code">
        <div class="input-field input-text input-code">
            <input id="txtCode" type="text" placeholder="短信验证码">
            <p class="info error-info hidden"></p>
        </div>
        <div class="input-field input-btn">
            <button id="btnSendSms" class="">获取短信验证码</button>
            <!-- 添加disable class 禁止点击 -->
            <!-- <button class="disable">重新获取短信验证码(32)</button> -->
        </div>
    </div>
    <div class="form-row">
        <!-- 如果表单验证正确请添加 "input-ok" class  -->
        <div class="input-field input-text input-pw">
            <input id="txtPassword" type="password" maxlength="16" placeholder="新密码">
            <p class="info error-info hidden"></p>
        </div>
    </div>
    <div class="form-row">
        <!-- 如果表单验证正确请添加 "input-ok" class  -->
        <div class="input-field input-text input-pw-confirm">
            <input id="txtPasswordConfirm" type="password" maxlength="16" placeholder="确认新密码">
            <p class="info error-info hidden"></p>
        </div>
    </div>

    <div class="form-row confirm-btn">
        <button id="btnSubmit" type="submit">确定</button>
    </div>
</div>
@stop