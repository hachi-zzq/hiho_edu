@extends('layout.bs3_admin_layout')

@section('title')
添加字幕 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">字幕 / </span>添加字幕</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        @if(!empty(\Session::get('error_tips')))
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{\Session::get('error_tips')}}</strong>
        </div>
        @endif
        <form class="panel form-horizontal" method="POST" enctype="multipart/form-data"   action="{{route('adminSubtitleAddAction')}}">
            <div class="panel-heading">
                <span class="panel-title">字幕信息</span>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputEmail2" class="col-sm-2 control-label">视频标题</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputEmail" placeholder="Email" name="title"
                               value="{{$objVideo->title}}">

                        <p class="help-block">这里可以更改视频的标题噢</p>
                    </div>
                </div>
                <!-- / .form-group -->
                <div class="form-group">
                    <label for="inputIsAdmin" class="col-sm-2 control-label">视频语言</label>

                    <div class="col-sm-10">
                        <div class="radio">
                            <label>
                                <input type="radio" name="video_language" id="optionsIsAdmin0" value="zh_cn" class="px"
                                        {{$objVideo->language=='zh_cn'?'checked':''}}>
                                <span class="lbl">中文</span>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="video_language" id="optionsIsAdmin1" value="en" class="px" {{$objVideo->language=='en'?'checked':''}}>
                                <span class="lbl">英文</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputIsAdmin" class="col-sm-2 control-label">字幕语言</label>

                    <div class="col-sm-10">
                        <div class="radio">
                            <label>
                                <input type="radio" name="language" id="optionsIsAdmin0" value="zh_cn" class="px"
                                       checked="checked">
                                <span class="lbl">中文</span>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="language" id="optionsIsAdmin1" value="en" class="px"">
                                <span class="lbl">英文</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail2" class="col-sm-2 control-label">字幕</label>

                    <div class="col-sm-10">
                        <ul class="nav nav-tabs bs-tabdrop-example">
                            <li  id="srt" class="active"><a href="#bs-tabdrop-tab1" data-toggle="tab">SRT字幕文件上传</a></li>
                            <li id="add"><a href="#bs-tabdrop-tab2" data-toggle="tab">手动输入</a></li>

                        </ul>
                        <div class="tab-content tab-content-bordered">
                            <div class="tab-pane active" id="bs-tabdrop-tab1">
                                <input type="file" id="file_subtitle" name="file_subtitle"  style="float: left"><button type="button" onclick="convert()" class="ms-btn">预览</button>
                            </div>
                            <div class="tab-pane" id="bs-tabdrop-tab2">
                                <textarea class="form-control" rows="5" placeholder="字幕" name="subtitle" id="subtitle"></textarea>
                            </div>
                        </div>
                    </div>
                </div>



                <input type="hidden" name="video_id" value="{{$objVideo->id}}">
                <!-- / .form-group -->
                <div class="form-group" style="margin-bottom: 0;">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">开始匹配</button>
                    </div>
                </div>
                <!-- / .form-group -->
            </div>
        </form>
    </div>
</div>
<script>
    function convert()
    {
        $.ajaxFileUpload
        (
            {
                url:'/admin/video/subtitleConvert',
                secureuri:false,
                fileElementId:'file_subtitle',
                dataType: 'json',
                success: function (data, status)
                {
                    jsonData = eval(data);
                    if(jsonData.msgCode=='0'){
                        $('#srt').removeClass('active');
                        $('#add').addClass('active');
                        $('#bs-tabdrop-tab2').addClass('active');
                        $('#bs-tabdrop-tab1').removeClass('active');
                        $('#subtitle').empty().append(jsonData.data);
                    }else if(jsonData.msgCode=='-2'){
                        alert('请选择要上传的文件');
                    }else if(jsonData.msgCode == '-1'){
                        alert('文件格式不合法');
                    }
                },
                error: function (data, status, e)
                {
                    alert(e);
                }
            }
        )

        return false;

    }
</script>

@stop

@section('js')
@parent
<script type="text/javascript" src="/static/admin/js/ajaxfileupload.js"></script>

@stop
