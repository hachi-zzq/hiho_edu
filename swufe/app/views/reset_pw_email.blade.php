@extends('layout.reset')

@section('title') 重置密码 @stop

@section('js')
<script src="/static/js/lib/blockUI/jquery.blockUI.js"></script>
<script>
    $(document).ready(function(){
        $("#btnSubmit").click(function(){
            var token = $("#hiddenToken").val();
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
                '/reset/email',
                {token:token,password:password},
                function(data){
                    $.unblockUI();
                    var obj = $.parseJSON(data);
                    if(obj.status == 0) {
                        location.href = '/reset/success';
                    }
                    else {
                        $(".error>.inner>p").html('');
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
<input id="hiddenToken" type="hidden" value="{{$token}}"/>
@stop