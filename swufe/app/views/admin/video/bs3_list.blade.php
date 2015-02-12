@extends('layout.bs3_admin_layout')

@section('title')
视频管理 - 西南财经大学
@stop


@section('content-wrapper')

<style type="text/css">
    .col-sm-4{
        width: 8%;
    }
</style>

<div class="page-header">
    <h1><span class="text-light-gray">视频 / </span>视频列表</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">视频列表</span>
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
                                <select class="form-control" name="category">
                                    <option value="0"> 全部分类 </option>
                                    @if(!empty($categories))
                                        @foreach($categories as $parent)
                                        <option value="{{$parent->id}}" @if(Input::get('category') == $parent->id) {{'selected'}} @endif>{{$parent->name}}</option>
                                            @if( ! empty($parent->child))
                                                @foreach($parent->child as $ch)
                                                <option value="{{$ch->id}}" @if(Input::get('category') == $parent->id) {{'selected'}} @endif> |--{{$ch->name}}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <select class="form-control" name="topic">
                                    <option value="0"> 全部主题 </option>
                                    @if(!empty($topics))
                                        @foreach($topics as $topic)
                                        <option value="{{$topic->id}}" @if(Input::get('topic') == $topic->id) {{'selected'}} @endif>{{$topic->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <select class="form-control" name="department"  onchange="teacherIndex(this)">
                                    <option value="0"> 全部院系 </option>
                                    @if(!empty($departments))
                                        @foreach($departments as $parent)
                                            <option value="{{$parent->id}}" @if(Input::get('department') == $parent->id) {{'selected'}} @endif>{{$parent->name}}</option>
                                            @if( ! empty($parent->child))
                                                @foreach($parent->child as $ch)
                                                <option value="{{$ch->id}}"> |--{{$ch->name}}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <select class="form-control" name="teacher" id="teacher">
                                    <option value="0"> 全部讲师 </option>
                                    @if(!empty($teachers))
                                    @foreach($teachers as $teacher)
                                    <option value="{{$teacher->id}}" @if(Input::get('teacher') ==$teacher->id) {{'selected'}} @endif>{{$teacher->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-xs-2">
                                <input type="text" class="form-control" placeholder="视频标题" name="title" value="{{Input::get('title')}}">
                            </div>
                            <div class="col-sm-offset-2 ">
                                <button type="submit" class="btn btn-primary">筛选</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="panel-body">
                <table class="table" id="video_list">
                    <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th nowrap="nowrap">封面</th>
                        <th nowrap="nowrap">标题</th>
                        <th nowrap="nowrap">讲师</th>
                        <th nowrap="nowrap">分类</th>
                        <th nowrap="nowrap">添加时间</th>
                        <th nowrap="nowrap">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <form id="video_bind">
                    @if(!empty($objVideo))
                        @foreach($objVideo as $video)
                    <tr>

                        <td>
                            <input type="checkbox" id="id" value="{{$video->video_id}}" name="check_id[]">
                        </td>
                        <td>{{$video->video_id}}</td>
                        <td style="background-color: white" width="160">
                            <img src="{{$video->pictures ?  $video->pictures->src : '/static/admin/images/160x120.png'}}" class="img-polaroid" width="160" height="120">
                        </td>
                        <td title="{{$video->title}}">{{Tool::csubstr($video->title,0,50,'...')}}</td>
                        <td nowrap="nowrap">{{$video->teacher_info? $video->teacher_info->name : '无'}}</td>
                        <td nowrap="nowrap">
                            {{$video->category?$video->category:'无'}}
                        </td>
                        <td nowrap="nowrap">{{$video->created_at}}</td>
                        <td>
                            <a href="{{route('adminVideoModify',array($video->video_id))}}" class="btn btn-xs">基本信息</a>
                            <a href="{{route('adminQuestionHighLight',array($video->video_id))}}" class="btn btn-xs">交互信息</a>
                            <a href="{{route('adminUpdateAttachmentShow',array($video->video_id))}}" class="btn btn-xs">附件</a>
                            <a href="{{route('adminVideoDelete',array($video->video_id))}}" class="btn btn-xs btn-danger">删除</a>
                        </td>
                    </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>
                <div>

                    <a href="javascript:void(0)" onclick="checkAll()">全选</a>
                    <a href="javascript:void(0)" onclick="uncheckAll()">反选</a>
                    <select name="category">
                        <option value="0">选择分类</option>
                        @if(!empty($categories))
                        @foreach($categories as $parent)
                        <option value="{{$parent->id}}" @if(Input::get('category') == $parent->id) {{'selected'}} @endif>{{$parent->name}}</option>
                        @if( ! empty($parent->child))
                        @foreach($parent->child as $ch)
                        <option value="{{$ch->id}}" @if(Input::get('category') == $parent->id) {{'selected'}} @endif> |--{{$ch->name}}</option>
                        @endforeach
                        @endif
                        @endforeach
                        @endif
                    </select>
                    <select name="topic">
                        <option value="0">选择主题</option>
                        @if(!empty($topics))
                        @foreach($topics as $topic)
                        <option value="{{$topic->id}}" @if(Input::get('topic') == $topic->id) {{'selected'}} @endif>{{$topic->name}}</option>
                        @endforeach
                        @endif
                    </select>
                    <select name="teacher">
                        <option value="0">选择讲师</option>
                        @if(!empty($teachers))
                        @foreach($teachers as $teacher)
                        <option value="{{$teacher->id}}" @if(Input::get('teacher') ==$teacher->id) {{'selected'}} @endif>{{$teacher->name}}</option>
                        @endforeach
                        @endif
                    </select>

                    <a class="btn btn-xs" type="button" onclick="bindVideoInfo()">绑定</a>
                  </form>
                </div>
            </div>
            {{$objVideo->appends(Input::except('page'))->links()}}
        </div>
    </div>
</div>
<script>
    function teacherIndex(obj){
        var department_id = obj.options[obj.selectedIndex].value;
        $.ajax({
            'url':'/admin/teachers/getDepartmentTeacher',
            'data':'department_id='+department_id,
            'success':function(data){
                data = JSON.parse(data);
                console.log(data);
                var html = '<option value="0"> 全部讲师 </option>';
                if((len =data.data.length)>0){
                    data = data.data;
                    for(var i=0;i<len;i++){
                        html = html + '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                    }
                }
                $('#teacher').empty().append(html);
                console.log(html);
            },
            'error':function(){
                alert('ajax error');
            }
        })
    }
    function checkAll()
    {
        var code_Values = document.getElementsByName('check_id[]');
        if(code_Values.length){
            for(var i=0;i<code_Values.length;i++)
            {
                code_Values[i].checked = true;
            }
        }else{
            code_Values.checked = true;
        }
    }
    function uncheckAll()
    {
        var code_Values = document.getElementsByName('check_id[]');
        if(code_Values.length){
            for(var i=0;i<code_Values.length;i++)
            {
                if(code_Values[i].checked){
                    code_Values[i].checked = false;
                }else{
                    code_Values[i].checked = true;
                }
            }
        }
    }
    function bindVideoInfo(){
        $.ajax({
            'url':'/admin/video/bindInfo',
            'type':'POST',
            'data':$('#video_bind').serializeArray(),
            'success':function(data){
                data = JSON.parse(data);
                if(data.msgCode == '0'){
                    alert(data.message);
                    window.location.reload();
                }else if(data.msgCode == '-1'){
                     alert(data.message);
                    return false;
                }
            },
            'error':function(){
                alert('ajax error');
            }
        })
    }
</script>
@stop

