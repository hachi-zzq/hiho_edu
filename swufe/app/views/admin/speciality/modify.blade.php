@extends('layout.bs3_admin_layout')

@section('title')
修改专业 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">教师与机构 / </span>修改专业</h1>
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
        <form class="panel form-horizontal" method="POST" action="{{route('adminSpecialityModify')}}">
            <div class="panel-heading">
                <span class="panel-title">专业信息</span>
            </div>
            <div class="panel-body">
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">名称</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" placeholder="名称" name="name"
                               value="{{$objSpeciality->name}}">
                    </div>
                </div>

                <input type="hidden" name="id" value="{{$objSpeciality->id}}">
                <!-- / .form-group -->
                <div class="form-group" style="margin-bottom: 0;">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">修改</button>
                    </div>
                </div>
                <!-- / .form-group -->
            </div>
        </form>
    </div>
</div>
@stop