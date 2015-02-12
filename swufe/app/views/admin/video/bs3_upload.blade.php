@extends('layout.bs3_admin_layout')

@section('title')
上传视频 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">视频 / </span>添加视频</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">上传视频</span>
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

            <!-- Javascript -->
            <script>
                init.push(function () {
                    $("#dropzonejs-example").dropzone({
                        url: "/admin/video/doUpload",
                        paramName: "Filedata", // The name that will be used to transfer the file
                        maxFilesize: 2048, // MB
                        addRemoveLinks : true,
                        dictResponseError: "上传错误",
                        dictCancelUpload:'取消上传',
                        dictFileTooBig:'最大只能上传2G大小文件',
                        autoProcessQueue: true,
                        thumbnailWidth: 138,
                        thumbnailHeight: 120,

                        previewTemplate: '<div class="dz-preview dz-file-preview"><div class="dz-details"><div class="dz-filename"><span data-dz-name></span></div><div class="dz-size">File size: <span data-dz-size></span></div><div class="dz-thumbnail-wrapper"><div class="dz-thumbnail"><img data-dz-thumbnail><span class="dz-nopreview">No preview</span><div class="dz-success-mark"><i class="fa fa-check-circle-o"></i></div><div class="dz-error-mark"><i class="fa fa-times-circle-o"></i></div><div class="dz-error-message"><span data-dz-errormessage></span></div></div></div></div><div class="progress progress-striped active"><div class="progress-bar progress-bar-success" data-dz-uploadprogress></div></div></div>',

                        resize: function(file) {
                            var info = { srcX: 0, srcY: 0, srcWidth: file.width, srcHeight: file.height },
                                srcRatio = file.width / file.height;
                            if (file.height > this.options.thumbnailHeight || file.width > this.options.thumbnailWidth) {
                                info.trgHeight = this.options.thumbnailHeight;
                                info.trgWidth = info.trgHeight * srcRatio;
                                if (info.trgWidth > this.options.thumbnailWidth) {
                                    info.trgWidth = this.options.thumbnailWidth;
                                    info.trgHeight = info.trgWidth / srcRatio;
                                }
                            } else {
                                info.trgHeight = file.height;
                                info.trgWidth = file.width;
                            }
                            return info;
                        }
                    });
                });
            </script>
            <div class="panel-body">
                <div class="panel">
                    <div class="panel-heading">
                        <!--                        <span class="panel-title">Dropzone.js file uploads</span>-->
                    </div>
                    <div class="panel-body">
                        <div class="note note-info">目前只支持FLV,MP4两种格式，试着把要上传的视频拖拽到下面，是不是很酷？！</div>

                        <div id="dropzonejs-example" class="dropzone-box">
                            <div class="dz-default dz-message">
                                <i class="fa fa-cloud-upload"></i>
                                Drop files in here<br><span class="dz-text-small">or click to pick manually</span>
                            </div>
                            <form action="/admin/video/doUpload" method="post" enctype="multipart/form-data" >
                                <div class="fallback">
                                    <input name="Filedata" type="file" multiple="" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@stop

