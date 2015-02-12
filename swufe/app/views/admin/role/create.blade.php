@extends('layout.bs3_admin_layout')

@section('title')
创建新角色 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">角色 / </span>创建新角色</h1>
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
        <form class="panel form-horizontal" method="POST">
            <div class="panel-heading">
                <span class="panel-title">新角色信息</span>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">角色名称</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" placeholder="角色名称" name="name"
                               value="{{\Input::old('name')}}">
                        <p class="help-block">角色名称不能有重复.</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="accessLevel" class="col-sm-2 control-label">访问等级</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="accessLevel" placeholder="访问等级" name="access_level"
                               value="{{\Input::old('accessLevel')}}">
                        <p class="help-block">访问等级为整数0-100.</p>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputPassword" class="col-sm-2 control-label">描述</label>

                    <div class="col-sm-10">
                        <textarea class="form-control" rows="5" placeholder="描述" name="description">{{\Input::old('description')}}</textarea>
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