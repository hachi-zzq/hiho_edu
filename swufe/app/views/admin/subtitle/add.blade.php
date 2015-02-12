@extends('layout.admin_layout')

@section('css')
    @parent
    <style type="text/css">
        #video_list tr td{
            vertical-align: middle;
        }
    </style>
@stop


@section('title')
Hiho_edu-添加字幕
@stop

@section('content')
<ul class="breadcrumb">
    <li><a href="#">首页</a> <span class="divider">/</span></li>
    <li><a href="#">视频管理</a> <span class="divider">/</span></li>
    <li class="active">添加字幕</li>
</ul>
<form method="post" action="{{route('adminSubtitleAddAction')}}" enctype="multipart/form-data">
<label>视频标题</label>
<input class="input" type="text" placeholder="title" name="title" value="{{$objVideo->title}}">
@if($errors->first('title'))
    <div class="alert alert-error">
    {{$errors->first('title')}}
    </div>
@endif
<label>视频语言</label>
<select name="video_language">
    <option value="zh_cn" {{$objVideo->language=='zh_cn'?'selected':''}}>中文</option>
    <option value="en" {{$objVideo->language=='en'?'selected':''}}>英文</option>
</select>

<label>字幕语言</label>
<select name="language">
    <option value="zh_cn">中文</option>
    <option value="en">英文</option>
</select>

<label>视频字幕</label>
<input type="file" name="subtitle"/>
    @if(!empty(\Session::get('file_tips')))
    <div class="alert alert-error">
        {{\Session::get('file_tips')}}
    </div>
    @endif
<input type="hidden" name="video_id" value="{{$objVideo->id}}">
<label></label>
<button type="submit" class="btn">开始匹配</button>
</form>
@stop