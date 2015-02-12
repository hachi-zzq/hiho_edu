@extends('layout.bs3_admin_layout')

@section('title')
修改视频信息 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">视频 / </span>添加视频信息</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <form class="panel form-horizontal" method="POST" action="{{route('adminVideoModifyPost')}}">
            <div class="panel-heading">
                <span class="panel-title">视频信息</span>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputEmail2" class="col-sm-2 control-label">视频标题</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputEmail" placeholder="视频标题" name="title"
                               value="{{$objVideo->title}}">

                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">分类</label>

                    <div class="col-sm-10">
                        <p class="help-block"><a href="javascript:void(0)" id="add_category">增加</a><span style="margin-left: 20px;"></span>可以选择多个分类</p>
                    </div>
                </div>

                <div class="form-group form_category" style="display: none;">
                    <label for="" class="col-sm-2 control-label"><a href="javascript:void(0)" onclick="this.parentNode.parentNode.remove()" class="delete_category">删除</a></label>

                    <div class="col-sm-10">
                        <select class="form-control form-group-margin category">
                            <option value="0">--  无 --</option>
                            @if(!empty($categories))
                            @foreach($categories as $parent)
                            <option value="{{$parent->id}}">{{$parent->name}}</option>
                            @if( ! empty($parent->child))
                            @foreach($parent->child as $ch)
                            <option value="{{$ch->id}}"> |--{{$ch->name}}</option>
                            @endforeach
                            @endif
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                @foreach($objVideo->video->categories as $sc)
                <div class="form-group form_category">
                    <label for="" class="col-sm-2 control-label"><a href="javascript:void(0)" onclick="this.parentNode.parentNode.remove()" class="delete_category">删除</a></label>

                    <div class="col-sm-10">
                        <select class="form-control form-group-margin category" name="category[]">
                            <option value="0">--  无 --</option>
                            @if(!empty($categories))
                            @foreach($categories as $parent)
                            <option value="{{$parent->id}}" @if($parent->id == $sc->id) {{'selected'}} @endif>{{$parent->name}}</option>
                            @if( ! empty($parent->child))
                            @foreach($parent->child as $ch)
                            <option value="{{$ch->id}}" @if($ch->selected == $sc->id) {{'selected'}} @endif> |--{{$ch->name}}</option>
                            @endforeach
                            @endif
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                @endforeach

                <!-- / .form-group -->
                <div class="form-group">
                    <label for="speciality" class="col-sm-2 control-label">专业</label>

                    <div class="col-sm-10">
                        <select class="form-control form-group-margin" name="speciality" id="speciality">
                            <option value="0">-- 无 --</option>
                            @if(!empty($specialities))
                            @foreach($specialities as $spec)
                            <option value="{{$spec->id}}" @if($specialitySelected and $spec->id == $specialitySelected->id) {{'selected'}} @endif>{{$spec->name}}</option>
                                @if(!empty($spec->child))
                                @foreach($spec->child as $ch)
                                    <option value="{{$ch->id}}" @if($specialitySelected and $ch->id == $specialitySelected->id) {{'selected'}} @endif> |--{{$ch->name}}</option>
                                @endforeach
                                @endif
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <!-- / .form-group -->
                <div class="form-group">
                    <label for="topic" class="col-sm-2 control-label">主题</label>

                    <div class="col-sm-10">
                        <select class="form-control form-group-margin" name="topic" id="topic">
                            <option value="0">-- 无 --</option>
                        @if(!empty($topics))
                            @foreach($topics as $topic)
                            <option value="{{$topic->id}}" @if($topic->selected == 1) {{'selected'}} @endif>{{$topic->name}}</option>
                            @endforeach
                         @endif
                        </select>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="department" class="col-sm-2 control-label">院系</label>

                    <div class="col-sm-10">
                        <select class="form-control form-group-margin" id="department" name="department" onchange="teacherIndex(this)">
                            <option value="0">-- 请选择 --</option>
                            @if(!empty($departments))
                                @foreach($departments as $parent)
                                    <option value="{{$parent->id}}" @if($parent->selected) {{'selected'}} @endif>{{$parent->name}}</option>
                                    @if( ! empty($parent->child))
                                        @foreach($parent->child as $ch)
                                        <option value="{{$ch->id}}"> |--{{$ch->name}}</option>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <p class="help-block">选择院系可以筛选该院系下的讲师</p>
                    </div>

                </div>

                <!-- / .form-group -->
                <div class="form-group">
                    <label for="teacher" class="col-sm-2 control-label">主讲</label>

                    <div class="col-sm-10">
                        <select class="form-control form-group-margin" name="teacher" id="teacher">
                            <option value="0">-- 无 --</option>
                            @if(!empty($teachers))
                                @foreach($teachers as $teacher)
                                <option value="{{$teacher->id}}" @if($teacher->selected == 1) {{'selected'}} @endif>{{$teacher->name}}</option>
                                @endforeach
                            @endif
                        </select>

                    </div>
                </div>

                <div class="form-group">
                    <label for="tb_access_level" class="col-sm-2 control-label">权限等级</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="tb_access_level" placeholder="权限等级" name="access_level"
                               value="{{$objVideo->video->access_level}}">
                    </div>
                </div>

                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputIsAdmin" class="col-sm-2 control-label">视频描述</label>

                    <div class="col-sm-10">
                        <textarea class="form-control" rows="5" placeholder="视频描述" name="description">{{$objVideo->description}}</textarea>
                    </div>
                </div>

                <input type="hidden" name="id" value="{{$objVideo->video_id}}">
                <!-- / .form-group -->
                <div class="form-group" style="margin-bottom: 0;">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">修改</button>
                    </div>
                </div>
                <!-- / .form-group -->
            </div>
        </form>
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
                var html = '';
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

    $(function(){
        $('#add_category').click(function(){
            var tpl = $('.form_category').first();
            tpl.find('select').attr('name','category[]');
            var lastCatForm = $('.form_category').last();
            lastCatForm.after(tpl.clone().show());
        });
    });
</script>
@stop

