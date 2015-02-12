define(['subtitleselect'], function(subtitleselect){
  jQuery(function($){
    'use strict';

    jQuery.extend({
      stringify: function stringify(obj) {
        var t = typeof (obj);
        if (t != "object" || obj === null) {
          // simple data type
          if (t == "string") obj = '"' + obj + '"';
          return String(obj);
        } else {
          // recurse array or object
          var n, v, json = [], arr = (obj && obj.constructor == Array);

          for (n in obj) {
            v = obj[n];
            t = typeof(v);
            if (obj.hasOwnProperty(n)) {
                if (t == "string") v = '"' + v + '"'; else if (t == "object" && v !== null) v = jQuery.stringify(v);
                json.push((arr ? "" : '"' + n + '":') + String(v));
            }
          }
          return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
        }
      }
    });

    var $subtitleList = $('#subtitleList').empty(),
      $annotationList = $('#annotationList'),
      $annotationStart = $('#annotationStart'),
      $annotationEnd = $('#annotationEnd'),
      $addAnnotationModal = $('#addAnnotationModal'),
      videoInfo = $subtitleList.data(),
      tplAnnotationItem = $.trim($('#tplAnnotationItem').remove().html()),
      selector;

    $.getJSON('/admin/getCourseSubtitle?video_guid=' + videoInfo.guid + '&language=' + videoInfo.language + '&type=json&version=2', function(subtitle){
      //console.log('subtitle', subtitle);
      if (subtitle == '' || subtitle == undefined){
        console.log('Subtitle load error.');
      } else {
        setSubtitle(subtitle);
      }
    }).fail(function(){
      $subtitleList.html('<li>Failed to load subtitles.</li>');
    });

    initAnnotationControl();

    function setSubtitle(subtitle){
      // annotations
      var annotationSchema = subtitle.annotationSchema,
        aIdIndex = annotationSchema.indexOf('id'),
        aStIndex = annotationSchema.indexOf('st'),
        aEtIndex = annotationSchema.indexOf('et'),
        aContentIndex = annotationSchema.indexOf('content'),
        annotationSerie = subtitle.annotations,
        annotations = [],
        annotationFragments = [],
        annotationOptions = [],
        currentAnnotation = 0,
        isInAnnotation;
      if(annotationSerie.length){
        for(var aIndex = 0, aLength = annotationSerie.length; aIndex < aLength; aIndex++){
          var aCurrent = annotationSerie[aIndex];
          annotations.push({
            id: aCurrent[aIdIndex],
            start: aCurrent[aStIndex],
            end: aCurrent[aEtIndex],
            content: aCurrent[aContentIndex]
          });
          annotationFragments.push(tplAnnotationItem.
            replace(/\{id\}/g, aCurrent[aIdIndex]).
            replace(/\{start\}/g, aCurrent[aStIndex]).
            replace(/\{end\}/g, aCurrent[aEtIndex]).
            replace(/\{content\}/g, getText(aCurrent[aContentIndex])).
            replace(/\{content\|encoded\}/g, encodeURIComponent(aCurrent[aContentIndex]))
          );
        }
        $annotationList.html(annotationFragments.join(''));
      }

      // subtitles
      var srt = subtitle.subtitles,
        subtitleSchema = subtitle.subtitleSchema,
        tokenIndex = subtitleSchema.indexOf('token'),
        stIndex = subtitleSchema.indexOf('st'),
        etIndex = subtitleSchema.indexOf('et'),
        scoreIndex = subtitleSchema.indexOf('score');

      var wordIndex = 0;
      var lineTpl = '<li class="line"></li>';
      var wordTpl = '<span class="word"></span>';
      var subtitleArray = /*_self.subtitleArray =*/ [];

      for (var i = 0, srtLength = srt.length; i < srtLength; i++){
        var lineLength = srt[i].length;
        var lineObj = $(lineTpl);
        lineObj.attr({"id": 'line_' + i}).data({"index": i});
        var pre = null;
        var lineProperties = {
          "index": i,
          "st": srt[i][0][stIndex],
          "et": srt[i][lineLength - 1][etIndex]
        };
        for (var j = 0; j < lineLength; j++){
          var wordObj = $(wordTpl);
          var word = srt[i][j];
          var wordTxt = word[tokenIndex];
          var addSpace = false;
          var wordProperties = {
            "index": wordIndex,
            "st": word[stIndex],
            "et": word[etIndex]
          };

          if (j == 0){
            lineObj.attr(lineProperties).data(lineProperties);
          }

          //添加词之间的空白
          if (pre != null && /^(\w|\d)/.test(wordTxt) && (!(/^(ve|s|ll)$/i).test(wordTxt) || !(/^(\'|’)$/).test(pre))){
            lineObj.append('<pre class="space' + (isInAnnotation ? ' word-highlight" data-highlightIndex="' + currentAnnotation + '"' : '"') + '> </pre>');
            addSpace = true;
          }
          pre = wordTxt;

          if (addSpace){
            wordTxt = ' ' + wordTxt;
          }

          wordObj.attr($.extend({"id": 'word_' + wordIndex}, wordProperties)).data(wordProperties);
          wordObj.text(wordTxt);

          wordObj.appendTo(lineObj);
          subtitleArray.push(wordObj);
          wordIndex++;

          // mark annotations
          if(annotations[currentAnnotation] && wordProperties.st === annotations[currentAnnotation].start){
            isInAnnotation = true;
          }
          if(isInAnnotation){
            wordObj.addClass('word-highlight');
            wordObj.data('annotationindex', currentAnnotation);
          }
          if(isInAnnotation && wordProperties.et === annotations[currentAnnotation].end){
            isInAnnotation = false;
            currentAnnotation++;
          }

        }

        lineObj.appendTo($subtitleList);

      }

      // init selection
      selector = subtitleselect.subtitleSelect({
          $subtitleList: $subtitleList,
          onSelect: function(selection){
            $annotationStart.data('start', selection.start).text(parseTime(selection.start));
            $annotationEnd.data('end', selection.end).text(parseTime(selection.end));
          }
        });
      /*var clipShare = new subtitleselect.subtitleSelect();
      clipShare.init({
          "container": $subtitleList,
          "lineHeight": parseInt($subtitleList.find('.word:eq(0)').css('lineHeight')),
          // "currentLine": currentLine,
          // "share": {"title": shareTitle},
          // addShow: addShow
          "previewContainer": $('#previewContainer')
        });
      clipShare.setSubtitleLine($subtitleList.children(), null);*/
    }

    function initAnnotationControl(){
      // add annotation button
      $subtitleList.on('click', '.addAnnotation', function(){
        $addAnnotationForm.get(0).reset();
        $annotationContent.code('');
      });
      // remove annotation
      $annotationList.on('click', '.removeAnnotation', function(){
        var $annotation = $(this).parents('li'),
          annotationId = parseInt($annotation.data('id')),
          start = $annotation.data('st'),
          end = $annotation.data('et');
        $.ajax({
          url: '/admin/course/annotationDestroy',
          type: 'POST',
          dataType: 'json',
          data: {
            annotation_id: annotationId
          }
        }).done(function(res){
          if(res.msgCode === 0){
            $annotation.remove();
            var isInTarget;
            $subtitleList.find('.word-highlight').each(function(){
              var $char = $(this);
              if($char.data('st') === start){
                isInTarget = true;
              }
              if(isInTarget){
                $char.removeClass('word-highlight');
                if($char.data('et') === end){
                  return false;
                }
              }
            });
            showSuccess('注释删除成功');
          }else{
            showError('注释删除失败');
          }
        });
        return false;
      });
      // modify annotation button
      var $modifyAnnotationModal = $('#modifyAnnotationModal'),
        $modifyAnnotationForm = $('#modifyAnnotationForm'),
        $annotationStartModify = $('#annotationStartModify'),
        $annotationEndModify = $('#annotationEndModify'),
        $annotationContentModify = $('#annotationContentModify'),
        $modifiedAnnotation;
      $annotationList.on('click', '.modifyAnnotation', function(){
        $modifiedAnnotation = $(this).parents('li');
        var annotationData = $modifiedAnnotation.data();
        $modifyAnnotationForm.data('id', annotationData.id).get(0).reset();
        $annotationStartModify.data('start', annotationData.st).text(parseTime(annotationData.st));
        $annotationEndModify.data('end', annotationData.et).text(parseTime(annotationData.et));
        $annotationContentModify.code(decodeURIComponent(annotationData.contentEncoded));
      });

      // add annotation
      var $addAnnotationForm = $('#addAnnotationForm'),
        $annotationContent = $('#annotationContent'),
        $addAnnotationButton = $('#addAnnotationButton');
      $addAnnotationButton.click(function(){
        var content = $.trim($annotationContent.code());
        if(!content){
          showError('请填写注释内容');
          return false;
        }
        var annotationData = {
            video_guid: videoInfo.guid,
            st: parseFloat($annotationStart.data('start')),
            et: parseFloat($annotationEnd.data('end')),
            content: content
          };
        $.ajax({
          url: '/admin/course/annotationCreate',
          type: 'POST',
          data: annotationData
        }).done(function(res){
          if(res.msgCode === 0){

            $addAnnotationModal.modal('hide');

            $annotationList.append(tplAnnotationItem.
              replace(/\{id\}/g, res.data.id).
              replace(/\{content\}/g, getText(content)).
              replace(/\{start\}/g, annotationData.st).
              replace(/\{end\}/g, annotationData.et)
            );

            selector.turnToMark();

            showSuccess('添加注释成功');
          }else{
            showError('添加注释失败，请稍后再试');
          }
        });
        return false;
      });

      // modify annotation
      $('#modifyAnnotationButton').click(function(){
        var content = $.trim($annotationContentModify.code());
        if(!content){
          showError('请填写注释内容');
          return false;
        }
        var annotationData = {
            annotation_id: $modifyAnnotationForm.data('id'),
            video_guid: videoInfo.guid,
            st: parseFloat($annotationStartModify.data('start')),
            et: parseFloat($annotationEndModify.data('end')),
            content: content
          };
        $.ajax({
          url: '/admin/course/annotationModify',
          type: 'POST',
          data: annotationData
        }).done(function(res){
          if(res.msgCode === 0){

            $modifyAnnotationModal.modal('hide');

            $modifiedAnnotation.data({
              contentEncoded: encodeURIComponent(content)
            }).find('.annotationPreview').text(getText(content));

            showSuccess('编辑注释成功');
          }else{
            showError('编辑注释失败，请稍后再试');
          }
        });
        return false;
      });

    }

    function parseTime(timeValue){
      var timeParts = [];
      timeParts[0] = toTwoDigits(Math.floor(timeValue / 3600));
      timeParts[1] = toTwoDigits(Math.floor(timeValue % 3600 / 60));
      timeParts[2] = toTwoDigits(Math.floor(timeValue % 60));
      return timeParts.join(':');
    }
    function toTwoDigits(value){
      var str = '0' + (value || 0);
      return str.slice(str.length - 2);
    }

    function getText(fragment){
      return $('<div>').append(fragment).text();
    }

    function showSuccess(success){
      alert(success);
    }

    function showError(error){
      alert(error);
    }

    function indexToAlphabet(index){
      return String.fromCharCode(65 + index);
    }

  });

});
