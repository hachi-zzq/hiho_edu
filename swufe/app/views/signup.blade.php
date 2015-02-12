<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>注册 西南财经大学－教材资料馆</title>
    <!--[if lt IE 9]>
    <script src="js/html5shiv.min.js"></script>
    <![endif]-->
    <script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
    <script src="/static/js/lib/blockUI/jquery.blockUI.js"></script>
    <link rel="stylesheet" href="/static/hiho-edu/css/icon.css">
    <link rel="stylesheet" href="/static/hiho-edu/css/style.css">
    <script>
        $(document).ready(function(){

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

            $("button").click(function(){
                $.blockUI({message:'<img src="/static/hiho-edu/img/busy.gif"/>'});
                $.post(
                    '/signupverify',
                    {email:$("#email").val(),password:$("#password").val()},
                    function(data){
                        $.unblockUI();
                        var obj = $.parseJSON(data);
                        if(obj.status == 0){
                            $(".success").show(200);
                            jump(5);
                        }
                        else {
                            $("#password, #confirm_password").val('');
                            $(".error").show(200).delay(5000).hide(200);
                        }
                    }
                );
            });
            $("#email,#password,#confirm_password").focus(function(){
                $(this).keydown(function(e){
                    if(e.keyCode == 13) {
                        $("button").click();
                    }
                });
            });
            $(".close").click(function(){
                $(".notification").hide();
            });
        });
    </script>
</head>
<body id="login-reg">
<div class="wrap">
    <div class="login-w reg">
        <div class="brand">
            <a href="">西南财经大学</a>
        </div>
        <div class="login-form">
            <div class="form-row input-row">
                <input type="email" id="email" name="email" placeholder="邮箱">
            </div>
            <div class="form-row input-row">
                <input type="password" id="password" name="password" placeholder="密码">
            </div>
            <div class="form-row input-row">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="重复密码">
            </div>
            <div class="form-row">
                <button><i class="icon-check"></i></button>
            </div>
            <div class="go">
                <a href="/login">有账号？立即登录</a>
            </div>
        </div>
    </div>
</div>

<!-- notification -->
<!-- 成功 -->
<div class="notification success hidden">
    <div class="inner">
        <div class="notify-icon"><i class="icon-ok-c"></i></div>
        <h2>注册成功</h2>
        <p><span id="num">5</span>秒后将自动跳转到首页 <a href="/">立即跳转</a></p>
        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
    </div>
</div>
<!--  错误 -->
<div class="notification error hidden">
    <div class="inner">
        <div class="notify-icon"><i class="icon-del-c"></i></div>
        <h2>注册失败</h2>
        <p>请检查输入信息</p>
        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
    </div>
</div>

</body>
</html>