@extends('layout.bs3_admin_layout')

@section('title')
角色管理 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">角色 / </span>全部角色</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">角色</span>
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
                        <th>#</th>
                        <th>名称</th>
                        <th>描述</th>
                        <th>访问等级</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>{{$role->id}}</td>
                        <td>{{$role->name}}</td>
                        <td>{{$role->description}}</td>
                        <td>{{$role->access_level}}</td>
                        <td>{{$role->created_at}}</td>
                        <td>
                            <a href="{{route('adminRoleModify',array($role->id))}}" class="btn btn-xs btn-xs">修改</a>
                            <a href="{{route('adminRoleDelete',array($role->id))}}" onclick="return confirm('确定删除该角色？');" class="btn btn-xs btn-danger">删除</a>
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="8">
                            <div class="pagination">
                                {{ $roles->links() }}
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div style="padding:10px;">
                    <a href="{{route('adminRoleAdd')}}" class="btn btn-primary">创建角色</a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop