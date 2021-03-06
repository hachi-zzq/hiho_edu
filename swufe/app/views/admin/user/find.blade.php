@extends('layout.bs3_admin_layout')

@section('title')
查找用户 - 西南财经大学
@stop

@section('content-wrapper')

<style type="text/css">
    .col-sm-4{
        width: 8%;
    }
</style>

<div class="page-header">
    <h1><span class="text-light-gray">用户 / </span>查找用户</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">

        <div class="panel">
            @if (Session::get('tips'))
            <div class="alert alert-page alert-dark">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {{ Session::get('tips') }}
            </div>
            @endif
            @if (Session::get('error_tips'))
            <div class="alert alert-page alert-danger alert-dark">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {{ Session::get('error_tips') }}
            </div>
            @endif
            @if (Session::get('success_tips'))
            <div class="alert alert-page alert-success alert-dark">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {{ Session::get('success_tips') }}
            </div>
            @endif
            <div class="panel">
                <div class="panel-heading">
                    <span class="panel-title">查找用户</span>
                </div>

                <div class="panel-body">
                    <form method="post" action="">
                        <div class="row">
                            <div class="col-sm-4">
                                <select class="form-control" name="role">
                                    <option value="0" @if($role=='0') {{'selected'}} @endif>所有用户</option>
                                    <option value="1" @if($role=='1') {{'selected'}} @endif>普通用户</option>
                                    <option value="2" @if($role=='2') {{'selected'}} @endif>管理员</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <select class="form-control" name="field">
                                    <option value="0" @if($field=='0') {{'selected'}} @endif>查找字段</option>
                                    <option value="email" @if($field=='email') {{'selected'}} @endif>邮箱</option>
                                    <option value="nickname" @if($field=='nickname') {{'selected'}} @endif>昵称</option>
                                </select>
                            </div>
                            <div class="col-xs-2">
                                <input type="text" class="form-control" placeholder="关键字" name="keyword" value="{{isset($keyword)?$keyword:''}}">
                            </div>
                            <div class="col-sm-offset-2 ">
                                <button type="submit" class="btn btn-primary">查找</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Email</th>
                        <th>最后登录时间</th>
                        <th>注册时间</th>
                        <th>是否管理员</th>
                        <th>统计</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($users) && !empty($users))
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <input type="checkbox" id="inlineCheckbox1" value="option1">
                            </td>
                            <td>{{$user->user_id}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->last_time}}</td>
                            <td>{{$user->created_at}}</td>
                            <td>{{$user->is_admin==1?'是':'否'}}</td>
                            <td>(0)</td>
                            <td><a href="#" class="label label-info">{{$user->status}}</a></td>
                            <td>
                                <a href="#" class="btn btn-xs">资料</a>
                                <a href="/admin/users/loginSession/{{$user->user_id}}" target="_blank" class="btn btn-xs">登入身份</a>
                                <a href="{{route('adminUserDelete',array($user->user_id))}}" class="btn btn-xs btn-danger">删除</a>
                        </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td colspan="8">
                            <div class="pagination">
                                @if(isset($users) && !empty($users))
                                {{ $users->links() }}
                                @endif
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop