@extends('layout.bs3_admin_layout')

@section('title')
教师管理 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">教师与机构 / </span>教师管理</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">教师</span>
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
                <form method="post" action="{{route('adminTeacherRecommend')}}">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>头像</th>
                        <th>固定标识</th>
                        <th>姓名</th>
                        <th>头衔</th>
                        <th>课程数</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($teachers as $teacher)
                    <tr class="valign-middle">
                        <td>
                            <input type="checkbox" id="inlineCheckbox1" name="check_id[]" value="{{$teacher->id}}"/>
                        </td>
                        <td>{{$teacher->id}}</td>
                        <td>
                            <img src="{{$teacher->portrait_src?$teacher->portrait_src:'/assets/demo/avatars/1.jpg'}}" alt="" class="rounded" style=" width: 40px;height: 40px;"></td>
                        <td>{{$teacher->permalink}}</td>
                        <td>{{$teacher->name}}</td>
                        <td>{{$teacher->title}}</td>
                        <td>{{$teacher->count}}</td>
                        <td>
                            <a href="{{route('adminTeacherModify',array($teacher->id))}}" class="btn btn-xs">修改</a>
                            <a href="{{route('adminTeacherDelete',array($teacher->id))}}" onclick="return confirm('确定删除该教师？');" class="btn btn-xs btn-danger">删除</a>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="8">
                            <div class="pagination">
                                {{ $teachers->links() }}
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div>

                    <a href="javascript:void(0)" onclick="checkAll()">全选</a>
                    <a href="javascript:void(0)" onclick="uncheckAll()">反选</a>
                    <button class="btn btn-xs btn-danger" type="button" >批量删除</button>
                    <select name="recommend_position">
                        <option value="0">推荐位置</option>
                        <option value="index" >首页</option>
                    </select>
                    <button class="btn btn-xs" type="submit" >推荐</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
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
</script>
@stop