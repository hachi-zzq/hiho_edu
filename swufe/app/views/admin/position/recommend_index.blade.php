@extends('layout.bs3_admin_layout')

@section('title')
推荐位管理 - 西南财经大学
@stop

@section('content-wrapper')
<style type="text/css">
    #playlist tr td{
        vertical-align: middle;
    }
</style>

<div class="page-header">
    <h1><span class="text-light-gray">运营 / </span>推荐位管理</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">推荐位列表</span>
            </div>
            <div style="padding:10px;">
                <a href="{{route('adminPositionCreate')}}" class="btn btn-primary" style="margin-left: 20px">新增推荐位</a>
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
                        <th>标题</th>
                        <th>推荐位类型</th>
                        <th>当前推荐数</th>
                        <th>最大推荐数</th>
                        <th>操作</th>
                    </tr>
                    @if(!empty($objPosition))
                        @foreach($objPosition as $list)
                        <tr>
                            <td>
                                <input type="checkbox" id="inlineCheckbox1" value="option1">
                            </td>
                            <td>{{$list->name}}</td>
                            <td>{{$list->type=='video'?'视频':'讲师'}}</td>
                            <td title="查看该推荐位下的推荐内容" ><a href="{{route('adminPositionRecommendDetail',$list->id)}}">{{$list->exist_num}}</a></td>
                            <td>{{$list->max_num}}</td>
                            <td>
                                <a href="{{route('adminPositionRecommendDetail',$list->id)}}" class="btn btn-xs">查看推荐内容</a>
                                <a href="{{route('adminPositionRecommendModify',$list->id)}}" class="btn btn-xs">修改</a>
                                <a href="{{route('adminPositionRecommendDestroy',$list->id)}}"  class="btn btn-xs btn-danger">删除</a>
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
@stop