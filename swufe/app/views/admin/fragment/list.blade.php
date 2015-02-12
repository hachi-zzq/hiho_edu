@extends('layout.admin_layout')

@section('css')
    @parent
    <style type="text/css">
        #video_list tr td,#video_list tr th{
            vertical-align: middle;
            text-align: center;
        }
        #bar select{
            width: 160px;;
        }
    </style>
@stop


@section('title')
Hiho_edu-碎片管理
@stop

@section('content')
<ul class="breadcrumb">
    <li><a href="#">首页</a> <span class="divider">/</span></li>
    <li><a href="#">碎片管理</a> <span class="divider">/</span></li>
    <li class="active">所有碎片</li>
</ul>
@if(!empty(\Session::get('tips')))
<div class="alert alert-success">
    {{\Session::get('tips')}}
</div>
@endif
<table id="video_list" class="table table-hover" xmlns="http://www.w3.org/1999/html">
    <tr>
        <th>#</th>
        <th>碎片图片</th>
        <th>碎片标题</th>
        <th>操作</th>
    </tr>
   @if(!empty($fragments))
        @foreach($fragments as $video)
    <tr>
        <td>{{$video->id}}</td>
       <td>
           {{-- <img src="/static/admin/images/160x120.png" class="img-polaroid"> --}}
           <img src="{{$video->sewise_videos_picture[0]->src}}" class="img-polaroid" width="160" height="120">
       </td>
       <td>{{$video->title}}</td>
       <td>
           <a href="#">查看信息</a> |
           <a href="{{route('adminFragmentDelete',array($video->id))}}" onclick="return confirm('真的要删除？')">删除</a>
       </td>
   </tr>
        @endforeach
    <tr>
        <td colspan="7" style="text-align: center">
            <div class="pagination">
                {{-- paginate --}}
                {{ $fragments->links() }}
            </div>
        </td>
    </tr>
    @endif
</table>
@stop

@section('js')
@parent
<script type="text/javascript">
    /*
     弹出窗口居中
     */
    function openwindow(url,name,iWidth,iHeight)
    {
        var url;                             //转向网页的地址;
        var name;                            //网页名称，可为空;
        var iWidth;                          //弹出窗口的宽度;
        var iHeight;                         //弹出窗口的高度;
        //获得窗口的垂直位置
        var iTop = (window.screen.availHeight-30-iHeight)/2;
        //获得窗口的水平位置
        var iLeft = (window.screen.availWidth-10-iWidth)/2;
        window.open(url,name,'height='+iHeight+',,innerHeight='+iHeight+',width='+iWidth+',innerWidth='+iWidth+',top='+iTop+',left='+iLeft+',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
    }

    /*
    |----------
    |select location
    |—————
     */
    function selectLocation(objSelect){
        var selectIndex = objSelect.selectedIndex;
        var selectValue = objSelect.options[selectIndex].value;
        window.location = '/admin/videos/'+selectValue;
    }
</script>

@stop