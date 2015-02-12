@extends('layout.bs3_admin_layout')

@section('title')
创建新教师 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">教师与机构 / </span>创建新教师</h1>
</div> <!-- / .page-header -->
<div class="panel">
    @if (\Session::get('tips'))
    <div class="alert alert-page alert-dark">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ \Session::get('tips') }}</strong>
    </div>
    @endif
    @if (\Session::get('error_tips'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ \Session::get('error_tips') }}</strong>
    </div>
    @endif
    @if (\Session::get('success_tips'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ \Session::get('success_tips') }}</strong>
    </div>
    @endif
</div>
<div class="row">
    <div class="col-sm-12">
        <form class="panel form-horizontal" method="POST" enctype="multipart/form-data" action="{{route('adminTearchCreatePost')}}">
            <div class="panel-heading">
                <span class="panel-title">新教师</span>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputParent" class="col-sm-2 control-label">所属院系机构</label>

                    <div class="col-sm-4">
                        <select class="form-control" name="department">
                            @foreach($departments as $parent)
                            <option value="{{$parent->id}}" style="color: #000">{{$parent->name}}</option>
                            @foreach($parent->child as $ch)
                            <option value="{{$ch->id}}"> |--{{$ch->name}}</option>
                            @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">姓名</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" placeholder="姓名" name="teacher_name"
                               value="{{\Input::old('name')}}">
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputPermalink" class="col-sm-2 control-label">唯一固定标识</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputPermalink" placeholder="唯一固定标识"
                               name="permalink"
                               value="{{\Input::old('permalink')}}">

                        <p class="help-block">以便于索引和搜索引擎收录。<br>访问此教师时 URL 中的标识，仅可使用英文字母和下划线，且不区分大小写。建议使用名称的拼音或英文缩写。</p>

                        <p class="help-block">例如：http://www.example.com/tearchers/<span class="text-primary">zhang-chun-hui</span>
                        </p>

                        <p class="help-block">例如：http://www.example.com/tearchers/<span
                                class="text-primary">sunny</span>
                        </p>

                        <p class="help-block">不建议：http://www.example.com/tearchers/<span
                                class="text-warning">zch</span></p>

                        <p class="help-block">错误：http://www.example.com/tearchers/<span
                                class="text-danger">张春辉</span></p>

                        <p class="help-block">错误：http://www.example.com/tearchers/<span class="text-danger">456</span>
                        </p>

                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputTitle" class="col-sm-2 control-label">头衔</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputTitle" placeholder="头衔" name="title"
                               value="{{\Input::old('title')}}">
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">邮箱</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputEmail" placeholder="邮箱" name="email"
                               value="{{\Input::old('email')}}">
                    </div>
                </div>
                <!-- / .form-group -->
                <script>
                    init.push(function () {
                        $('#input-upload-portrait').pixelFileInput({ placeholder: '未选择文件...' });
                    })
                </script>
                <div class="form-group">
                    <label for="inputPortrait" class="col-sm-2 control-label">头像</label>

                    <div class="col-sm-10">
                        <input type="file" id="input-upload-portrait" name="portrait">
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputDescription" class="col-sm-2 control-label">简介</label>

                    <div class="col-sm-10">
                        <textarea class="form-control" name="description"></textarea>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group" style="margin-bottom: 0;">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">创建</button>
                    </div>
                </div>
                <!-- / .form-group -->
            </div>
        </form>
    </div>
</div>
@stop