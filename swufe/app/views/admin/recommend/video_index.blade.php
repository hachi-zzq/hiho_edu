@extends('layout.bs3_admin_layout')

@section('title')
视频管理 - 西南财经大学
@stop


@section('content-wrapper')


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


            </div>
            <div class="panel-body">
                <table class="table" id="video_list">
                    <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>视频标题</th>
                        <th>推荐位置</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <form method="post" action="/admin/recommend/create">
                        @if(count($videos))
                        @foreach($videos as $video)
                        <tr>

                            <td>
                                <input type="checkbox" id="id" value="{{$video->id}}" name="check_id[]">
                            </td>
                            <td>{{$video->id}}</td>
                            <th title="{{$video->title}}">{{$video->title}}
                                @if($video->recommend_id)
                                <img src="/static/admin/images/tui.png">
                                @endif
                            </th>
                            <th>
                                <select  id="position_id_{{$video->id}}">
                                    <option value="0"> -- 无 --</option>
                                    @if(count($objRecommendVideo))
                                        @foreach($objRecommendVideo as $position)
                                        <option value="{{$position->id}}" @if($position->id == $video->recommend_id) selected @endif>{{$position->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </th>
                            <th>
                                <a rel-data="{{$video->id}}" href="javascript:void (0);" class="btn btn-xs" onclick="recommendVideo(this)">推荐</a>
                            </th>
                        </tr>
                        @endforeach
                        @endif
                        <tr>
                            <td colspan="5">
                            {{$videos->links()}}
                            </td>
                        </tr>
                    </tbody>

                </table>
                <div>

                    <a href="javascript:void(0)" onclick="checkAll()">全选</a>
                    <a href="javascript:void(0)" onclick="uncheckAll()">反选</a>


                    <select name="positionId">
                        <option value="0"> -- 选择推荐 --</option>
                        @if(count($objRecommendVideo))
                        @foreach($objRecommendVideo as $position)
                        <option value="{{$position->id}}">{{$position->name}}</option>
                        @endforeach
                        @endif
                    </select>
                    <button type="button" >推荐</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

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

    function recommendVideo(obj){
        $.ajax({
            'url':'/admin/recommend/create',
            'type':'POST',
            'data':'check_id='+$(obj).attr('rel-data')+'&type=video&positionId='+$('#position_id_'+$(obj).attr('rel-data')).val(),
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

