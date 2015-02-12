@extends('layout.master1')

@section('title') {{'个人设置 西南财经大学－教材资料馆'}} @stop

@section('content')
<div class="top-title">
    <div class="content-inner">
        <h2 class="color-icon-title">
            <i class="color-icon color-icon-user"></i><span>个人设置</span>
        </h2>
    </div>
</div>

<div class="main profile-main">
    <div class="content-inner">
        <div class="profile">
            <div class="form">
                <div class="form-row">
                    <div class="avatar-upload">
                        <img src="{{$user->avatar}}">
                    </div>
                </div>
                <div class="form-row form-title">
                    <h3>个人资料</h3>
                </div>

                <div class="form-row">
                    <div class="input-label">
                        <span>昵称</span>
                    </div>
                    <!-- 如果表单验证正确请添加 "input-ok" class  -->
                    <div class="input-field input-text">
                        <input id="txtName" type="text" value="{{$user->nickname}}">
                        <p class="info error-info hidden"></p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-label">
                        <span>邮箱</span>
                    </div>
                    <!-- 如果表单验证正确请添加 "input-ok" class  -->
                    <div class="input-field input-text">
                        <input id="txtEmail" type="email" value="{{$user->email}}">
                        <p class="info error-info hidden"></p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-label">
                        <span>手机号</span>
                    </div>
                    <!-- 如果表单验证正确请添加 "input-ok" class  -->
                    <div class="input-field input-text">
                        <input id="txtMobile" maxlength="11" type="text" value="{{$user->mobile}}">
                        <p class="info error-info hidden"></p>
                    </div>
                </div>

                <div class="form-row sms-code">
                    <div class="input-label"></div>
                    <div class="input-field input-text">
                        <input id="txtCode" type="text" maxlength="11" placeholder="短信验证码">
                        <p class="info error-info hidden"></p>
                    </div>
                    <div class="input-field input-btn">
                        <button id="btnSendSms" class="">获取短信验证码</button>
                        <!-- 添加disable class 禁止点击 -->
                        <!-- <button class="disable">重新获取短信验证码(32)</button> -->
                    </div>
                </div>

                <div class="form-row form-title">
                    <h3>修改密码</h3>
                </div>
                <div class="form-row">
                    <div class="input-label">
                        <span>旧密码</span>
                    </div>
                    <!-- 如果表单验证正确请添加 "input-ok" class  -->
                    <div class="input-field input-text">
                        <input id="txtOldPw" type="password" maxlength="11" placeholder="旧密码">
                        <p class="info error-info hidden"></p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-label">
                        <span>新密码</span>
                    </div>
                    <!-- 如果表单验证正确请添加 "input-ok" class  -->
                    <div class="input-field input-text">
                        <input id="txtNewPw" type="password" maxlength="11" placeholder="新密码">
                        <p class="info error-info hidden"></p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-label">
                        <span>重复新密码</span>
                    </div>
                    <!-- 如果表单验证正确请添加 "input-ok" class  -->
                    <div class="input-field input-text">
                        <input id="txtConfirmPw" type="password" maxlength="11" placeholder="确认新密码">
                        <p class="info error-info hidden"></p>
                    </div>
                </div>

                <div class="form-row confirm-btn">
                    <button id="btnSubmit" type="submit">确定</button>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script>
function jump(count) {
    window.setTimeout(function(){
        count--;
        if(count > 0) {
            $('#num').html(count);
            jump(count);
        } else {
            location.href="/";
        }
    }, 1000);
}

function checkEmail(email) {
    var express = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (express.test(email)) {
        return true;
    }
    else return false;
}

function checkMobile(mobile) {
    var express = /^1[3|4|5|7|8][0-9]\d{8}$/;
    if (express.test(mobile)) {
        return true;
    }
    else return false;
}

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
    var infoFlag = false;
    var passwordFlag = false; // if the password field is modified, set to true

    $("#txtName,#txtEmail,#txtMobile,txtCode").keydown(function(){
        infoFlag = true;
    });

    $("#txtOldPw,#txtNewPw,#txtConfirmPw").keydown(function(){
        passwordFlag = true;
    });

    $("#btnSendSms").click(function(){
        var phone = $("#txtMobile").val();
        if (!checkMobile(phone)) {
            $("#txtMobile").parent("div.input-field").addClass("input-error").end().siblings("p").html("请输入正确的手机号码").show();
            return false;
        }

        $.post(
            '/sendSms',
            {mobile:phone,type:'modify'},
            function(data){
                var obj = $.parseJSON(data);
                if (obj.status == -5) {
                    $("#txtMobile").parent("div.input-field").addClass("input-error").end().siblings("p").html("该手机号已被注册").show();
                }
                else if (obj.status == 0) {
                    $("#txtMobile").parent("div.input-field").removeClass("input-error").addClass("input-ok").end().siblings("p").html("").hide();
                    $("#btnSendSms").addClass("disable");
                    countDown(60);
                }
            }
        );
    });

    $("#btnSubmit").click(function(){
        var info = {name:null,email:null,mobile:null,password:null,code:null,infoFlag:0};
        console.log('infoflag = ' + infoFlag);

        if (infoFlag) {
            info.infoFlag = 1;
            var name = $("#txtName").val();
            if (name.length == 0) {
                $("#txtName").parent("div.input-field").addClass("input-error")
                .end().siblings("p").html("请输入昵称").show();
                return false;
            }
            info.name = name;
            $("#txtName").parent("div.input-field").removeClass("input-error")
            .addClass("input-ok").end().siblings("p").html("").hide();

            var email = $("#txtEmail").val();
            if (!checkEmail(email)) {
                $("#txtEmail").parent("div.input-field").addClass("input-error")
                .end().siblings("p").html("请输入正确的邮箱").show();
                return false;
            }
            info.email = email;
            $("#txtEmail").parent("div.input-field").removeClass("input-error")
            .addClass("input-ok").end().siblings("p").html("").hide();

            var phone = $("#txtMobile").val();
            if (!checkMobile(phone)) {
                $("#txtMobile").parent("div.input-field").addClass("input-error")
                .end().siblings("p").html("请输入正确的手机号码").show();
                return false;
            }
            info.mobile = phone;
            $("#txtMobile").parent("div.input-field").removeClass("input-error")
            .addClass("input-ok").end().siblings("p").html("").hide();

            info.code = $("#txtCode").val();
        }

        if (passwordFlag) {
            // the password field is modified
            var oldPassword = $("#txtOldPw").val();
            var newPassword = $("#txtNewPw").val();
            var confirmPassword = $("#txtConfirmPw").val();
            if (newPassword != confirmPassword) {
                $("#txtNewPw,#txtConfirmPw")
                .parent("div.input-field").addClass("input-error")
                .end().siblings("p").html("两次输入的密码不一致").show();
                return false;
            }
            info.password = newPassword;
            $("#txtOldPw,#txtNewPw,#txtConfirmPw")
            .parent("div.input-field").removeClass("input-error")
            .addClass("input-ok").end().siblings("p").html("").hide();
        }

        $.post(
            '/my/profile',
            info,
            function(data){
                console.log(data);
                var obj = $.parseJSON(data);
                if (obj.status == 0) {
                    // $(".success").show(200).delay(5000).hide(200);
                    location.reload();
                }
                else if (obj.status == -2) {
                    $("#errorMsg").html("请先登录");
                    $(".error").show(200);
                    return false;
                }
                else if (obj.status == -3) {
                    $("#txtEmail").parent("div.input-field").addClass("input-error")
                    .end().siblings("p").html("邮箱已被注册").show();
                    return false;
                }
                else if (obj.status == -4) {
                    $("#txtMobile").parent("div.input-field").addClass("input-error")
                    .end().siblings("p").html("手机号已被注册").show();
                    return false;
                }
                else if (obj.status == -5) {
                    $("#txtCode").parent("div.input-field").addClass("input-error")
                    .end().siblings("p").html("验证码错误").show();
                    return false;
                }
                else if (obj.status == -6) {
                    $("#txtOldPw").parent("div.input-field").addClass("input-error")
                    .end().siblings("p").html("密码错误").show();
                    return false;
                }
            }
        );
    });
});
</script>
@stop