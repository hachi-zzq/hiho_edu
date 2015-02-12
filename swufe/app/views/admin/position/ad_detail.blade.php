@extends('layout.bs3_admin_layout')

@section('title')
广告位列表 - 西南财经大学
@stop

@section('content-wrapper')
<style type="text/css">

    .sort{
        width: 40px;;
    }
</style>

<div class="page-header">
    <h1><span class="text-light-gray">运营 / </span>广告位管理</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">广告列表</span>
            </div>
            <div style="padding:10px;">

            </div>
            <div style="padding:10px;">
                <a href="/admin/positions/advertisement/index" class="btn btn-primary" style="margin-left: 20px">返回广告位</a>
                <a href="{{route('adminAdCreate',$id)}}" class="btn btn-primary" style="margin-left: 20px">添加广告</a>
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
                <form method="post" action="{{route('adminAdSort',$id)}}">
                <table class="table " id="playlist">
                    <tbody>
                    <tr>
                        <th></th>
                        <th>排序</th>
                        <th>广告名称</th>
                        <th>广告类型</th>
                        <th>描述</th>
                        <th>添加时间</th>
                        <th>更新时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    @if(count($objAdList))
                    @foreach($objAdList as $list)
                        <tr>
                            <td>
                                <input type="checkbox" id="inlineCheckbox1" name="check_id[]" value="{{$list->id}}">
                            </td>
                            <td>
                                <input type="text" class="sort" name="sort[{{$list->id}}]" value="{{$list->sort}}" >
                            </td>
                            <td>{{$list->name}}</td>
                            <td>{{$list->type}}</td>
                            <td>{{$list->description}}</td>
                            <td>{{$list->created_at}}</td>
                            <td>{{$list->updated_at}}</td>
                            <td><a href="#" class="label label-info">{{$list->status==1?'投放中':'已下架'}}</a></td>
                            <td>
                                <a href="{{route('adminAdStatus',$list->id)}}" class="btn btn-xs">{{$list->status==1?'关闭':'投放'}} </a>
                                <a href="{{route('adminAdModify',$list->id)}}" class="btn btn-xs">修改</a>
                                <a href="{{route('adminAdDestroy',$list->id)}}"  class="btn btn-xs btn-danger">删除</a>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>

                </table>
                    <a href="javascript:void(0)" onclick="checkAll()">全选</a>
                    <a href="javascript:void(0)" onclick="uncheckAll()">反选</a>
                <button class="btn-xs btn" type="submit">排序</button>
                </form>
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
</script>
@stop