@extends('layout.bs3_admin_layout')

@section('title')
添加注释片段 - 西南财经大学
@stop

@section('style')
<link href="{{\Config::get('app.pathToSource')}}/stylesheets/subtitle.css" rel="stylesheet" type="text/css">
@stop

@section('content-wrapper')

<div class="row">
  <div class="col-md-8">
      <ul class="nav nav-tabs subtitle-tabs">
        <li class="active">
          <a href="{{route('adminQuestionHighLight',$videoId)}}">重点与问题</a>
        </li>
        <li>
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
    <div id="previewContainer" class="hilitePreview">
      <!-- <iframe src="" frameborder="0"></iframe> -->
    </div>
    <div class="panel">
      <div class="panel-heading">
        <span class="panel-title"><i class="panel-title-icon fa fa-film"></i>重要片段</span>
      </div>
      <ul id="highlightList" class="list-group"></ul>
    </div>
  </div>
</div>

<div id="addQuestionModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddQuestionModal">×</button>
        <h4 class="modal-title">在<span id="questionTime" class="text-info"></span>处加问题</h4>
      </div>
      <div class="modal-body">
        <form id="addQuestionForm" class="form-horizontal">
          <div class="row form-group">
            <label for="questionDescription" class="col-sm-2 control-label tal">问题</label>
            <div class="col-sm-10">
              <input type="text" name="name" id="questionDescription" class="form-control">
            </div>
          </div>
          <hr>
          <div class="row form-group">
            <label class="col-sm-1 control-label tal">选项</label>
            <label class="col-sm-1 control-label" for="choiceA">A</label>
            <div class="col-sm-8">
              <input type="text" id="choiceA" class="form-control">
            </div>
            <div class="col-sm-2">
              <label class="checkbox-inline">
                <input type="checkbox" class="px questionAnswer">
                <span class="lbl">正确答案</span>
              </label>
            </div>
          </div>
          <div class="row form-group">
            <label class="col-sm-1 control-label tal"></label>
            <label class="col-sm-1 control-label" for="choiceB">B</label>
            <div class="col-sm-8">
              <input type="text" id="choiceB" class="form-control">
            </div>
            <div class="col-sm-2">
              <label class="checkbox-inline">
                <input type="checkbox" class="px questionAnswer">
                <span class="lbl">正确答案</span>
              </label>
            </div>
          </div>
          <div class="row form-group">
            <label class="col-sm-1 control-label tal"></label>
            <label class="col-sm-1 control-label" for="choiceC">C</label>
            <div class="col-sm-8">
              <input type="text" id="choiceC" class="form-control">
            </div>
            <div class="col-sm-2">
              <label class="checkbox-inline">
                <input type="checkbox" class="px questionAnswer">
                <span class="lbl">正确答案</span>
              </label>
            </div>
          </div>
          <div class="row form-group">
            <label class="col-sm-1 control-label tal"></label>
            <label class="col-sm-1 control-label" for="choiceD">D</label>
            <div class="col-sm-8">
              <input type="text" id="choiceD" class="form-control">
            </div>
            <div class="col-sm-2">
              <label class="checkbox-inline">
                <input type="checkbox" class="px questionAnswer">
                <span class="lbl">正确答案</span>
              </label>
            </div>
          </div>
          <hr>
          <div class="row">
            <label class="col-sm-2 control-label tal">错误操作</label>
            <div class="col-sm-10">

              <ul id="uidemo-tabs-default-demo" class="nav nav-tabs nav-tabs-simple">
                <li class="active errorAction">
                  <a href="#questionSkipTab" data-toggle="tab">跳转到指定时间</a>
                </li>
                <li class="errorAction">
                  <a href="#questionWarnTab" data-toggle="tab">添加提示</a>
                </li>
                <li class="errorAction">
                  <a href="#questionContinueTab" data-toggle="tab">继续播放</a>
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane fade in active" id="questionSkipTab">
                  <div class="row">
                    <div class="col-sm-4">
                      <label class="radio">
                        <input type="radio" name="styled-r1" checked class="px">
                        <span class="lbl">从重点片段选择</span>
                      </label>
                    </div>
                    <div class="col-sm-8">
                      <select id="questionHighlights" class="form-control" style="margin-bottom: 15px;"></select>
                    </div>
                  </div>
                </div> <!-- / .tab-pane -->
                <div class="tab-pane fade" id="questionWarnTab">
                  <textarea id="questionWarning" class="form-control" rows="3"></textarea>
                </div> <!-- / .tab-pane -->
                <div class="tab-pane fade" id="questionContinueTab">
                  <p>继续播放视频</p>
                </div> <!-- / .tab-pane -->
              </div> <!-- / .tab-content -->

            </div>
          </div>
        </form>
      </div> <!-- / .modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" id="addQuestionButton" class="btn btn-primary">创建问题</button>
      </div>
    </div> <!-- / .modal-content -->
  </div> <!-- / .modal-dialog -->
</div>

<div id="modifyQuestionModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="modifyAddQuestionModal">×</button>
        <h4 class="modal-title">编辑<span id="questionTimeModify" class="text-info"></span>处的问题</h4>
      </div>
      <div class="modal-body">
        <form id="modifyQuestionForm" class="form-horizontal">
          <div class="row form-group">
            <label for="questionDescriptionModify" class="col-sm-2 control-label tal">问题</label>
            <div class="col-sm-10">
              <input type="text" name="name" id="questionDescriptionModify" class="form-control">
            </div>
          </div>
          <hr>
          <div class="row form-group">
            <label class="col-sm-1 control-label tal">选项</label>
            <label class="col-sm-1 control-label" for="choiceAModify">A</label>
            <div class="col-sm-8">
              <input type="text" id="choiceAModify" class="form-control choiceModify">
            </div>
            <div class="col-sm-2">
              <label class="checkbox-inline">
                <input type="checkbox" class="px questionAnswerModify">
                <span class="lbl">正确答案</span>
              </label>
            </div>
          </div>
          <div class="row form-group">
            <label class="col-sm-1 control-label tal"></label>
            <label class="col-sm-1 control-label" for="choiceBModify">B</label>
            <div class="col-sm-8">
              <input type="text" id="choiceBModify" class="form-control choiceModify">
            </div>
            <div class="col-sm-2">
              <label class="checkbox-inline">
                <input type="checkbox" class="px questionAnswerModify">
                <span class="lbl">正确答案</span>
              </label>
            </div>
          </div>
          <div class="row form-group">
            <label class="col-sm-1 control-label tal"></label>
            <label class="col-sm-1 control-label" for="choiceCModify">C</label>
            <div class="col-sm-8">
              <input type="text" id="choiceCModify" class="form-control choiceModify">
            </div>
            <div class="col-sm-2">
              <label class="checkbox-inline">
                <input type="checkbox" class="px questionAnswerModify">
                <span class="lbl">正确答案</span>
              </label>
            </div>
          </div>
          <div class="row form-group">
            <label class="col-sm-1 control-label tal"></label>
            <label class="col-sm-1 control-label" for="choiceDModify">D</label>
            <div class="col-sm-8">
              <input type="text" id="choiceDModify" class="form-control choiceModify">
            </div>
            <div class="col-sm-2">
              <label class="checkbox-inline">
                <input type="checkbox" class="px questionAnswerModify">
                <span class="lbl">正确答案</span>
              </label>
            </div>
          </div>
          <hr>
          <div class="row">
            <label class="col-sm-2 control-label tal">错误操作</label>
            <div class="col-sm-10">

              <ul id="uidemo-tabs-default-demo" class="nav nav-tabs nav-tabs-simple">
                <li class="active errorActionModify">
                  <a href="#questionModifySkipTab" data-toggle="tab">跳转到指定时间</a>
                </li>
                <li class="errorActionModify">
                  <a href="#questionModifyWarnTab" data-toggle="tab">添加提示</a>
                </li>
                <li class="errorActionModify">
                  <a href="#questionModifyContinueTab" data-toggle="tab">继续播放</a>
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane fade in active" id="questionModifySkipTab">
                  <div class="row">
                    <div class="col-sm-4">
                      <label class="radio">
                        <input type="radio" name="styled-r1" checked class="px">
                        <span class="lbl">从重点片段选择</span>
                      </label>
                    </div>
                    <div class="col-sm-8">
                      <select id="questionHighlightsModify" class="form-control" style="margin-bottom: 15px;"></select>
                    </div>
                  </div>
                </div> <!-- / .tab-pane -->
                <div class="tab-pane fade" id="questionModifyWarnTab">
                  <textarea id="questionWarningModify" class="form-control" rows="3"></textarea>
                </div> <!-- / .tab-pane -->
                <div class="tab-pane fade" id="questionModifyContinueTab">
                  <p>继续播放视频</p>
                </div> <!-- / .tab-pane -->
              </div> <!-- / .tab-content -->

            </div>
          </div>
        </form>
      </div> <!-- / .modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" id="modifyQuestionButton" class="btn btn-primary">保存编辑</button>
      </div>
    </div> <!-- / .modal-content -->
  </div> <!-- / .modal-dialog -->
</div>

<div id="addHighlightModal" class="modal fade" tabindex="-1" role="dialog" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddQuestionModal">×</button>
        <h4 class="modal-title">添加<span id="highlightStart" class="text-info"></span> - <span id="highlightEnd" class="text-info"></span>为重要片段</h4>
      </div>
      <div class="modal-body">
        <form action="" id="addHighlightForm" class="form-horizontal">
          <div class="row form-group">
            <label for="highlightTitle" class="col-sm-2 control-label tal">片段标题</label>
            <div class="col-sm-10">
              <input type="text" name="name" id="highlightTitle" class="form-control">
            </div>
          </div>
          <div class="row form-group">
            <label for="highlightDescription" class="col-sm-2 control-label tal">片段描述</label>
            <div class="col-sm-10">
              <textarea id="highlightDescription" class="form-control" rows="3"></textarea>
            </div>
          </div>
        </form>
      </div> <!-- / .modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" id="addHighlightButton" class="btn btn-primary">创建片段</button>
      </div>
    </div> <!-- / .modal-content -->
  </div> <!-- / .modal-dialog -->
</div>

<div id="modifyHighlightModal" class="modal fade" tabindex="-1" role="dialog" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAddQuestionModal">×</button>
        <h4 class="modal-title">编辑<span id="highlightStartModify" class="text-info"></span> - <span id="highlightEndModify" class="text-info"></span>处的重要片段</h4>
      </div>
      <div class="modal-body">
        <form action="" id="modifyHighlightForm" class="form-horizontal">
          <div class="row form-group">
            <label for="highlightTitleModify" class="col-sm-2 control-label tal">片段标题</label>
            <div class="col-sm-10">
              <input type="text" name="name" id="highlightTitleModify" class="form-control">
            </div>
          </div>
          <div class="row form-group">
            <label for="highlightDescriptionModify" class="col-sm-2 control-label tal">片段描述</label>
            <div class="col-sm-10">
              <textarea id="highlightDescriptionModify" class="form-control" rows="3"></textarea>
            </div>
          </div>
        </form>
      </div> <!-- / .modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" id="modifyHighlightButton" class="btn btn-primary">保存编辑</button>
      </div>
    </div> <!-- / .modal-content -->
  </div> <!-- / .modal-dialog -->
</div>

@stop

@section('js')
<script type="text/template" id="tplHighlightItem">
  <li class="list-group-item" data-id="{id}" data-st="{start}" data-et="{end}" data-heading-encoded="{heading|encoded}" data-description-encoded="{description|encoded}">
    {heading}
    <div class="hiliteButtons"><!-- <button class="btn btn-sm btn-primary btn-rounded btn-outline">预览重点</button> <button class="btn btn-sm btn-primary btn-rounded btn-outline">编辑选区</button> --> <button data-toggle="modal" data-target="#modifyHighlightModal" class="btn btn-sm btn-primary btn-rounded btn-outline modifyInfo">编辑描述</button> <button class="btn btn-sm btn-primary btn-rounded btn-outline removeHighlight">删除重点</button></div>
  </li>
</script>
<script type="text/template" id="tplHighlightOption">
  <option value="{value}">{description}</option>
</script>
<script type="text/template" id="tplQuestion">
  <ul class="quiz" data-id="{id}" data-time="{time}" data-stem-encoded="{stem|encoded}" data-choices="{choices}" data-answers="{answers}" data-action="{action}" data-actiondetail="{actionDetail}" data-highlightid="{highlightId}">
    <li class="quiz-item">
      <span class="questionStem">{stem}</span>
      <div class="quiz-action">
        <a href="javascript:;" data-toggle="modal" data-target="#modifyQuestionModal" data-id="{id}" class="edit-quiz">编辑</a>
        <a href="javascript:;" data-id="{id}" class="delete-quiz">删除</a>
      </div>
    </li>
  </ul>
</script>
<script type="text/template" id="tplQuestionButton">
  <a href="javascript:;" class="add-quiz" data-toggle="modal" data-target="#addQuestionModal" data-time="{time}">添加问题</a>
</script>
<script type="text/template" id="tplBubble">
  <div class="clip-action">
    <div class="clip-action-inner">
      <a class="clip-action-btn addHighlight" href="javascript:;" data-toggle="modal" data-target="#addHighlightModal">添加为片段</a>
    </div>
  </div>
</script>
<script src="{{\Config::get('app.pathToSource')}}/scripts/lib/require.js" data-main="{{\Config::get('app.pathToSource')}}/scripts/admin/questions.js"></script>
@stop
