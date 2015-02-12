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
Hiho_edu-查看讲师
@stop

@section('content')
<ul class="breadcrumb">
    <li><a href="#">首页</a> <span class="divider">/</span></li>
    <li><a href="#">讲师管理</a> <span class="divider">/</span></li>
    <li class="active">查看讲师</li>
</ul>
<form method="post" action="{{route('adminTeacherModifyAction')}}" enctype="multipart/form-data" >
    <label>讲师名</label>
    <input class="input" type="text" placeholder="name" name="teacher_name" value="{{$objTeacher->name}}">
    @if( !empty(\Session::get('name_tips')))
    <div class="alert alert-error">
        {{\Session::get('name_tips')}}
    </div>
    @endif

    <label>所属院系</label>
    <select onchange="getSub(this.options[this.selectedIndex].value)" id="parent">
        @if(!empty($departments))
        <option value="0">-- 请选择 --</option>
        @foreach($departments as $depart)
        <option value="{{$depart->id}}" >{{$depart->name}}</option>
        @endforeach
        @endif
    </select>
    <select name="department" id="department">
        <option value="{{$objDepartment->id}}" >{{$objDepartment->name}}</option>
    </select>
    <label>邮箱</label>
    <input class="input" type="text"  name="email" value="{{$objTeacher->email}}">
    @if( !empty(\Session::get('email_tips')))
    <div class="alert alert-error">
        {{\Session::get('email_tips')}}
    </div>
    @endif
    <label>头衔</label>
    <input class="input" type="text"  name="title" value="{{$objTeacher->title}}">
    <label>个人简介</label>
    <textarea name="description" cols="10">{{$objTeacher->description}}</textarea>
    <label>头像</label>
    <input type="file" name="header"/>
    @if( !empty(\Session::get('file_tips')))
    <div class="alert alert-error">
        {{\Session::get('file_tips')}}
    </div>
    @endif
    @if(!empty($objTeacher->portrait_src))
    <label></label>
        <img src="{{$objTeacher->portrait_src}}" width="60" height="60">
    @endif
    <label></label>
    <input type="hidden" name="id" value="{{$objTeacher->id}}">
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

    function checkSubmt(){
        var obj = document.getElementById('parent');
        if(obj.options[obj.selectedIndex].value == '0'){
            alert('还没有选择院系');
            return false;
        }
    }
</script>
@stop