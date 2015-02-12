@extends('layout.bs3_admin_layout')

@section('title')
院系机构管理 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">教师与机构 / </span>上级院系</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">院系机构</span>
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
                        <th>固定标识</th>
                        <th>包含教师</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="5"><button class="btn btn-sm" disabled>当前顶级</button> <button class="btn btn-sm" disabled>返回顶级...</button> <a href="{{route('adminDepartments')}}"><button class="btn btn-sm">返回上级...</a></button></td>
                    </tr>
                    @foreach($salve as $s)
                    <tr>
                        <td>{{$s->id}}</td>
                        <td>{{$s->name}}</td>
                        <td>{{$s->permalink}}</td>
                        <td>{{$s->teacherCount}}</td>
                        <td>
<!--                            <a href="#" class="btn btn-xs">子学院</a>-->
                            <a href="{{route('adminDepartmentModify',array($s->id))}}" class="btn btn-xs">修改</a>
                            <a href="{{route('adminDepartmentDelete',array($s->id))}}" class="btn btn-xs btn-danger">删除</a>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop