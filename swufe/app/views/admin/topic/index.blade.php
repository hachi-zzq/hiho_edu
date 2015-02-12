@extends('layout.bs3_admin_layout')

@section('title')
主题管理 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">主题/ </span>全部主题</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">标签</span>
            </div>
            <div style="padding:10px;">
                <a href="{{route('adminTopicsCreate')}}" class="btn btn-primary" style="margin-left: 20px">添加主题</a>
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
                        <th>名称</th>
                        <th>固定连接</th>
                        <th>建立时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($topics as $topic)
                    <tr>
                        <td>
                            <input type="checkbox" id="inlineCheckbox1" value="option1">
                        </td>
                        <td>{{$topic->id}}</td>
                        <td>{{$topic->name}}</td>
                        <td>{{$topic->permalink}}</td>
                        <td>{{$topic->created_at}}</td>
                        <td>
                            <a href="{{route('adminTopicsModify',array($topic->id))}}" class="btn btn-xs">修改</a>
                            <a href="{{route('adminTopicsDelete',array($topic->id))}}" onclick="return confirm('确定删除该主题？');" class="btn btn-xs btn-danger ">删除</a>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="8">
                            <div class="pagination">
                                {{ $topics->links() }}
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