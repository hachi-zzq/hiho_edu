@extends('layout.bs3_admin_layout')

@section('title')
创建推荐位 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">运营 / </span>新增推荐位</h1>
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
        <form class="panel form-horizontal"    method="POST">
            <div class="panel-heading">
                <span class="panel-title">推荐位资料</span>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputEmail2" class="col-sm-2 control-label">名称</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputEmail" name="name"
                               >

                    </div>
                </div>
                <div class="form-group">
                    <label for="inputNickname" class="col-sm-2 control-label">位置标识</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputNickname" name="class">
                        <p class="help-block">位置标识，主要用于与模板中class对应，不填写默认是<span class="text-primary">名称的拼音</span></p>
                        <p class="help-block">例如：<span class="text-primary">index-video</span>
                        <p class="help-block">例如：<span class="text-primary">index-teacher</span>
                        </p>
                    </div>


                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputPassword" class="col-sm-2 control-label">最大推荐数</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputPassword"  name="max_number"  >
                        <p class="help-block">例如：<span class="text-primary">只能是数字</span>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputIsAdmin" class="col-sm-2 control-label">推荐位类型</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="type">
                            <option value="video">视频</option>
                            <option value="teacher">讲师</option>
                        </select>
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