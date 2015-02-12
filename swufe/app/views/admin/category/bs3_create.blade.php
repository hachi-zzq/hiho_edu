@extends('layout.bs3_admin_layout')

@section('title')
创建新分类 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">分类与主题 / </span>创建新分类</h1>
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
        <form class="panel form-horizontal" method="POST" action="{{route('adminCategoryAddPost')}}">
            <div class="panel-heading">
                <span class="panel-title">新分类</span>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputParent" class="col-sm-2 control-label">所属上级</label>
                    <div class="col-sm-4">
                        <select class="form-control" name="category">
                            <option value="0">-- 无 --</option>
                            @foreach($categories as $parent)
                                <option value="{{$parent->id}}">{{$parent->name}}</option>
                                @foreach($parent->child as $ch)
                                 <option value="{{$ch->id}}"> |--{{$ch->name}}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">名称</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" placeholder="名称" name="name"
                               value="{{\Input::old('name')}}">
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="tb_sort" class="col-sm-2 control-label">顺序</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="tb_sort" placeholder="顺序" name="sort"
                               value="{{\Input::old('sort')}}">
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="tb_access_level" class="col-sm-2 control-label">权限等级</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="tb_access_level" placeholder="权限等级" name="access_level"
                               value="{{\Input::old('access_level')}}">
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