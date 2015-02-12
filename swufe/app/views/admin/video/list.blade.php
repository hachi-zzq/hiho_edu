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
Hiho_edu-视频管理
@stop

@section('content')
<ul class="breadcrumb">
    <li><a href="#">首页</a> <span class="divider">/</span></li>
    <li><a href="#">视频管理</a> <span class="divider">/</span></li>
    <li class="active">所有视频</li>
</ul>
<div style="padding-right: 20px;" id="bar">
<a class="btn" href="javascript:void(0)" onclick="openwindow('/admin/video/upload','new_blank',600,400)">上传视频</a>
    <p style="float: right;margin-right: 20px;;">
        <select onchange="selectLocation(this)">
            <option value="0">-- 全部视频 --</option>
            <option value="7" {{$type==7?"selected":''}}>完成匹配</option>
            <option value="6" {{$type==6?"selected":''}}>正在匹配</option>
            <option value="-7" {{$type=='-7'?"selected":''}}>匹配失败</option>
            <option value="else" {{$type=='else'?"selected":''}}>其他</option>
        </select>
    </p>
</div>
@if(!empty(\Session::get('tips')))
<div class="alert alert-success">
    {{\Session::get('tips')}}
</div>
@endif
<table id="video_list" class="table table-hover" xmlns="http://www.w3.org/1999/html">
    <tr>
        <th>视频图片</th>
        <th>视频标题</th>
        <th>视频大小</th>
        <th>是否添加字幕</th>
        <th>当前状态</th>
        <th>操作</th>
    </tr>
   @if(!empty($videos))
        @foreach($videos as $video)
    <tr>
        <?php $video->load('sewise_videos_picture')?>
       <td>
           {{-- <img src="/static/admin/images/160x120.png" class="img-polaroid"> --}}
           <img src="{{$video->pic?$video->pic->src:'/static/admin/images/160x120.png'}}" class="img-polaroid" width="160" height="120">
       </td>
       <td>{{$video->title}}</td>
       <td>{{Tool::formatBytes($video->bytesize)}}</td>
       <td>{{ !empty($video->source_id)?'是':'否'}}</td>
       <td>{{ \Tool::returnStatus($video->status)}}</td>
       <td>
           @if(!empty($video->source_id))
           <span class="disabled" style="color: #aaa" title="已经上传过字幕，请等待匹配结果"> 视频匹配</span> |
           @else
           <a href="{{route('adminSubtitleAdd',array($video->id))}}" title="上传字幕文件，开始匹配">视频匹配</a> |
           @endif
           <a href="{{route('adminVideoModify',array($video->id))}}">添加信息</a> |
           <a href="{{route('adminVideoDelete',array($video->id))}}" onclick="return confirm('真的要删除？')">删除</a>
       </td>
   </tr>
        @endforeach
    <tr>
        <td colspan="7" style="text-align: center">
            <div class="pagination">
                {{-- paginate --}}
                {{ $videos->appends('type',$type)->links() }}
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