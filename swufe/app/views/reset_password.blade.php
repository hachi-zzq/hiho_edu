@extends('layout.reset')

@section('title') 重置密码 @stop

@section('js')
<script src="/static/js/lib/blockUI/jquery.blockUI.js"></script>
<script>
    $(document).ready(function(){
        $("#btnReset").click(function(){
            var password = $("#password").val();
            var confirm_password = $("#confirm_password").val();
            var token = $("#hiddenToken").val();
            var guid = $("#hiddenGuid").val();
            $.blockUI({message:'<img src="/static/hiho-edu/img/busy.gif"/>'});
            $.post(
                '/resetPassword',
                {token:token,password:password,confirm_password:confirm_password},
                function(data) {
                    $.unblockUI();
                    var obj = $.parseJSON(data);
                    if(obj.status == 0) {
                        location.href = '/resetSuccess/' + guid;
                    }
                    else {
                        $("#password, #confirm_password").val('');
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
@stop

@section('content')
<div class="content">
    <h1>请重设您的密码</h1>
    <div class="input-w input-pw-w">
        <input id="password" type="password" placeholder="新密码">
        <input id="confirm_password" type="password" placeholder="重复新密码">
        <button id="btnReset" type="submit">重设密码</button>
    </div>
</div>
<div class="footer">
    <p class="desc">当前正在为<b>{{$user->email}}</b>账户重置密码</p>
</div>
<input type="hidden" id="hiddenToken" value="{{$token}}"/>
<input type="hidden" id="hiddenGuid" value="{{$user->guid}}"/>

<!--  错误 -->
<div class="notification error hidden">
    <div class="inner">
        <div class="notify-icon"><i class="icon-del-c"></i></div>
        <h2 style="text-align: left;">重置失败</h2>
        <p style="text-align: left;">请检查输入信息</p>
        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
    </div>
</div>
@stop