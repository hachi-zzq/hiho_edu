@extends('layout.reset')

@section('title') 重置密码 @stop

@section('js')
<script src="/static/js/lib/blockUI/jquery.blockUI.js"></script>
<script>
    function changeValidateImg(){
        $.get(
            '/captcha?t=' + Math.random(),
            {},
            function(data){
                $("#validateImg").attr('src',data);
            }
        );
    }

    $(document).ready(function(){
        $("#btnRefreshCode").click(function () {
            changeValidateImg();
        });

        $("#btnSubmit").click(function () {
            var account = $("#txtAccount").val();
            if(account.length == 0) {
                $(".input-phone").addClass("input-error").children("p").html("内容不能为空").show();
                return false;
            }
            var type = '';
            if(/^1/.test(account)) {
                //以数字1开头，判断是否是正确的手机号格式
                var expressPhone = /^1[3|4|5|7|8][0-9]\d{8}$/;
                if(!expressPhone.test(account)) {
                    $(".input-phone").addClass("input-error").children("p").html("请输入正确的手机号码").show();
                    return false;
                }
                type = 'mobile';
            }
            else {
                //判断是否是正确的邮箱格式
                var expressMail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if(!expressMail.test(account)) {
                    $(".input-phone").addClass("input-error").children("p").html("请输入正确的邮箱").show();
                    return false;
                }
                type = 'email';
            }
            $(".input-phone").removeClass("input-error").addClass("input-ok").children("p").html('').hide();

            var code = $("#txtCode").val();
            if(code.length == 0) {
                $(".input-code").addClass("input-error").children("p").html("验证码不能为空").show();
                return false;
            }
            $(".input-code").removeClass("input-error").addClass("input-ok").children("p").html('').hide();

            //send ajax
            $.blockUI({message:'<img src="/static/hiho-edu/img/busy.gif"/>'});
            $.post(
                '/reset',
                {account:account,code:code,type:type},
                function(data){
                    $.unblockUI();
                    var obj = $.parseJSON(data);
                    if(obj.status == 0) {
                        location.href = '/reset/mobile?mobile=' + account;
                    }
                    else if(obj.status == 1) {
                        location.href = '/reset/email_sent?email=' + account;
                    }
                    else if(obj.status == -2) {
                        $(".input-code").addClass("input-error").children("p").html("验证码不正确").show();
                        return false;
                    }
                    else if(obj.status == -4) {
                        $(".input-phone").addClass("input-error").children("p").html("该邮箱或手机号还未注册").show();
                        return false;
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
    <div class="step-w step2">
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
            <input id="txtAccount" type="text" placeholder="邮箱/手机号">
            <p class="info error-info hidden"></p>
        </div>
    </div>
    <div class="form-row authcode-row">
        <div class="input-field input-text input-code">
            <input id="txtCode" type="text" placeholder="验证码">
            <p class="info error-info hidden"></p>
        </div>
        <div class="input-field authcode-img">
            <img id="validateImg" src="{{$captcha}}" alt="">
        </div>
        <div class="input-field authcode-btn">
            <a id="btnRefreshCode" href="javascript:void(0);"><i class="icon-reload"></i></a>
        </div>
    </div>
    <div class="form-row confirm-btn">
        <button id="btnSubmit" type="submit">下一步</button>
    </div>
</div>
@stop