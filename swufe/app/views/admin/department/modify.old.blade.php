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
Hiho_edu-修改院系信息
@stop

@section('content')
<ul class="breadcrumb">
    <li><a href="/admin">首页</a> <span class="divider">/</span></li>
    <li><a href="#">院系管理</a> <span class="divider">/</span></li>
    <li class="active">修改信息</li>
</ul>
<form method="post" action="{{route('adminDepartmentModify')}}">
    <label>名称</label>
    <input class="input" type="text" placeholder="name" name="name" value="{{\Input::old('name')?\Input::old('name'):$objDepartment->name}}">
    @if($errors->first('name'))
    <div class="alert alert-error">
        {{$errors->first('name')}}
    </div>
    @endif

    <label>permalink</label>
    <input class="input" type="text" placeholder="permalink" name="permalink" value="{{\Input::old('permalink')?\Input::old('permalink'):$objDepartment->permalink}}"> <span style="color: green">( * 不填写默认名称拼音)</span>
    @if($errors->first('name'))
    <div class="alert alert-error">
        {{$errors->first('name')}}
    </div>
    @endif

    <label>描述</label>
    <textarea name="description">{{\Input::old('description')?\Input::old('description'):$objDepartment->description}}</textarea>
    @if($errors->first('name'))
    <div class="alert alert-error">
        {{$errors->first('name')}}
    </div>
    @endif

    <label>所属上级</label>
    <select name="departments">
        <option value="0" @if($objDepartment->parent == 0) {{"selected"}} @endif>无</option>
        @if(!empty($departments))
            @foreach($departments as $parent)
        <option value="{{$parent->id}}"  @if($parent->id == $objDepartment->parent) {{"selected"}} @endif >{{$parent->name}}</option>
            @if( ! empty($parent->chlid))
                @foreach($parent->chlid as $ch)
                <option value="{{$ch->id}}"  @if($ch->id == $objDepartment->parent) {{"selected"}} @endif > |--{{$ch->name}}</option>
                @endforeach
            @endif
            @endforeach
        @endif
    </select>
    <label></label>
    <input type="hidden" name="id" value="{{$objDepartment->id}}">
    <button type="submit" class="btn">提交入库</button>
</form>
@stop