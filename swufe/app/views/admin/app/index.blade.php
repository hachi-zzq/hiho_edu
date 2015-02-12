@extends('layout.bs3_admin_layout')

@section('title')
App移动管理 - 西南财经大学
@stop

@section('content-wrapper')
<div class="row" xmlns="http://www.w3.org/1999/html">

<div class="panel">
<div class="panel-heading">
    <span class="panel-title">运营/App管理</span>
</div>
<div class="panel-body">
<ul id="uidemo-tabs-default-demo" class="nav nav-tabs">
    <li class="active">
        <a href="#uidemo-tabs-default-demo-home" data-toggle="tab">下载地址 </a>
    </li>

</ul>

<div class="tab-content tab-content-bordered">
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
    <form method="post">
    <div class="tab-pane fade  active in"  id="uidemo-tabs-default-demo-home">
            <div class="form-group">
                <label for="inputEmail2" class="col-sm-2 control-label">Android版：</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputEmail"   name="android"  value="{{$android}}">

                    <p class="help-block"><span class="text-primary">填写android版系统的下载地址</span></p>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail2" class="col-sm-2 control-label">IOS：</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputEmail"   name="ios"  value="{{$ios}}" >

                    <p class="help-block"><span class="text-primary">站点名称，将显示在浏览器窗口标题、页面底部等位置.</span></p>
                </div>
            </div>

        <div class="form-group" >
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">设置</button>
            </div>
        </div>
    </form>
    </div> <!-- / .tab-pane -->
</div> <!-- / .tab-pane -->
</div> <!-- / .tab-content -->
</div>
</div>
@stop