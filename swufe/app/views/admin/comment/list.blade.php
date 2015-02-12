@extends('layout.bs3_admin_layout')

@section('title')
评论管理 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">评论 / </span>所有评论</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            @if (Session::get('tips'))
            <div class="alert alert-page alert-dark">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ Session::get('tips') }}</strong>
            </div>
            @endif
            @if (Session::get('error_tips'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ Session::get('error_tips') }}</strong>
            </div>
            @endif
            @if (Session::get('success_tips'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ Session::get('success_tips') }}</strong>
            </div>
            @endif
        </div>

        <div class="panel widget-comments">
            <div class="panel-heading">
                <span class="panel-title"><i class="panel-title-icon fa fa-comment-o"></i>最新评论</span>
            </div> <!-- / .panel-heading -->
            <div class="panel-body">
            @if(!empty($objComment))
                @foreach($objComment as $comment)
                <div class="comment">
                    <img src="{{$comment->user?$comment->user->avatar:'/static/admin/images/header.jpg'}}" alt="" class="comment-avatar" title="{{$comment->user?$comment->user->email:''}}">
                    <div class="comment-body">
                        <div class="comment-by">
                            <a href="#" title=""></a>  <a href="#" title="{{$comment->video->title}}">{{$comment->video->title}}</a>评论
                        </div>
                        <div class="comment-text">
                            {{htmlspecialchars($comment->content)}}
                        </div>
                        <div class="comment-actions">
                            <a href="{{route('adminCommentModify',array($comment->id))}}"><i class="fa fa-pencil"></i>修改</a>
                            <a href="{{route('adminCommentDelete',array($comment->id))}}"><i class="fa fa-times"></i>删除</a>
                            <span class="pull-right">{{\Tool::timeFormat(strtotime($comment->created_at))}}</span>
                        </div>
                    </div> <!-- / .comment-body -->
                </div> <!-- / .comment -->
                @endforeach
            @endif


            </div> <!-- / .panel-body -->
        </div> <!-- / .panel -->
    </div>
</div>
{{$objComment->links()}}
@stop