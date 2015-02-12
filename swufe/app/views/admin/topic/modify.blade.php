@extends('layout.bs3_admin_layout')

@section('title')
主题修改 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">分类与主题 / </span>主题修改</h1>
</div> <!-- / .page-header -->
<div class="panel">
    @if (\Session::get('tips'))
    <div class="alert alert-page alert-dark">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ \Session::get('tips') }}</strong>
    </div>
    @endif
    @if (\Session::get('error_tips'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ \Session::get('error_tips') }}</strong>
    </div>
    @endif
    @if (\Session::get('success_tips'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ \Session::get('success_tips') }}</strong>
    </div>
    @endif
</div>
<div class="row">
    <div class="col-sm-12">
        <form class="panel form-horizontal" method="POST" action="">
            <div class="panel-heading">
                <span class="panel-title">主题资料</span>
            </div>
            <div class="panel-body">
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">名称</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" placeholder="名称" name="topic_name"
                               value="{{$objTopic->name}}">
                    </div>
                </div>
                <input type="hidden" name="id" value="{{$objTopic->id}}">
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