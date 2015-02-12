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
Hiho_edu-视频管理
@stop

@section('content')
<ul class="breadcrumb">
    <li><a href="#">首页</a> <span class="divider">/</span></li>
    <li><a href="#">视频管理</a> <span class="divider">/</span></li>
    <li class="active">添加视频信息</li>
</ul>
<form method="post" action="{{route('adminVideoModifyPost')}}">
<label>视频标题</label>
<input class="input-xxlarge" type="text" placeholder="video title" name="title" value="{{$objVideo->title}}">
    @if( !empty(\Session::get('title_tips')))
    <div class="alert alert-error">
        {{\Session::get('title_tips')}}
    </div>
    @endif

    <label>分类</label>
<select name="category">
    <option value="0">无</option>
    @if(!empty($categories))
        @foreach($categories as $parent)
            <option value="{{$parent->id}}" @if($parent->selected) {{'selected'}} @endif>{{$parent->name}}</option>
            @if( ! empty($parent->child))
                @foreach($parent->child as $ch)
                <option value="{{$ch->id}}"> |--{{$ch->name}}</option>
                @endforeach
            @endif
        @endforeach
    @endif
</select>
<label>院系</label>
<select onchange="getSub(this.options[this.selectedIndex].value)" id="parent">
    @if(!empty($departments))
    <option value="0">-- 请选择 --</option>
        @foreach($departments as $depart)
        <option value="{{$depart->id}}">{{$depart->name}}</option>
        @endforeach
    @endif
</select>
<select name="department" id="department" ><option value="0">-- 请选择 --</option>
</select>
<label>主讲</label>
<select name="teacher">
    @if(!empty($teachers))
        @foreach($teachers as $teacher)
            <option value="{{$teacher->id}}" @if($teacher->selected) {{'selected'}} @endif>{{$teacher->name}}</option>
        @endforeach
    @endif
</select>

<label>视频描述</label>
<textarea rows="3" name="description">{{$objVideo->description}}</textarea>
    <input type="hidden" name="id" value="{{$objVideo->id}}">
<label></label>
<button type="submit" class="btn">提交入库</button>
</form>
@stop

@section('js')
@parent
<script>
    function getSub(id){
        $.ajax({
            'url':'/admin/teacher/getSubDepartment',
            'type':'get',
            'data':'id='+id,
            'success':function(res){
                console.log(res);
                res = JSON.parse(res);
                if(res.length>0){
                    var html = '';
                    for(var i=0;i<res.length;i++){
                        html +='<option value="'+res[i].id+'">'+res[i].name+'</option>'
                    }
                    $('#department').empty();
                    $('#department').append(html);
                }
            },
            'error':function(){
                alert('ajax error');
            }
        })
    }

    function getTeacher(departID){
        $.ajax({
            'url':'/admin/teacher/getDepartmentTeacher',
            'type':'get',
            'data':'id='+departID,
            'success':function(res){
                console.log(res);
                alert(res)
            },
            'error':function(){
                alert('ajax error');
            }
        })
    }
</script>
@stop