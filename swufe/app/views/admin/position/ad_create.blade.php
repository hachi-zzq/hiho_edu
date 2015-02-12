@extends('layout.bs3_admin_layout')

@section('title')
创建广告位 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">运营 / </span>新增广告位</h1>
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
        <form class="panel form-horizontal"  method="POST">
            <div class="panel-heading">
                <span class="panel-title">广告位资料</span>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputEmail2" class="col-sm-2 control-label">广告位名称</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputEmail" name="name" >

                    </div>
                </div>
                <div class="form-group">
                    <label for="inputNickname" class="col-sm-2 control-label">广告位描述</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputNickname" name="description">
                    </div>

                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputIsAdmin" class="col-sm-2 control-label">广告位类型</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="type">
                            <option value="picture">图片广告</option>
                            <option value="text">文字连接</option>
                            <option value="rotation">图片轮播</option>
                            <option value="code">代码广告</option>
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