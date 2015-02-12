@extends('layout.bs3_admin_layout')

@section('title')
视频管理 - 西南财经大学
@stop


@section('content-wrapper')
<style type="text/css">
    .col-sm-4{
        width: 10%;
    }
</style>

<div class="page-header">
    <h1><span class="text-light-gray">视频 / </span>上传列表</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">上传列表</span>
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
            <div class="panel">

                <div class="panel-body">
                    <form method="get" action="">
                        <div class="row">
                            <div class="col-sm-4">
                                <select class="form-control" name="type">
                                    <option value="0"> 全部视频 </option>
                                    <option value="-99" {{$type=='-99'?"selected":''}}>等待上传字幕</option>
                                    <option value="7" {{$type==7?"selected":''}}>完成匹配</option>
                                    <option value="6" {{$type==6?"selected":''}}>正在匹配</option>
                                    <option value="-7" {{$type=='-7'?"selected":''}}>匹配失败</option>
                                    <option value="else" {{$type=='else'?"selected":''}}>其他</option>
                                </select>
                            </div>
                            <div class="col-xs-2">
                                <input type="text" class="form-control" placeholder="视频标题" name="title" value="{{isset($title)?$title:''}}">
                            </div>
                            <div class="col-sm-offset-2 ">
                                <button type="submit" class="btn btn-primary">刷选</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel-body">
                <table class="table table-striped" id="upload_list">
                    <thead>
                    <tr>
                        <th>缩略图</th>
                        <th>视频标题</th>
                        <th>视频大小</th>
                        <th>是否已经添加字幕</th>
                        <th>当前匹配状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($videos))
                    @foreach($videos as $video)
                    <?php $video->load('sewise_videos_picture')?>
                    <tr>
                        <td id="img" style="background-color: white">
                            <img src="{{$video->pic ? $video->pic->src:'/static/admin/images/160x120.png'}}" class="img-polaroid" width="160" height="120">
                        </td>
                        <td style="vertical-align: middle;background-color: white">{{$video->title}}</td>
                        <td style="vertical-align: middle;background-color: white">{{Tool::formatBytes($video->bytesize)}}</td>
                        <td style="vertical-align: middle;background-color: white"><a href="#" class="label label-info">{{ !empty($video->source_id)?'是':'否'}}</a></td>
                        <td style="vertical-align: middle;background-color: white"><a href="#" class="label label-info">{{ \Tool::returnStatus($video->status)}}</a></td>
                        <td style="vertical-align: middle;background-color: white">
                            @if(!empty($video->source_id))
                            <span class="btn btn-xs disabled">已经上传过字幕</span>
                            @else
                            <a href="{{route('adminSubtitleAdd',array($video->id))}}" title="上传字幕文件，开始匹配" class="btn btn-xs">上传字幕，开始匹配</a>
                            @endif
                            <a href="{{route('adminVideoUploadDelete',array($video->id))}}" onclick="return confirm('真的要删除？')" class="btn btn-xs btn-danger">删除</a>
                        </td>
                    </tr>
                    @endforeach
                    @endif

                    </tbody>
                </table>
            </div>
        </div>
        {{ $videos->appends(Input::except('page'))->links() }}
    </div>
</div>
@stop