@extends('layout.bs3_admin_layout')

@section('title')
机构修改 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">教师与机构 / </span>修改机构</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <form class="panel form-horizontal" method="POST" action="{{route('adminDepartmentModify')}}">
            <div class="panel-heading">
                <span class="panel-title">院系机构资料</span>
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
                <div class="form-group">
                    <label for="inputParent" class="col-sm-2 control-label">所属上级</label>
                    <div class="col-sm-4">
                        <select class="form-control" name="departments">
                            <option value="0" @if($objDepartment->parent == 0) {{"selected"}} @endif>无</option>
                            @if(!empty($departments))
                                @foreach($departments as $parent)
                                @if($objDepartment->id != $parent->id)
                                    <option value="{{$parent->id}}"  @if($parent->id == $objDepartment->parent) {{"selected"}} @endif >{{$parent->name}}</option>
                                    @if( ! empty($parent->child))
                                        @foreach($parent->child as $ch)
                                            @if($objDepartment->id != $ch->id)
                                             <option value="{{$ch->id}}"  @if($ch->id == $objDepartment->parent) {{"selected"}} @endif > |--{{$ch->name}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">名称</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" placeholder="名称" name="name"
                               value="{{\Input::old('name')?\Input::old('name'):$objDepartment->name}}">
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputPermalink" class="col-sm-2 control-label">唯一固定标识</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputPermalink" placeholder="唯一固定标识"
                               name="permalink"
                               value="{{\Input::old('permalink')?\Input::old('permalink'):$objDepartment->permalink}}">

                        <p class="help-block">以便于索引和搜索引擎收录。<br>访问此机构时 URL 中的标识，仅可使用英文字母和下划线，且不区分大小写。建议使用名称的拼音或英文缩写。</p>

                        <p class="help-block">例如：http://www.example.com/departments/<span class="text-primary">zhong_wen_xue_yuan</span>
                        </p>

                        <p class="help-block">例如：http://www.example.com/departments/<span class="text-primary">IT_department</span>
                        </p>

                        <p class="help-block">不建议：http://www.example.com/departments/<span
                                class="text-warning">zwxy</span></p>

                        <p class="help-block">错误：http://www.example.com/departments/<span
                                class="text-danger">中文学院</span></p>

                        <p class="help-block">错误：http://www.example.com/departments/<span class="text-danger">123</span>
                        </p>

                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputDescription" class="col-sm-2 control-label">描述</label>

                    <div class="col-sm-10">
                        <textarea class="form-control" name="description">{{\Input::old('description')?\Input::old('description'):$objDepartment->description}}</textarea>
                    </div>
                </div>
                <input type="hidden" name="id" value="{{$objDepartment->id}}">
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
@stop