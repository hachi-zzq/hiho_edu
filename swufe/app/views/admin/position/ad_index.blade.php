@extends('layout.bs3_admin_layout')

@section('title')
广告位列表 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">运营 / </span>广告位管理</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">广告位列表</span>
            </div>
            <div style="padding:10px;">
                <a href="{{route('adminPositionAdCreate')}}" class="btn btn-primary" style="margin-left: 20px">新增广告位</a>
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
                <table class="table " id="playlist">
                    <tbody>
                    <tr>
                        <th></th>
                        <th>广告类型</th>
                        <th>标题</th>
                        <th>描述</th>
                        <th>广告数</th>
                        <th>调用代码</th>
                        <th>当前状态</th>
                        <th>操作</th>
                    </tr>
                    @if(!empty($objAdPositionList))
                        @foreach($objAdPositionList as $list)
                        <tr>
                            <td>
                                <input type="checkbox" id="inlineCheckbox1" value="option1">
                            </td>
                            <td>{{$list->type}}</td>
                            <td>{{$list->name}}</td>
                            <td>{{$list->description}}</td>
                            <td>{{$list->adCount}} </td>
                            <td> <input type="text" style="width: 200px;" value="<?php echo '{{'?>\AdvertisementCall::render({{$list->id}})<?php echo '}}'?>" onmouseover="selectInputContent(this)"> </td>
                            <td><a href="#" class="label label-info">{{$list->status == 1?'开启':'关闭'}}</a></td>
                            <td>
                                <a href="{{route('adminPositionAds',$list->id)}}" class="btn btn-xs">广告列表</a>
                                <a href="{{route('adminPositionAdModify',$list->id)}}" class="btn btn-xs">修改</a>
                                <a href="{{route('adminPositionAdStatus',$list->id)}}" class="btn btn-xs">{{$list->status == 1?'关闭':'开启'}}</a>
                                <a href="{{route('adminPositionAdDestroy',$list->id)}}"  class="btn btn-xs btn-danger">删除</a>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
function selectInputContent(obj){
    obj.focus();
    obj.select();
}
</script>
@stop