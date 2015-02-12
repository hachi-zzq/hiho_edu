@extends('layout.bs3_admin_layout')

@section('title')
上传视频附件 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray"> 视频 / </span>字幕信息修改</h1>
</div> <!-- / .page-header -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div style="padding:10px;">
                <a href="javascript:void(0);" onclick="history.back();" class="btn btn-primary">返回</a>
            </div>
            <div class="panel-heading">
                <span class="panel-title">上传视频附件</span>
            </div>

            <div class="alert alert-page alert-dark warning" style="display:none;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span></span>
            </div>
            <div class="alert alert-page alert-danger alert-dark fail" style="display:none;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span></span>
            </div>
            <div class="alert alert-page alert-success alert-dark success" style="display:none;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span></span>
            </div>

            <!-- Javascript -->
            <script>
                init.push(function () {
                    $("#dropzonejs-example").dropzone({
                        url: "/admin/course/uploadAttachment/{{$video_id}}",
                        paramName: "Filedata", // The name that will be used to transfer the file
                        maxFilesize: 1024, // MB
                        addRemoveLinks: true,
                        dictResponseError: "Can't upload file!",
                        autoProcessQueue: true,
                        thumbnailWidth: 138,
                        thumbnailHeight: 120,

                        previewTemplate: '<div class="dz-preview dz-file-preview"><div class="dz-details"><div class="dz-filename"><span data-dz-name></span></div><div class="dz-size">File size: <span data-dz-size></span></div><div class="dz-thumbnail-wrapper"><div class="dz-thumbnail"><img data-dz-thumbnail><span class="dz-nopreview">No preview</span><div class="dz-success-mark"><i class="fa fa-check-circle-o"></i></div><div class="dz-error-mark"><i class="fa fa-times-circle-o"></i></div><div class="dz-error-message"><span data-dz-errormessage></span></div></div></div></div><div class="progress progress-striped active"><div class="progress-bar progress-bar-success" data-dz-uploadprogress></div></div></div>',

                        resize: function (file) {
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

                <div id="dropzonejs-example" class="dropzone-box">
                    <div class="dz-default dz-message">
                        <i class="fa fa-cloud-upload"></i>
                        Drop files in here<br><span class="dz-text-small">or click to pick manually</span>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="fallback">
                            <input name="Filedata" type="file" multiple=""/>
                        </div>
                    </form>
                </div>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>名称</th>
                        <th>小大</th>
                        <th>上传时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($objAttachment))
                    @foreach($objAttachment as $attachment)
                    <tr>
                        <td>{{$attachment->id}}</td>
                        <td>{{$attachment->title}}</td>
                        <td>{{Tool::formatBytes($attachment->size)}}</td>
                        <td>{{$attachment->created_at}}</td>
                        <td>
                            <a href="{{$attachment->path}}" target="_blank" class="btn btn-xs">下载</a>
                            <a href="{{route('adminAttachmentDestroy',$attachment->id)}}" class="btn btn-xs btn-danger">删除</a>
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="8">
                            <div class="pagination">
                            </div>
                        </td>
                    </tr>
                    @endif

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.delete').click(function(){
            var currentTr = $(this).parent().parent();
            var attTile = currentTr.children('td').eq(1).text();
            var attId = currentTr.children('td').eq(0).text();
            if(confirm('确定删除 '+attTile+'?')){
                $.post(
                    '/admin/course/deleteAttachment/'+attId,
                    {},
                    function(data){
                        var obj = $.parseJSON(data);
                        if(obj.status == 0) {
                            currentTr.remove();
                            $('.success').show().find('span').text(obj.message);
                        }
                        else if(obj.status == -1){
                            $('.fail').show().find('span').text(obj.message);
                        }
                    }
                );
            }
        });
    });
</script>
@stop