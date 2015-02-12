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
                            <th>姓名</th>
                            <th>头衔</th>
                            <th>推荐位置</th>
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
                            <td>{{$teacher->name}}
                                @if($teacher->recommend_id)
                                <img src="/static/admin/images/tui.png">
                                @endif
                            </td>
                            <td>{{$teacher->title}}</td>
                            <td>
                                <select name="recommend" id="position_id_{{$teacher->id}}">
                                    <option value="0"> -- 无 --</option>
                                    @if(count($objRecommendTecher))
                                        @foreach($objRecommendTecher as $position)
                                        <option value="{{$position->id}}"  @if($position->id == $teacher->recommend_id) selected @endif>{{$position->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td>
                                <a rel-data="{{$teacher->id}}" href="javascript:void (0);" class="btn btn-xs" onclick="recommendTeacher(this)">推荐</a>
                            </td>
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
                        <select name="recommend_position">
                            <option value="0"> -- 选择推荐 --</option>
                            @if(count($objRecommendTecher))
                                @foreach($objRecommendTecher as $position)
                                     <option value="{{$position->id}}">{{$position->name}}</option>
                                @endforeach
                            @endif
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

    function recommendTeacher(obj){
        $.ajax({
            'url':'/admin/recommend/create',
            'type':'POST',
            'data':'check_id='+$(obj).attr('rel-data')+'&type=teacher&positionId='+$('#position_id_'+$(obj).attr('rel-data')).val(),
            'success':function(responseData){
                if(responseData.msgCode==0){
                    alert('推荐成功');
                    window.location.reload();
                }else if(responseData.msgCode==-1){
                    alert('请选择推荐位');
                }else if(responseData.msgCode ==-2){
                    alert('超过了该推荐位的最大推荐数');
                    window.location.reload();
                }
            },
            'error':function(){
                alert('ajax error');
            }
        })
    }
</script>
@stop