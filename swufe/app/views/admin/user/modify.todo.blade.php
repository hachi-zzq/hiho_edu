@extends('layout.bs3_admin_layout')

@section('title')
修改用户资料 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">用户 / </span>修改用户资料</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <form class="panel form-horizontal">
            <div class="panel-heading">
                <span class="panel-title">用户资料</span>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputEmail2" class="col-sm-2 control-label">Email</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputEmail" placeholder="Email" name="email "
                               value="{{\Input::old('email')}}">

                        <p class="help-block">由后台添加的用户, 将不再验证 Email 地址有效性.</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputNickname" class="col-sm-2 control-label">昵称</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputNickname" placeholder="昵称" name="nikename"
                               value="{{\Input::old('nikename')}}">
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputPassword" class="col-sm-2 control-label">密码</label>

                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="inputPassword" placeholder="密码"
                               name="password" value="{{\Input::old('password')}}">

                        <p class="help-block">密码需是 6 位以上字符.</p>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputPasswordConfirm" class="col-sm-2 control-label">确认密码</label>

                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="inputPasswordConfirm" placeholder="确认密码"
                               name="password_confirm" value="{{\Input::old('password_confirm')}}">
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputIsAdmin" class="col-sm-2 control-label">是否管理员</label>

                    <div class="col-sm-10">
                        <div class="radio">
                            <label>
                                <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" class="px"
                                       checked="checked">
                                <span class="lbl">普通用户</span>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" class="px"">
                                <span class="lbl">管理员</span>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group" style="margin-bottom: 0;">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">创建</button>
                    </div>
                </div>
                <!-- / .form-group -->
            </div>
        </form>
    </div>
</div>
@stop