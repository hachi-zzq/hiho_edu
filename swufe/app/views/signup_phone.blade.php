<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>注册 西南财经大学－教材资料馆</title>
    <script type="text/javascript" src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
    <script src="/static/js/lib/blockUI/jquery.blockUI.js"></script>
    <!--[if lt IE 9]>
    <script src="js/html5shiv.min.js"></script>
    <script type="text/javascript" src="js/selectivizr-min.js"></script>
    <![endif]-->
<!--    <script type="text/javascript" src="js/modernizr.2.8.2.js"></script>-->
    <link rel="stylesheet" href="/static/hiho-edu/css/icon.css">
    <link rel="stylesheet" href="/static/hiho-edu/css/style.css">
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

        function checkMobile(mobile) {
            var express = /^1[3|4|5|7|8][0-9]\d{8}$/;
            if(express.test(mobile)) {
                $("#inputPhoneRow").removeClass("input-error").addClass("input-ok").children("p").hide();
                return true;
            }
            else{
                $("#inputPhoneRow").addClass("input-error").children("p").html("请输入正确的手机号码").show();
                $("#txtPhone").val('');
                return false;
            }
        }

        function checkPassword(password) {
            if(password.length < 6 || password.length > 16) {
                $("#txtPassword").val('');
                $("#txtPasswordConfirm").val('');
                $(".input-pw").addClass("input-error");
                $("#inputPasswordRow").children("p").html("密码长度不能低于6位").show();
                return false;
            }
            return true;
        }

        function checkCode(code) {
            if(code.length == 0) {
                $("#inputCodeRow").addClass("input-error");
                return false;
            }
            else{
                $("#inputCodeRow").removeClass("input-error").addClass("input-ok");
            }
            return true;
        }

        $(document).ready(function(){
            $("#btnSendSms").click(function(){
                var mobile = $("#txtPhone").val();
                if(!checkMobile(mobile)) {
                    return false;
                }

                $.post(
                    '/sendSms',
                    {mobile:mobile,type:'register'},
                    function(data){
                        var obj = $.parseJSON(data);
                        if(obj.status == -2) {
                            $("#inputPhoneRow").addClass("input-error").children("p").html("该手机号已被注册").show();
                        }
                        else if(obj.status == 0) {
                            $("#inputPhoneRow").removeClass("input-error").addClass("input-ok");
                            $("#btnSendSms").addClass("disable");
                            countDown(60);
                        }
                        return false;
                    }
                );
            });

            $("#btnSubmit").click(function(){
                var mobile = $("#txtPhone").val();
                var password = $("#txtPassword").val();
                var code = $("#txtCode").val();

                if(!checkMobile(mobile)) {
                    return false;
                }

                if(!checkPassword(password)) {
                    return false;
                }

                if(!checkCode(code)) {
                    return false;
                }

                $.blockUI({message:'<img src="/static/hiho-edu/img/busy.gif"/>'});
                $.post(
                    '/signup/phone',
                    {mobile:mobile,password:password,code:code},
                    function(data){
                        $.unblockUI();
                        var obj = $.parseJSON(data);
                        if(obj.status == 0){
                            location.href = "/signup/phone_step2";
                        }
                        else {
                            $("#txtPassword, #txtPasswordConfirm").val('');
                            $(".error").show(200).delay(5000).hide(200);
                        }
                    }
                );

            });
        });
    </script>
</head>
<body id="signup" class="sign-in-up-w">
<div class="wrap">
    <div class="sign-in-up">
        <div class="header">
            <div class="brand">
                <a href="/">西南财经大学-教材资料馆</a>
            </div>
        </div>
        <!-- <div class="title">
          <h2>欢迎注册</h2>
        </div> -->
        <div class="signup-step">
            <div class="step-w step1 step-progressed">
                <div class="step">
                    <span class="step-dot"></span>
                    <span class="step-label">设置登录信息</span>
                </div>
            </div>
            <div class="step-w step2">
                <div class="step">
                    <span class="step-dot"></span>
                    <span class="step-label">填写用户信息</span>
                </div>
                <span class="step-line"></span>
            </div>
            <div class="step-w step3">
                <div class="step">
                    <span class="step-dot"></span>
                    <span class="step-label">完成注册</span>
                </div>
                <span class="step-line"></span>
            </div>
        </div>
        <div class="form">
            <p class="note">请使用手机号注册！还可以使用<a href="/signup/email">邮箱注册</a>。</p>
            <div class="form-row">
                <!-- 如果表单验证正确请添加 "input-ok" class  -->
                <div id="inputPhoneRow" class="input-field input-text input-phone">
                    <input id="txtPhone" type="text" maxlength="11" placeholder="手机号">
                    <p class="info error-info hidden"></p>
                </div>
            </div>
            <div class="form-row">
                <!-- 如果表单验证错误请添加 "input-error" class, 并添加错误提示信息 -->
                <div id="inputPasswordRow" class="input-field input-pw">
                    <input id="txtPassword" type="password" maxlength="16" placeholder="密码">
                    <!-- 表单错误提示 -->
                    <p class="info error-info hidden"></p>
                </div>
            </div>
            <div class="form-row">
                <div class="input-field input-pw">
                    <input id="txtPasswordConfirm" type="password" maxlength="16" placeholder="重复密码">
                </div>
            </div>
            <div class="form-row sms-code">
                <div id="inputCodeRow" class="input-field input-text">
                    <input id="txtCode" type="text" placeholder="短信验证码">
                </div>
                <div class="input-field input-btn">
                    <button id="btnSendSms" class="">获取短信验证码</button>
                    <!-- 添加disable class 禁止点击 -->
                    <!-- <button class="disable">重新获取短信验证码(32)</button> -->
                </div>
            </div>
            <div class="form-row agreement">
                <label for="agreement">
                    <input type="checkbox" id="agreement">
                    我已阅读并同意<a href="">《西南财经大学用户注册协议》</a>
                </label>
            </div>
            <div class="form-row confirm-btn">
                <button id="btnSubmit">下一步</button>
            </div>
            <div class="go">
                <a href="/login">已有账号？立即登录</a>
            </div>
        </div>
    </div>
</div>

<!-- notification -->
<!-- 成功 -->
<div class="notification success" style="display: none;">
    <div class="inner">
        <div class="notify-icon"><i class="icon-ok-c"></i></div>
        <h2>注册成功</h2>
        <p><span id="num">5</span>秒后将自动跳转到首页 <a href="/">立即跳转</a></p>
        <a href="" class="close"><i class="icon-times"></i></a>
    </div>
</div>
<!--  错误 -->
<div class="notification error" style="display: none;">
    <div class="inner">
        <div class="notify-icon"><i class="icon-del-c"></i></div>
        <h2>注册失败</h2>
        <p>请检查输入信息</p>
        <a href="" class="close"><i class="icon-times"></i></a>
    </div>
</div>

</body>
</html>