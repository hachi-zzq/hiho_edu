@extends('layout.bs3_admin_layout')

@section('title')
段视频管理 - 西南财经大学
@stop


@section('content-wrapper')

<style type="text/css">
    #video_list tbody tr th{
        vertical-align: middle;
    }
</style>

<div class="page-header">
    <h1><span class="text-light-gray">视频 / </span>短视频管理</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">短视频列表</span>
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
                <table class="table" id="video_list">
                    <thead>
                    <tr>
                        <th nowrap="nowrap">封面</th>
                        <th nowrap="nowrap">原视频标题</th>
                        <th nowrap="nowrap">用户</th>
                        <th nowrap="nowrap">开始时间</th>
                        <th nowrap="nowrap">结束时间</th>
                        <th nowrap="nowrap">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($objFragment))
                        @foreach($objFragment as $Fragment)
                    <tr>
                        <th style="background-color: white">
                            <img src="{{$Fragment->cover ? $Fragment->cover : '/static/admin/images/160x120.png'}}" class="img-polaroid" width="160" height="120">
                        </th>
                        <th>{{$Fragment->title}}</th>
                        <th nowrap="nowrap">{{$Fragment->user?$Fragment->user->nickname:''}}</th>
                        <th nowrap="nowrap">{{ gmstrftime('%H:%M:%S',round($Fragment->start_time,0)) }}</th>
                        <th nowrap="nowrap">{{ gmstrftime('%H:%M:%S',round($Fragment->end_time,0)) }}</th>
                        <th>
                            <a href="/play/{{$Fragment->getPlayIdStr()}}" target="_blank" class="btn btn-xs">查看</a>
<!--                            <a href="#" class="btn btn-xs">修改</a>-->
                            <a href="{{route('adminFragmentDelete',array($Fragment->id))}}" onclick="return confirm('确定删除该短视频？');" class="btn btn-xs btn-danger">删除</a>
                        </th>
                    </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            {{$objFragment->links()}}
        </div>
    </div>
</div>
@stop