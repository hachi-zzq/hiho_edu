@extends('layout.bs3_admin_layout')

@section('title')
广告修改 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">运营 / </span>修改广告</h1>
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
        <form class="panel form-horizontal"  enctype="multipart/form-data"  method="POST">
            <div class="panel-heading">
                <span class="panel-title">广告资料</span>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="inputEmail2" class="col-sm-2 control-label">广告位</label>

                    <div class="col-sm-10">

                        <input type="text" readonly="readonly"  class="form-control" id="inputEmail"   value="{{$objPosition->name}}">

                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail2" class="col-sm-2 control-label">广告位类型</label>

                    <div class="col-sm-10">
                        <input type="text" readonly="readonly" class="form-control" id="inputEmail"  value="{{$objPosition->type}}" >

                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail2" class="col-sm-2 control-label">广告名称</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputEmail" name="ad_name"  value="{{$objAd->name}}">

                    </div>
                </div>
                <div class="form-group">
                    <label for="inputNickname" class="col-sm-2 control-label">广告描述</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputNickname" name="ad_description" value="{{$objAd->description}}">
                    </div>

                </div>
                @if($objAd->type == 'picture' || $objAd->type== 'rotation')
                <script>
                    init.push(function () {
                        $('#input-upload-portrait').pixelFileInput({ placeholder: '未选择文件...' });
                    })
                </script>
                <div class="form-group">
                    <label for="inputPortrait" class="col-sm-2 control-label">上传图片</label>

                    <div class="col-sm-10">
                        <input type="file" id="input-upload-portrait" name="ad_img">
                        @if($objAd->img_src)
                        <img src="{{$objAd->img_src}}">
                        @endif
                    </div>

                </div>

                <div class="form-group">
                    <label for="inputNickname" class="col-sm-2 control-label">广告连接</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputNickname" name="ad_href" value="{{$objAd->href}}">
                    </div>

                </div>
                @endif

                @if($objAd->type == 'text')
                <div class="form-group">
                    <label for="inputPortrait" class="col-sm-2 control-label">广告文字</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputNickname" name="ad_text" value="{{$objAd->text_name}}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputNickname" class="col-sm-2 control-label">广告连接</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputNickname" name="ad_href" value="{{$objAd->href}}">
                    </div>

                </div>
                @endif

                @if($objAd->type == 'code')
                <div class="form-group">
                    <label for="inputNickname" class="col-sm-2 control-label">代码广告</label>

                    <div class="col-sm-10">
                        <textarea style="width: 1330px;height: 100px;" name="ad_code">{{$objAd->code}}</textarea>
                    </div>

                </div>
                @endif

                <div class="form-group">
                    <label for="inputIsAdmin" class="col-sm-2 control-label">审核通过</label>

                    <div class="col-sm-10">
                        <div class="radio">
                            <label>
                                <input type="radio" name="status" id="optionsIsAdmin0" value="1" class="px"
                                      @if($objAd->status==1) checked="checked" @endif>
                                <span class="lbl">是</span>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="status" id="optionsIsAdmin1" value="0" class="px"" @if($objAd->status==0) checked="checked" @endif>
                                <span class="lbl">否</span>
                            </label>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="id" value="{{$objAd->id}}">
                <input type="hidden" name="type" value="{{$objAd->type}}">
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