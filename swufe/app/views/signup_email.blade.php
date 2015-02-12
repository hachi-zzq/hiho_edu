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
    <script type="text/javascript" src="/static/js/lib/modernizr/2.7.1/modernizr.js"></script>
    <link rel="stylesheet" href="/static/hiho-edu/css/icon.css">
    <link rel="stylesheet" href="/static/hiho-edu/css/style.css">
    <script>

        function checkEmail(email) {
            var express = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(express.test(email)) {
                $(".input-phone").removeClass("input-error").addClass("input-ok").children("p").hide();
                return true;
            }
            else {
                $(".input-phone").addClass("input-error").children("p").html("请输入正确的邮箱").show();
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
            $("#btnRefreshCode").click(function(){
                $.get(
                    '/captcha?t=' + Math.random(),
                    {},
                    function(data){
                        $("#validateImg").attr('src',data);
                    }
                );
            });

            $("#btnSubmt").click(function(){
                var email = $("#txtEmail").val();
                var password = $("#txtPassword").val();
                var code = $("#txtCode").val();

                $(".input-field").removeClass("input-error").children("p").hide();
                if(!checkEmail(email)) {
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
                    '/signup/email',
                    {email:email,password:password,code:code},
                    function(data){
                        $.unblockUI();
                        var obj = $.parseJSON(data);
                        if(obj.status == 0) {
                            location.href = "/signup/email_step2";
                        }
                        else if(obj.status == -2 || (obj.status == -1 && obj.message == 'The email has already been taken.')) {
                            $(".input-phone").addClass("input-error").children("p").html("该邮箱已被注册").show();
                            return false;
                        }
                        else if(obj.status == -3) {
                            $(".input-code").addClass("input-error").children("p").html("验证码错误").show();
                            return false;
                        }
                        else{
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
            <p class="note">请使用常用邮箱注册！推荐使用<a href="/signup/phone">手机号注册</a>。</p>
            <div class="form-row">
                <!-- 如果表单验证正确请添加 "input-ok" class  -->
                <div class="input-field input-text input-phone">
                    <input id="txtEmail" type="text" maxlength="64" placeholder="邮箱">
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
            <div class="form-row authcode-row">
                <div id="inputCodeRow" class="input-field input-text input-code">
                    <input id="txtCode" type="text" maxlength="4" placeholder="验证码">
                    <p class="info error-info hidden"></p>
                </div>
                <div class="input-field authcode-img">
                    <img id="validateImg" src="{{ $captcha }}" alt="">
                </div>
                <div class="input-field authcode-btn">
                    <a id="btnRefreshCode" href="javascript:void(0);"><i class="icon-reload"></i></a>
                </div>
            </div>
            <div class="form-row agreement">
                <label for="agreement">
                    <input type="checkbox" id="agreement">
                    我已阅读并同意<a href="">《西南财经大学用户注册协议》</a>
                </label>
            </div>
            <div class="form-row confirm-btn">
                <button id="btnSubmt">下一步</button>
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
        <h2>登录成功</h2>
        <p>5 秒后将自动跳转到首页(其他描述信息) <a href="">立即跳转</a></p>
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