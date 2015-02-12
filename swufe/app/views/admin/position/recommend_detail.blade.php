@extends('layout.bs3_admin_layout')

@section('title')
推荐位详情 - 西南财经大学
@stop

@section('content-wrapper')
<style type="text/css">
    #playlist tr td{
        vertical-align: middle;
    }
</style>

<div class="page-header">
    <h1><span class="text-light-gray">用户内容 / </span>推荐位详情</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">推荐位列表</span>
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
                        <th>标题/名称</th>
                        <th>推荐位类型</th>
                        <th>操作</th>
                    </tr>
                    @if(!empty($objRecommendList))
                        @foreach($objRecommendList as $list)
                        <tr>
                            <td>
                                <input type="checkbox" id="inlineCheckbox1" value="option1">
                            </td>
                            <td>@if($list->type=='video') {{VideoInfo::where('video_id',$list->content_id)->first()->title}} @elseif($list->type=='teacher') {{Teacher::find($list->content_id)->name}} @endif</td>
                            <td>{{$list->type=='video'?'视频':'讲师'}}</td>
                            <td>
                                <a href="{{route('adminNoRecommend',$list->id)}}"  class="btn btn-xs btn-danger">取消推荐</a>
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