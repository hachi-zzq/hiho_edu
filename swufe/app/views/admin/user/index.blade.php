@extends('layout.bs3_admin_layout')

@section('title')
用户管理 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">用户 / </span>全部用户</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">用户</span>
            </div>
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
                        <td><a href="#" class="label {{$user->status != \HiHo\Model\User::STATUS_LOCKED ?'label-info' :'label-default'}}">{{$user->status}}</a></td>
                        <td>
                            <a href="#" class="btn btn-xs">资料</a>
                            <a href="/admin/users/loginSession/{{$user->user_id}}" target="_blank" class="btn btn-xs">登入身份</a>
                            <a href="{{route('adminUserUnlock',array($user->user_id))}}" class="btn btn-xs" {{$user->status != \HiHo\Model\User::STATUS_LOCKED ?'disabled' :''}}>解锁</a>
                            <a href="{{route('adminUserDelete',array($user->user_id))}}" class="btn btn-xs btn-danger" onclick="return confirm('确定删除该用户？');">删除</a>
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="8">
                            <div class="pagination">
                                {{ $users->links() }}
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