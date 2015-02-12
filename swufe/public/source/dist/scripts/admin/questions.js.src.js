define(['subtitleselect'], function(subtitleselect){
  jQuery(function($){
    

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
      $highlightList = $('#highlightList'),
      $highlightStart = $('#highlightStart'),
      $highlightEnd = $('#highlightEnd'),
      $addQuestionModal = $('#addQuestionModal'),
      $addHighlightModal = $('#addHighlightModal'),
      $questionHighlights = $('#questionHighlights'),
      $questionHighlightsModify = $('#questionHighlightsModify'),
      videoInfo = $subtitleList.data(),
      tplHighlightItem = $.trim($('#tplHighlightItem').remove().html()),
      tplHighlightOption = $.trim($('#tplHighlightOption').remove().html()),
      tplQuestion = $.trim($('#tplQuestion').remove().html()),
      tplQuestionButton = $.trim($('#tplQuestionButton').remove().html()),
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

    initHighlightControl();
    initQuestionControl();

    function setSubtitle(subtitle){
      // highlights
      var highlightSchema = subtitle.highlightSchema,
        hIdIndex = highlightSchema.indexOf('id'),
        hStIndex = highlightSchema.indexOf('st'),
        hEtIndex = highlightSchema.indexOf('et'),
        hTitleIndex = highlightSchema.indexOf('title'),
        hBriefIndex = highlightSchema.indexOf('brief'),
        hThumbIndex = highlightSchema.indexOf('thumbnail'),
        highlightSerie = subtitle.highlights,
        highlights = [],
        highlightFragments = [],
        highlightOptions = [],
        currentHighlight = 0,
        isInHighlight;
      if(highlightSerie.length){
        for(var hIndex = 0, hLength = highlightSerie.length; hIndex < hLength; hIndex++){
          var hCurrent = highlightSerie[hIndex];
          highlights.push({
            thumbnail: hCurrent[hThumbIndex],
            heading: hCurrent[hTitleIndex],
            start: hCurrent[hStIndex],
            end: hCurrent[hEtIndex]
          });
          highlightFragments.push(tplHighlightItem.
            replace(/\{id\}/g, hCurrent[hIdIndex]).
            replace(/\{heading\}/g, hCurrent[hTitleIndex]).
            replace(/\{start\}/g, hCurrent[hStIndex]).
            replace(/\{end\}/g, hCurrent[hEtIndex]).
            replace(/\{heading\|encoded\}/g, encodeURIComponent(hCurrent[hTitleIndex])).
            replace(/\{description\|encoded\}/g, encodeURIComponent(hCurrent[hBriefIndex]))
          );
          highlightOptions.push(tplHighlightOption.
            replace(/\{value\}/g, hCurrent[hIdIndex]).
            replace(/\{description\}/g, hCurrent[hTitleIndex])
          );
        }
        $highlightList.html(highlightFragments.join(''));
        $questionHighlights.html(highlightOptions.join(''));
        $questionHighlightsModify.html(highlightOptions.join(''));
      }

      // questions
      var questionSchema = subtitle.questionSchema,
        qIdIndex = questionSchema.indexOf('id'),
        qTimeIndex = questionSchema.indexOf('time'),
        qStemIndex = questionSchema.indexOf('question'),
        qAnswerIndex = questionSchema.indexOf('answer'),
        qChoicesIndex = questionSchema.indexOf('choices'),
        qOpIndex = questionSchema.indexOf('operation'),
        qOpDetailIndex = questionSchema.indexOf('operationDetail'),
        qHighlightIdIndex = questionSchema.indexOf('gotoHighlightId'),
        questionSerie = subtitle.questions,
        questions = [],
        questionFragments = [],
        currentQuestion = 0;
      if(questionSerie.length){
        for(var qIndex = 0, qLength = questionSerie.length; qIndex < qLength; qIndex++){
          var qCurrent = questionSerie[qIndex];
          questions.push({
            id: qCurrent[qIdIndex],
            time: qCurrent[qTimeIndex],
            question: qCurrent[qStemIndex],
            answer: qCurrent[qAnswerIndex],
            choices: qCurrent[qChoicesIndex],
            operation: qCurrent[qOpIndex],
            operationDetail: qCurrent[qOpDetailIndex],
            highlightId: qCurrent[qHighlightIdIndex]
          });
        }
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
            lineObj.append('<pre class="space' + (isInHighlight ? ' word-highlight" data-highlightIndex="' + currentHighlight + '"' : '"') + '> </pre>');
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

          // mark highlights
          if(highlights[currentHighlight] && wordProperties.st === highlights[currentHighlight].start){
            isInHighlight = true;
          }
          if(isInHighlight){
            wordObj.addClass('word-highlight');
            wordObj.data('highlightindex', currentHighlight);
          }
          if(isInHighlight && wordProperties.et === highlights[currentHighlight].end){
            isInHighlight = false;
            currentHighlight++;
          }

        }

        lineObj.append(tplQuestionButton.replace(/\{time\}/, lineProperties.et)).appendTo($subtitleList);

        // questions
        var currentQuestionObj = questions[currentQuestion];
        if(currentQuestionObj && (lineProperties.st >= currentQuestionObj.time)){
          lineObj.before(tplQuestion.
              replace(/\{id\}/g, currentQuestionObj.id).
              replace(/\{stem\|encoded\}/g, encodeURIComponent(currentQuestionObj.question)).
              replace(/\{stem\}/g, currentQuestionObj.question).
              replace(/\{time\}/g, currentQuestionObj.time).
              replace(/\{choices\}/g, encodeURIComponent($.stringify(currentQuestionObj.choices))).
              replace(/\{answers\}/g, encodeURIComponent($.stringify(currentQuestionObj.answer))).
              replace(/\{action\}/g, currentQuestionObj.operation).
              replace(/\{actionDetail\}/g, currentQuestionObj.operationDetail).
              replace(/\{highlightId\}/g, currentQuestionObj.highlightId || '')
            );
          currentQuestion++;
        }
      }

      // init selection
      selector = subtitleselect.subtitleSelect({
          $subtitleList: $subtitleList,
          onSelect: function(selection){
            $highlightStart.data('start', selection.start).text(parseTime(selection.start));
            $highlightEnd.data('end', selection.end).text(parseTime(selection.end));
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

    function initHighlightControl(){
      // add button
      var $addHighlightForm = $('#addHighlightForm');
      $subtitleList.on('click', '.addHighlight', function(){
        $addHighlightForm.get(0).reset();
      });
      // remove highlight
      $highlightList.on('click', '.removeHighlight', function(){
        var $highlight = $(this).parents('li'),
          highlightId = parseInt($highlight.data('id')),
          start = $highlight.data('st'),
          end = $highlight.data('et');
        if(confirm('确定删除这个重点片段？')){
          $.ajax({
            url: '/admin/course/highlightsDestroy',
            type: 'POST',
            dataType: 'json',
            data: {
              id: highlightId
            }
          }).done(function(res){
            if(res.msgCode === 0){
              $highlight.remove();
              $questionHighlights.children().each(function(){
                var $option = $(this);
                if(parseInt($option.val()) === highlightId){
                  $option.remove();
                  return false;
                }
              });
              $questionHighlightsModify.children().each(function(){
                var $option = $(this);
                if(parseInt($option.val()) === highlightId){
                  $option.remove();
                  return false;
                }
              });
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
              showSuccess('片段删除成功');
            }else{
              showError('片段删除失败');
            }
          });
        }
        return false;
      });
      // modify button
      var $highlightStartModify = $('#highlightStartModify'),
        $highlightEndModify = $('#highlightEndModify'),
        $modifyHighlightModal = $('#modifyHighlightModal'),
        $modifyHighlightForm = $('#modifyHighlightForm'),
        $highlightTitleModify = $('#highlightTitleModify'),
        $highlightDescriptionModify = $('#highlightDescriptionModify'),
        $modifiedHighlight;
      $highlightList.on('click', '.modifyInfo', function(){
        var $highlight = $(this).parents('li'),
          highlightId = parseInt($highlight.data('id')),
          start = $highlight.data('st'),
          end = $highlight.data('et'),
          title = decodeURIComponent($highlight.data('heading-encoded')),
          description = decodeURIComponent($highlight.data('description-encoded'));
        $modifyHighlightForm.data('id', highlightId).get(0).reset();
        $highlightStartModify.data('start', start).text(parseTime(start));
        $highlightEndModify.data('end', end).text(parseTime(end));
        $highlightTitleModify.val(title);
        $highlightDescriptionModify.val(description);
        $modifiedHighlight = $highlight;
      });

      // add highlight
      var $highlightTitle = $('#highlightTitle'),
        $highlightDescription = $('#highlightDescription');
      $('#addHighlightButton').click(function(){
        var title = $.trim($highlightTitle.val()),
          description = $.trim($highlightDescription.val());
        if(!title){
          showError('请填写片段标题');
          return false;
        }
        var highlightData = {
            video_guid: videoInfo.guid,
            st: parseFloat($highlightStart.data('start')),
            et: parseFloat($highlightEnd.data('end')),
            title: title,
            description: description
          };
        $.ajax({
          url: '/admin/course/highlightsCreate',
          type: 'POST',
          data: highlightData
        }).done(function(res){
          if(res.msgCode === 0){
            $addHighlightModal.modal('hide');

            $highlightList.append(tplHighlightItem.
              replace(/\{id\}/g, res.data.id).
              replace(/\{heading\}/g, title).
              replace(/\{start\}/g, highlightData.st).
              replace(/\{end\}/g, highlightData.et).
              replace(/\{heading\|encoded\}/g, encodeURIComponent(title)).
              replace(/\{description\|encoded\}/g, encodeURIComponent(description))
            );
            $questionHighlights.append(tplHighlightOption.
              replace(/\{value\}/g, res.data.id).
              replace(/\{description\}/g, title)
            );
            $questionHighlightsModify.append(tplHighlightOption.
              replace(/\{value\}/g, res.data.id).
              replace(/\{description\}/g, title)
            );

            selector.turnToMark();

            showSuccess('添加片段成功');
          }else{
            showError('添加片段失败，请稍后再试');
          }
        });
      });

      // modify highlight
      $('#modifyHighlightButton').click(function(){
        var title = $.trim($highlightTitleModify.val()),
          description = $.trim($highlightDescriptionModify.val());
        if(!title){
          showError('请填写片段标题');
          return false;
        }
        var highlightData = {
            video_guid: videoInfo.guid,
            highlight_id: $modifyHighlightForm.data('id'),
            st: parseFloat($highlightStartModify.data('start')),
            et: parseFloat($highlightEndModify.data('end')),
            title: title,
            description: description
          };
        $.ajax({
          url: '/admin/course/highlightsModify',
          type: 'POST',
          data: highlightData
        }).done(function(res){
          if(res.msgCode === 0){
            $modifyHighlightModal.modal('hide');

            $modifiedHighlight.replaceWith(tplHighlightItem.
              replace(/\{id\}/g, res.data.id).
              replace(/\{heading\}/g, title).
              replace(/\{start\}/g, highlightData.st).
              replace(/\{end\}/g, highlightData.et).
              replace(/\{heading\|encoded\}/g, encodeURIComponent(title)).
              replace(/\{description\|encoded\}/g, encodeURIComponent(description))
            );

            showSuccess('编辑片段成功');
          }else{
            showError('编辑片段失败，请稍后再试');
          }
        });
      });
    }

    function initQuestionControl(){
      var $questionTime = $('#questionTime'),
        $questionAfter;
      // add question button
      $subtitleList.on('click', '.add-quiz', function(){
        var $this = $(this),
          time = $this.data('time');
        $addQuestionForm.get(0).reset();
        $questionTime.data('time', time).text(parseTime(time));
        $questionAfter = $this.parents('.line');
      });
      // delete question
      $subtitleList.on('click', '.delete-quiz', function(){
        var $this = $(this);
        $.ajax({
          url: '/admin/course/questionDestroy',
          method: 'POST',
          data: {
            question_id: $this.data('id')
          }
        }).done(function(res){
          if(res.msgCode === 0){
            $this.parents('.quiz').remove();
            showSuccess('删除问题成功');
          }else{
            showError('删除问题失败');
          }
        })
      });
      // modify question button
      var $modifyQuestionForm = $('#modifyQuestionForm'),
        $questionTimeModify = $('#questionTimeModify'),
        $questionDescriptionModify = $('#questionDescriptionModify'),
        $choiceModify = $('.choiceModify'),
        $choiceAModify = $('#choiceAModify'),
        $choiceBModify = $('#choiceBModify'),
        $choiceCModify = $('#choiceCModify'),
        $choiceDModify = $('#choiceDModify'),
        $questionAnswerModify = $('.questionAnswerModify'),
        $errorActionModify = $('.errorActionModify'),
        $questionWarningModify = $('#questionWarningModify'),
        $modifiedQuestion;
      $subtitleList.on('click', '.edit-quiz', function(){
        $modifiedQuestion = $(this).parents('.quiz');
        var questionData = $modifiedQuestion.data();
        if(!$.isArray(questionData.choices)){
          questionData.choices = $.parseJSON(decodeURIComponent(questionData.choices));
        }
        $modifyQuestionForm.data('id', questionData.id).get(0).reset();
        $questionTimeModify.data('time', questionData.time).text(parseTime(questionData.time));
        $questionDescriptionModify.val(decodeURIComponent(questionData.stemEncoded));
        $.each(questionData.choices, function(index, choicePair){
          $choiceModify.eq(alphabetToIndex(choicePair[0])).val(choicePair[1]);
        });
        $questionAnswerModify/*.prop('checked', false)*/.eq(alphabetToIndex($.parseJSON(decodeURIComponent(questionData.answers))[0])).prop('checked', true);
        $errorActionModify.eq(findInArray(errorActions, questionData.action)).find('a').click();
        switch(questionData.action){
          case 'goto':
            $questionHighlightsModify.val(questionData.highlightid);
            break;
          case 'tips':
            $questionWarningModify.val(questionData.actiondetail);
        }
      });
      // add question
      var $addQuestionForm = $('#addQuestionForm'),
        $questionDescription = $('#questionDescription'),
        $choiceA = $('#choiceA'),
        $choiceB = $('#choiceB'),
        $choiceC = $('#choiceC'),
        $choiceD = $('#choiceD'),
        $questionAnswer = $('.questionAnswer'),
        $errorAction = $('.errorAction'),
        $questionWarning = $('#questionWarning'),
        errorActions = ['goto', 'tips', 'continue'],
        $closeAddQuestionModal = $('#closeAddQuestionModal');
      $('#addQuestionButton').click(function(){
        var question = $questionDescription.val(),
          choiceA = $choiceA.val(),
          choiceB = $choiceB.val(),
          choiceC = $choiceC.val(),
          choiceD = $choiceD.val(),
          answerIndex = $questionAnswer.index($questionAnswer.filter(':checked')),
          errorAction = $errorAction.index($errorAction.filter('.active')),
          questionHighlight = $questionHighlights.val(),
          questionWarning = $questionWarning.val();
        if(!question){
          showError('请填写题干');
          return false;
        }
        if(!choiceA || !choiceB){
          showError('请至少填写两个选项');
          return false;
        }
        if(answerIndex < 0){
          showError('请选择一个正确答案');
          return false;
        }
        switch(errorAction){
          case 0:
            if(!questionHighlight){
              showError('请选择片段');
              return false;
            }
            break;
          case 1:
            if(!questionWarning){
              showError('请填写提示信息');
              return false;
            }
        }
        var answers = {};
        if(choiceA){
          answers[indexToAlphabet(0)] = choiceA;
        }
        if(choiceB){
          answers[indexToAlphabet(1)] = choiceB;
        }
        if(choiceC){
          answers[indexToAlphabet(2)] = choiceC;
        }
        if(choiceD){
          answers[indexToAlphabet(3)] = choiceD;
        }

        var questionData = {
          video_guid: videoInfo.guid,
          time_point: $questionTime.data('time'),
          title: question,
          type: 'radio',
          answers: $.stringify(answers),
          correct_answers: $.stringify([indexToAlphabet(answerIndex)]),
          error_operation: errorActions[errorAction],
          target_highlight_id: questionHighlight,
          tips_content: questionWarning
        };
        $.ajax({
          url: '/admin/course/questionCreate',
          type: 'POST',
          data: questionData
        }).done(function(res){
          if(res.msgCode === 0){
            $addQuestionModal.modal('hide');
            $addQuestionForm.get(0).reset();
            $questionAfter.after(tplQuestion.
                replace(/\{id\}/g, res.data.id).
                replace(/\{stem\|encoded\}/g, encodeURIComponent(question)).
                replace(/\{stem\}/g, question).
                replace(/\{time\}/g, questionData.time_point).
                replace(/\{choices\}/g, encodeURIComponent($.stringify(parseChoiceArray($.parseJSON(questionData.answers))))).
                replace(/\{answers\}/g, encodeURIComponent(questionData.correct_answers)).
                replace(/\{action\}/g, errorActions[errorAction]).
                replace(/\{actionDetail\}/g, encodeURIComponent(questionWarning)).
                replace(/\{highlightId\}/g, questionHighlight || '')
              );
          }else{
            showError('创建问题失败');
          }
        });
        return false;
      });
      // modify question
      var $modifyQuestionModal = $('#modifyQuestionModal');
      $('#modifyQuestionButton').click(function(){
        var question = $questionDescriptionModify.val(),
          choiceA = $choiceAModify.val(),
          choiceB = $choiceBModify.val(),
          choiceC = $choiceCModify.val(),
          choiceD = $choiceDModify.val(),
          answerIndex = $questionAnswerModify.index($questionAnswerModify.filter(':checked')),
          errorAction = $errorActionModify.index($errorActionModify.filter('.active')),
          questionHighlight = $questionHighlightsModify.val(),
          questionWarning = $questionWarningModify.val();
        if(!question){
          showError('请填写题干');
          return false;
        }
        if(!choiceA || !choiceB){
          showError('请至少填写两个选项');
          return false;
        }
        if(answerIndex < 0){
          showError('请选择一个正确答案');
          return false;
        }
        switch(errorAction){
          case 0:
            if(!questionHighlight){
              showError('请选择片段');
              return false;
            }
            break;
          case 1:
            if(!questionWarning){
              showError('请填写提示信息');
              return false;
            }
        }
        var answers = {};
        if(choiceA){
          answers[indexToAlphabet(0)] = choiceA;
        }
        if(choiceB){
          answers[indexToAlphabet(1)] = choiceB;
        }
        if(choiceC){
          answers[indexToAlphabet(2)] = choiceC;
        }
        if(choiceD){
          answers[indexToAlphabet(3)] = choiceD;
        }

        var questionData = {
          question_id: $modifyQuestionForm.data('id'),
          video_guid: videoInfo.guid,
          time_point: $questionTimeModify.data('time'),
          title: question,
          type: 'radio',
          answers: $.stringify(answers),
          correct_answers: $.stringify([indexToAlphabet(answerIndex)]),
          error_operation: errorActions[errorAction],
          target_highlight_id: questionHighlight,
          tips_content: questionWarning
        }
        $.ajax({
          url: '/admin/course/questionModify',
          type: 'POST',
          data: questionData
        }).done(function(res){
          if(res.msgCode === 0){
            $modifyQuestionModal.modal('hide');
            $modifyQuestionForm.get(0).reset();
            $modifiedQuestion.data({
              stemEncoded: encodeURIComponent(question),
              choices: encodeURIComponent($.stringify(parseChoiceArray($.parseJSON(questionData.answers)))),
              answers: encodeURIComponent(questionData.correct_answers),
              action: errorActions[errorAction],
              actiondetail: encodeURIComponent(questionWarning),
              highlightid: questionHighlight || ''
            }).find('.questionStem').text(question);
            showSuccess('编辑问题成功');
          }else{
            showError('编辑问题失败');
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

    function showSuccess(success){
      alert(success);
    }

    function showError(error){
      alert(error);
    }

    function indexToAlphabet(index){
      return String.fromCharCode(65 + index);
    }

    function alphabetToIndex(letter){
      return letter.toUpperCase().charCodeAt() - 65;
    }

    function findInArray(array, target){
      var result = -1;
      $.each(array, function(index, value){
        if(value === target){
          result = index;
          return false;
        }
      });
      return result;
    }

    function parseChoiceArray(choices){
      var choiceArray = [];
      $.each(choices, function(tag, description){
        choiceArray.push([tag, description]);
      });
      return choiceArray;
    }

  });

});
