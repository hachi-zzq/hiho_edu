<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>注册 西南财经大学－教材资料馆</title>
    <script type="text/javascript" src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
    <!--[if lt IE 9]>
    <script src="js/html5shiv.min.js"></script>
    <script type="text/javascript" src="js/selectivizr-min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="/static/js/lib/modernizr/2.7.1/modernizr.js"></script>
    <link rel="stylesheet" href="/static/hiho-edu/css/icon.css">
    <link rel="stylesheet" href="/static/hiho-edu/css/style.css">
    <script>

        function checkNickname(nickname){
            if(nickname.length == 0){
                $("#inputNickRow").addClass("input-error").children("p").html("请输入昵称").show();
                return false;
            }
            else{
                $("#inputNickRow").removeClass("input-error").addClass("input-ok").children("p").hide();
                return true;
            }
        }

        function checkEmail(email) {
            var emailExress = /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/;
            if(emailExress.test(email)) {
                $("#inputEmailRow").removeClass("input-error").addClass("input-ok").children("p").hide();
                return true;
            }
            else{
                $("#inputEmailRow").addClass("input-error").children("p").html("请输入正确的邮箱").show();
                return false;
            }
        }

        $(document).ready(function(){

            $("#txtNickname").blur(function(){
                var nickname = $(this).val();
                checkNickname(nickname);
                return false;
            });

            $("#txtEmail").blur(function(){
                var email = $(this).val();
                checkEmail(email);
                return false;
            });

            $("#btnSubmit").click(function(){
                var nickname = $("#txtNickname").val();
                var email = $("#txtEmail").val();
                if(!checkNickname(nickname)) {
                    return false;
                }

                if(!checkEmail(email)) {
                    return false;
                }

                $.post(
                    '/signup/phone_step2',
                    {nickname:nickname,email:email},
                    function(data){
                        var obj = $.parseJSON(data);
                        if(obj.status == 0) {
                            location.href = "/signup/phone/success";
                        }
                        else if (obj.status == -3 || (obj.status == -1 && obj.message == 'The email has already been taken.')) {
                            $(".error>.inner>p").html("该邮箱已被使用");
                            $(".error").show(200).delay(5000).hide(200);
                        }
                        else {
                            $(".error>.inner>p").html("请检查输入信息");
                            $(".error").show(200).delay(5000).hide(200);
                        }
                    }
                );
            });

            $(".close").click(function(){
                $(".notification").hide();
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
            <div class="step-w step2 step-progressed">
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
            <!-- 上传头像 -->
            <div class="form-row avatar-upload">
                <div class="avatar-w">
                    <img src="/static/hiho-edu/img/avatar_default.png" alt="">
                </div>
                <div class="upload-btn">
                    <span>更换头像</span>
                    <input id="uploadAvatarFile" name="uploadAvatarFile" type="file">
                </div>
            </div>
            <div class="form-row">
                <!-- 如果表单验证正确请添加 "input-ok" class  -->
                <div id="inputNickRow" class="input-field input-text">
                    <input id="txtNickname" type="text" placeholder="昵称">
                    <p class="info error-info hidden"></p>
                </div>
            </div>

            <div class="form-row">
                <!-- 如果表单验证正确请添加 "input-ok" class  -->
                <div id="inputEmailRow" class="input-field input-text">
                    <input id="txtEmail" type="email" placeholder="邮箱">
                    <p class="info error-info hidden"></p>
                </div>
            </div>

            <div class="form-row confirm-btn">
                <button id="btnSubmit">下一步</button>
            </div>
            <div class="go">
                <a href="/signup/phone/success">稍后填写</a>
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
        <h2>操作失败</h2>
        <p></p>
        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
    </div>
</div>

</body>
</html>