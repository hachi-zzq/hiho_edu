@extends('layout.bs3_admin_layout')

@section('title')
修改分类 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">分类与主题 / </span>修改分类</h1>
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
        <form class="panel form-horizontal" method="POST" action="{{route('adminCategoryModify')}}">
            <div class="panel-heading">
                <span class="panel-title">分类信息</span>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputParent" class="col-sm-2 control-label">所属上级</label>
                    <div class="col-sm-4">
                        <select class="form-control" name="category">
                            <option value="0">-- 无 --</option>
                            @foreach($categories as $parent)
                            @if($objCategory->id != $parent->id)
                                <option value="{{$parent->id}}" @if($parent->id==$objCategory->parent) {{'selected'}} @endif>{{$parent->name}}</option>
                                    @foreach($parent->child as $ch)
                                        @if($objCategory->id != $ch->id)
                                            <option value="{{$ch->id}}" @if($ch->id==$objCategory->parent) {{'selected'}} @endif> |--{{$ch->name}}</option>
                                        @endif
                                    @endforeach
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">名称</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" placeholder="名称" name="name"
                               value="{{$objCategory->name}}">
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="tb_sort" class="col-sm-2 control-label">顺序</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="tb_sort" placeholder="顺序" name="sort"
                               value="{{$objCategory->sort}}">
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="tb_access_level" class="col-sm-2 control-label">权限等级</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="tb_access_level" placeholder="权限等级" name="access_level"
                               value="{{$objCategory->access_level}}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="cb_reset_video" class="col-sm-2 control-label">影响该分类下视频</label>

                    <div class="col-sm-10">
                        <input type="checkbox" id="cb_reset_video" name="reset_video" />
                    </div>
                </div>

                <input type="hidden" name="id" value="{{$objCategory->id}}">
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