@extends('layout.reset')

@section('title') 忘记密码 @stop

@section('js')
<script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script>
<script src="/static/js/lib/blockUI/jquery.blockUI.js"></script>
<script>
    $(document).ready(function(){
        var commentErrorMsg = '请检查邮箱是否已注册';
        $("#btnSend").click(function(){
            var email = $("#email").val();
            $.blockUI({message:'<img src="/static/hiho-edu/img/busy.gif"/>'});
            $.post(
                '/forgot',
                {email:email},
                function(data){
                    $.unblockUI();
                    var obj = $.parseJSON(data);
                    if(obj.status == 0) {
                        location.href = '/resetSendSuccess?email=' + obj.email;
                    }
                    else if(obj.status == -2){
                        $('#errorMsg').text(obj.message);
                        $("#email").val('').focus();
                        $(".error").show(200).delay(5000).hide(200);
                    }
                    else{
                        $('#errorMsg').text(commentErrorMsg);
                        $("#email").val('').focus();
                        $(".error").show(200).delay(5000).hide(200);
                    }
                }
            );
        });
        $("#email").focus(function(){
            $(this).keydown(function(e){
                if(e.keyCode == 13){
                    $("#btnSend").click();
                }
            });
        });
        $(".close").click(function(){
            $(".notification").hide();
        });
    });
</script>
@stop

@section('content')
<div class="content">
    <h1>哇哦，密码忘记啦？</h1>
    <div class="input-w">
        <input id="email" type="email" placeholder="请输入您的注册邮箱">
        <button id="btnSend" type="submit">提交</button>
    </div>
</div>
<div class="footer">
    <p class="desc">我们将发送一封重置密码的邮件给您, 稍后请查看您的邮箱</p>
</div>

<!--  错误 -->
<div class="notification error hidden">
    <div class="inner">
        <div class="notify-icon"><i class="icon-del-c"></i></div>
        <h2 style="text-align: left;">操作失败</h2>
        <p style="text-align: left;" id="errorMsg">请检查邮箱是否已注册</p>
        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
    </div>
</div>
@stop