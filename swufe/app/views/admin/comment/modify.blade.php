@extends('layout.bs3_admin_layout')

@section('title')
评论管理 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">评论 / </span>修改评论</h1>
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
            <form class="panel form-horizontal" method="post" action="{{route('adminCommentModify')}}">
                <div class="panel-heading">
                    <span class="panel-title">评论详情</span>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="asdasdas" class="col-sm-2 control-label">Text</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="content">{{$objComment->content}}</textarea>
                        </div>
                    </div> <!-- / .form-group -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">修改</button>
                        </div>
                    </div> <!-- / .form-group -->
                </div>
                <input type="hidden" name="id" value="{{$objComment->id}}">
            </form>
                </div> <!-- / .comment -->


            </div> <!-- / .panel-body -->
        </div> <!-- / .panel -->
    </div>
</div>
@stop