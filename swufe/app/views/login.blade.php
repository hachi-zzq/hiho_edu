<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登录 西南财经大学－教材资料馆</title>
    <script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
    <script src="/static/js/lib/blockUI/jquery.blockUI.js"></script>
    <!--[if lt IE 9]>
    <script src="js/html5shiv.min.js"></script>
    <script type="text/javascript" src="js/selectivizr-min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="/static/js/lib/modernizr/2.7.1/modernizr.js"></script>
    <link rel="stylesheet" href="/static/hiho-edu/css/icon.css">
    <link rel="stylesheet" href="/static/hiho-edu/css/style.css">
    <script>
        function checkUsername(username) {
            if (username.length == 0) {
                $(".input-phone").addClass("input-error").children("p").html("请输入手机号或邮箱").show();
                return false;
            }
            else {
                $(".input-phone").removeClass("input-error").addClass("input-ok").children("p").hide();
                return true;
            }
        }

        function checkPassword(password) {
            if (password.length < 8 || password.length > 16) {
                $("#password").val('');
                $(".input-pw").addClass("input-error").children("p").html("密码长度不能低于8位").show();
                return false;
            }
            else {
                $(".input-pw").removeClass("input-error").addClass("input-ok").children("p").hide();
                return true;
            }
        }

        $(document).ready(function () {
            var commentErrorMsg = "请检查输入信息";
            $("#btnSubmit").click(function () {
                var username = $("#username").val();
                var password = $("#password").val();
                var validate_code = $("#validate_code").val();

                if (!checkUsername(username)) {
                    return false;
                }

                $.post(
                    '/login',
                    {email: username, password: password,validate_code:validate_code},
                    function (data) {
                        var obj = $.parseJSON(data);
                        if (obj.status == 0) {
                            location.href = '/';
                        }
                        else if (obj.status == 1) {
                            location.href = '/admin';
                        }else if(obj.status == -100){
                            $('#errorMsg').text(obj.message);
                            $.unblockUI();
                            $("#password").val('');
                            $("#validate_code").val('');
                            $(".error").show(200).delay(5000).hide(200);
                        }
                        else {
                            if (obj.needCode) {
                               $('#validate_row').show();
                            }
                            $('#errorMsg').text(commentErrorMsg);
                            $.unblockUI();
                            $("#password").val('');
                            $("#validate_code").val('');
                            $(".error").show(200).delay(5000).hide(200);
                        }
                        $("#validateImg").attr('src', obj.captcha);
                    }
                );
            });

            $("#username,#password").keydown(function (e) {
                if (e.keyCode == 13) {
                    $("#btnSubmit").click();
                }
            });

            $(".close").click(function () {
                $(".notification").hide();
            });

            $("#btnRefreshCode").click(function () {
                changeValidateImg();
            });
        });

        function changeValidateImg(){
            $.get(
                '/captcha?t=' + Math.random(),
                {},
                function(data){
                    $("#validateImg").attr('src',data);
                }
            );
        }
    </script>
</head>
<body id="signin" class="sign-in-up-w">
<div class="wrap">
    <div class="sign-in-up">
        <div class="header">
            <div class="brand">
                <a href="/">西南财经大学-教材资料馆</a>
            </div>
        </div>
        <div class="title">
            <h2>欢迎登录</h2>
        </div>
        <div class="form">

            <div class="form-row">
                <!-- 如果表单验证正确请添加 "input-ok" class  -->
                <div class="input-field input-text input-phone">
                    <input id="username" type="text" maxlength="64" placeholder="手机号/邮箱">

                    <p class="info error-info hidden"></p>
                </div>
            </div>
            <div class="form-row" id="password_row">
                <!-- 如果表单验证错误请添加 "input-error" class, 并添加错误提示信息 -->
                <div class="input-field input-pw">
                    <input id="password" type="password" maxlength="16" placeholder="密码">
                    <!-- 表单错误提示 -->
                    <p class="info error-info hidden"></p>
                </div>
            </div>
            <div class="form-row authcode-row" id="validate_row" style="display: none;">
                <div id="inputCodeRow" class="input-field input-text">
                    <input id="validate_code" type="text" maxlength="4" placeholder="验证码">
                </div>
                <div class="input-field authcode-img">
                    <img id="validateImg" src="{{ $captcha }}" alt="">
                </div>
                <div class="input-field authcode-btn">
                    <a id="btnRefreshCode" href="javascript:void(0);"><i class="icon-reload"></i></a>
                </div>
            </div>
            <div class="form-row rem-pw">
                <a href="/reset" class="forget-pw">忘记密码？</a>
                <label for="rem-pw">
                    <input type="checkbox" id="rem-pw">
                    记住密码
                </label>
            </div>

            <div class="form-row confirm-btn">
                <button id="btnSubmit">登录</button>
            </div>
            <div class="go">
                <a href="/signup/phone">还没有账号？立即注册</a>
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
        <h2>登录失败</h2>

        <p id="errorMsg">请检查输入信息</p>
        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
    </div>
</div>

</body>
</html>