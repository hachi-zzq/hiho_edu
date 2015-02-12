@extends('layout.bs3_admin_layout')

@section('title')
收藏管理 - 西南财经大学
@stop

@section('content-wrapper')
<style type="text/css">
    #playlist tr td{
        vertical-align: middle;
    }
</style>

<div class="page-header">
    <h1><span class="text-light-gray">用户内容 / </span>收藏管理</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">用户收藏</span>
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
                        <th>描述</th>
                        <th>创建者</th>
                        <th>短视频数</th>
                        <th>操作</th>
                    </tr>
                    @if(!empty($lists))
                        @foreach($lists as $list)
                        <tr>
                            <td>
                                <input type="checkbox" id="inlineCheckbox1" value="option1">
                            </td>
                            <td>{{$list->title}}</td>
                            <td>{{$list->description}}</td>
                            <td>{{$list->user?$list->user->email:''}}</td>
                            <td>{{$list->fragmentCount ? $list->fragmentCount : 0}}</td>
                            <td>
<!--                                <a href="#" class="btn btn-xs">查看</a>-->
                                @if($list->status == 'NORMAL')
                                <a href="{{route('adminPlaylistCheck',array($list->id))}}" class="btn btn-xs btn-danger">屏蔽该笔记</a>
                                @else
                                <a href="{{route('adminPlaylistCheck',array($list->id))}}" class="btn btn-xs">激活该笔记</a>
                                @endif
                                <a href="{{route('adminPlaylistDestory',array($list->id))}}" class="btn btn-xs btn-danger">删除</a>
                        </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- $users->links() --}}
@stop