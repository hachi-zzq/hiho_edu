@extends('layout.bs3_admin_layout')

@section('title')
修改角色信息 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">角色 / </span>修改角色信息</h1>
</div> <!-- / .page-header -->

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
                               value="{{{$role->name}}}">
                        <p class="help-block">角色名称不能有重复.</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="accessLevel" class="col-sm-2 control-label">访问等级</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="accessLevel" placeholder="访问等级" name="access_level"
                               value="{{$role->access_level}}">
                        <p class="help-block">访问等级为整数0-100.</p>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputPassword" class="col-sm-2 control-label">描述</label>

                    <div class="col-sm-10">
                        <textarea class="form-control" rows="5" placeholder="描述" name="description">{{{$role->description}}}</textarea>
                    </div>
                </div>
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