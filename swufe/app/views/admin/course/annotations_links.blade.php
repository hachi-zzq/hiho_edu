@extends('layout.bs3_admin_layout')

@section('title')
添加注释 - 西南财经大学
@stop

@section('style')
<link href="{{\Config::get('app.pathToSource')}}/stylesheets/subtitle.css" rel="stylesheet" type="text/css">
@stop

@section('content-wrapper')

<div class="row">
    <div class="col-md-8">
        <ul class="nav nav-tabs subtitle-tabs">
            <li >
                <a href="{{route('adminQuestionHighLight',$videoId)}}">重点与问题</a>
            </li>
            <li class="active">
                <a href="{{route('adminAnnotationsLinks',$videoId)}}">注释</a>
            </li>
            <li>
                <a href="{{route('adminAppendixsGetCreate',$videoId)}}">编辑附录</a>
            </li>
        </ul>

        <div class="panel panel-box">
            <div class="subtitle-box">
                <ul id="subtitleList" class="subtitle" data-guid="{{$videoGuid}}" data-language="{{$language}}" style=""></ul>
            </div>

        </div>
    </div>


    <div class="col-md-4">
        <!-- <div id="previewContainer" class="hilitePreview">
            <iframe src="" frameborder="0"></iframe>
        </div> -->
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title"><i class="panel-title-icon fa fa-book"></i>注释</span>
            </div>
            <ul id="annotationList" class="list-group"></ul>
        </div>
    </div>
</div>

<div id="addAnnotationModal" class="modal fade" tabindex="-1" role="dialog" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddQuestionModal">×</button>
                <h4 class="modal-title" id="myModalLabel">在<span id="annotationStart" class="text-info"></span> - <span id="annotationEnd" class="text-info"></span>添加注释</h4>
            </div>
            <div class="modal-body">
                <form action="" id="addAnnotationForm" class="form-horizontal">
                    <div class="row form-group">
                        <label for="highlightTitle" class="col-sm-2 control-label tal">注释内容</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="annotationContent" rows="20" name="content"></textarea>
                        </div>
                    </div>
                </form>
            </div> <!-- / .modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" id="addAnnotationButton" class="btn btn-primary">创建注释</button>
            </div>
        </div> <!-- / .modal-content -->
    </div> <!-- / .modal-dialog -->
</div>

<div id="modifyAnnotationModal" class="modal fade" tabindex="-1" role="dialog" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddQuestionModal">×</button>
                <h4 class="modal-title" id="myModalLabel">编辑<span id="annotationStartModify" class="text-info"></span> - <span id="annotationEndModify" class="text-info"></span>处的注释</h4>
            </div>
            <div class="modal-body">
                <form action="" id="modifyAnnotationForm" class="form-horizontal">
                    <div class="row form-group">
                        <label for="highlightTitle" class="col-sm-2 control-label tal">注释内容</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="annotationContentModify" rows="20" name="content"></textarea>
                        </div>
                    </div>
                </form>
            </div> <!-- / .modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" id="modifyAnnotationButton" class="btn btn-primary">保存编辑</button>
            </div>
        </div> <!-- / .modal-content -->
    </div> <!-- / .modal-dialog -->
</div>

@stop

@section('js')
<script type="text/template" id="tplAnnotationItem">
  <li class="list-group-item" data-id="{id}" data-st="{start}" data-et="{end}" data-content-encoded="{content|encoded}">
    <div class="annotationPreview">{content}</div>
    <div class="hiliteButtons"><!-- <button class="btn btn-sm btn-primary btn-rounded btn-outline">预览注释</button> <button class="btn btn-sm btn-primary btn-rounded btn-outline">编辑选区</button> --> <button data-toggle="modal" data-target="#modifyAnnotationModal" class="btn btn-sm btn-primary btn-rounded btn-outline modifyAnnotation">编辑详情</button> <button class="btn btn-sm btn-primary btn-rounded btn-outline removeAnnotation">删除注释</button></div>
  </li>
</script>
<script type="text/template" id="tplBubble">
    <div class="clip-action">
        <div class="clip-action-inner">
            <a class="clip-action-btn addAnnotation" href="javascript:;" data-toggle="modal" data-target="#addAnnotationModal">添加注释</a>
        </div>
    </div>
</script>
<script>
    function sendFile(file,editor,welEditable) {
        data = new FormData();
        data.append("file", file);
        $.ajax({
            data: data,
            type: "POST",
            url: "/admin/imgUpload",
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                editor.insertImage(welEditable, response);
            }
        });
    }

  init.push(function () {
    if (! $('html').hasClass('ie8')) {
      $('#annotationContent').summernote({
        height: 400,
        tabsize: 2,
        codemirror: {
          theme: 'monokai'
        },
      onImageUpload: function(files, editor, welEditable) {
          sendFile(files[0],editor,welEditable);
      }
      });
    }
    if (! $('html').hasClass('ie8')) {
      $('#annotationContentModify').summernote({
        height: 400,
        tabsize: 2,
        codemirror: {
          theme: 'monokai'
        },
      onImageUpload: function(files, editor, welEditable) {
          sendFile(files[0],editor,welEditable);
      }
      });
    }
    $('#summernote-boxed').on($('html').hasClass('ie8') ? "propertychange" : "change", function () {
      var $panel = $(this).parents('.panel');
      if ($(this).is(':checked')) {
        $panel.find('.panel-body').addClass('no-padding');
        $panel.find('.panel-body > *').addClass('no-border');
      } else {
        $panel.find('.panel-body').removeClass('no-padding');
        $panel.find('.panel-body > *').removeClass('no-border');
      }
    });
  });
</script>
<script src="{{\Config::get('app.pathToSource')}}/scripts/lib/require.js" data-main="{{\Config::get('app.pathToSource')}}/scripts/admin/annotations.js"></script>
@stop
