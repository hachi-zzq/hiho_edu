@extends('layout.bs3_admin_layout')

@section('title')
添加注释片段 - 西南财经大学
@stop

@section('style')
<link href="{{\Config::get('app.pathToSource')}}/stylesheets/subtitle.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/theme/blackboard.min.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/theme/monokai.min.css">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/xml/xml.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/codemirror/2.36.0/formatting.min.js"></script>
@stop

@section('content-wrapper')

<div class="row">
  <div class="col-md-8">
      <ul class="nav nav-tabs">
        <li>
          <a href="{{route('adminQuestionHighLight',$video_id)}}">重点与问题</a>
        </li>
        <li>
            <a href="{{route('adminAnnotationsLinks',$video_id)}}">注释与链接</a>
        </li>
        <li class="active">
          <a href="#">编辑附录</a>
        </li>
      </ul>

      <div class="panel">
        <form action="{{route('adminAppendixsCreate')}}" method="post">
          <div class="panel-heading">
            <span class="panel-title">视频附录</span>
          </div>
          <div class="panel-body">
            <textarea class="form-control" id="summernote-example" rows="20" name="appendix_content">{{$objAppendix?$objAppendix->content:''}}</textarea>
          </div>
          <div class="panel-footer text-right">
              <input type="hidden" name="video_id" value="{{$video_id}}">
            <button class="btn btn-primary">提交</button>
          </div>
        </form>
      </div>
  </div>


  <div class="col-md-4">
    
  </div>
</div>


@stop

@section('js')
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
      $('#summernote-example').summernote({
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
@stop