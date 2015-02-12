@extends('layout.bs3_admin_layout')

@section('title')
专业管理 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">教师与机构 / </span>专业管理</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">

        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">专业列表</span>
            </div>
            <div style="padding:10px;">
                <a href="{{route('adminSpecialityAdd')}}" class="btn btn-primary" style="margin-left: 20px">添加专业</a>
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
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($specialities as $spe)
                    <tr>
                        <td>{{$spe->id}}</td>
                        <td>{{$spe->name}}</td>
                        <td>{{$spe->permalink}}</td>
                        <td>
                            <a href="{{route('adminSpecialityModify',array($spe->id))}}" class="btn btn-xs">修改</a>
                            <a href="{{route('adminSpecialityDelete',array($spe->id))}}" onclick="return confirm('确定删除该专业？');" class="btn btn-xs btn-danger">删除</a>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop