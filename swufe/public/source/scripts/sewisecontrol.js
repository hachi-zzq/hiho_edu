define(function(){
  /*
 * 视频、字幕
 * 基于sewise.player
 */

    /*
     * 带字幕的播放器
     */
    function player(){
        this.config = {
            //当前播放到的词的索引
            "currentWordIndex": 0,
            //当前播放到的行的索引
            "currentLineIndex": 0
        };
    };

    var _proto = player.prototype;

    _proto.init = function(cfg){
        var _self = this;

        _self.config.cfg = cfg;
        _self.$playZone = $('#playZone');
        _self.videoElem = cfg.videoElem;
        _self.subtitleElem = cfg.subtitleElem;
        _self.subtitleScrollCont = cfg.subtitleElem.find('.subtitle-content');
        _self.subtitleContainer = cfg.subtitleElem.find('.subtitle');
        _self.$question = _self.$playZone.find('#question');
        _self.$questionStem = _self.$playZone.find('#stem');
        _self.$choice = _self.$playZone.find('#choices');
        _self.$questionRight = _self.$playZone.find('#questionRight');
        _self.$questionWrongWarn = _self.$playZone.find('#questionWrongWarn');
        _self.$questionWarning = _self.$questionWrongWarn.find('#questionWarning');
        _self.$questionWrongSkip = _self.$playZone.find('#questionWrongSkip');
        _self.$questionSkipThumb = _self.$questionWrongSkip.find('#questionSkipThumb');
        _self.$questionSkipButton = _self.$questionWrongSkip.find('#questionSkipButton');
        _self.$questionWrongContinue = _self.$playZone.find('#questionWrongContinue');
        _self.$functionAside = $('#functionAside');
        _self.srtJson = cfg.srtJson;
        //播放器初始化完毕
        _self.playReady = false;
        _self.isPause = true;

        _self.currentTime = 0;

        //可以点词播放
        _self.config.wordClickEvent = true;
        //碎片分享模式下不能自动滚动字幕
        _self.config.isClipMode = false;

        _self.loadSubtitle(cfg);
        _self.initEvent();

        // study mode toolbar
        _self.initStudyBar(cfg);

        // annotation layer
        _self.initAnnotation(cfg);

        // question
        _self.initQuestion(cfg);

        //当sewise播放器准备好时回调
        window.playerReady = function(){
            //console.log("Sewise Player On Ready");
            _self.playReady = true;

            window.onStart = function(){
                //console.log("onStart");
                _self.isPause = false;
            };

            window.onPause = function(){
                //console.log("onPause");
                _self.isPause = true;
            };

            window.onStop = function(){
                //console.log("onStop");
            };

            //进度条改变时
            window.onSeek = function(time){
                if (_self.isPause){
                    _self.progress(time);
                }
            };

            //播放时
            window.onPlayTime = function(time){
                if (!_self.isPause){
                    _self.progress(time);
                }
            };

            _self.setSubtitle(_self.subtitleJson, cfg);

            if (cfg.callbackFunc){
                cfg.callbackFunc();
            }
        }
    };

    // study mode toolbar
    _proto.initStudyBar = function(cfg){
      var me = this,
        $studyModeSwitch = $('#studyModeSwitch');

      $studyModeSwitch.change(function(){
        if($studyModeSwitch.is(':checked')){
          me.$functionAside.addClass('learn-mode');
        }else{
          me.$functionAside.removeClass('learn-mode');
          me.$annotation.stop().hide();
          clearTimeout(me.annotationTimeout);
          me.$question.hide();
          me.$questionRight.hide();
          me.$questionWrongWarn.hide();
          me.$questionWrongSkip.hide();
          me.$questionSkipButton.hide();
        }
      });

      /*me.$appendix = $('#appendix').scroll(function(evt){
        evt.stopPropagation();
      });
      me.$highlights = $('#hilites').scroll(function(evt){
        evt.stopPropagation();
      });

      $('#buttonAppendix').click(function(){
        if(!$(this).hasClass('disable')){
          if(me.$appendix.is(':visible')){
            me.$appendix.stop().fadeOut('fast');
          }else{
            me.$highlights.stop().hide();
            me.$appendix.stop().fadeIn('fast');
          }
        }
      });
      $('#buttonHilite').click(function(){
        if(!$(this).hasClass('disable')){
          if(me.$highlights.is(':visible')){
            me.$highlights.stop().fadeOut('fast');
          }else{
            me.$appendix.stop().hide();
            me.$highlights.stop().fadeIn('fast');
          }
        }
      });

      me.$appendix.find('.close').click(function(){
        me.$appendix.stop().fadeOut('fast');
      });
      me.$highlights.find('.close').click(function(){
        me.$highlights.stop().fadeOut('fast');
      });*/
    };

    // study mode toolbar
    _proto.initAnnotation = function(cfg){
      var me = this;

      me.$annotation = $('#annotationPane');
      me.$annotationContent = $('#annotationContent');

      me.subtitleContainer.on('mouseenter', '.annotation', function(){
        if(me.$functionAside.hasClass('learn-mode')){
          var targetIndex = parseInt($(this).data('annotationindex'));
          if(targetIndex !== me.annotationOnStage){
            var currentAnnotation = me.annotations[targetIndex];
            me.$annotation.stop().hide();
            me.$annotationContent.html(currentAnnotation.content);
            me.$annotation.fadeIn('fast');
            me.annotationOnStage = targetIndex;
            clearTimeout(me.annotationTimeout);
          }else{
            clearTimeout(me.annotationTimeout);
            me.$annotation.fadeIn('fast');
          }
        }
      });

      me.subtitleContainer.on('mouseout', '.annotation', hideAnnotation);

      me.$annotation.on({
        mouseenter: function(){
          clearTimeout(me.annotationTimeout);
        },
        mouseout: function(evt){
          if(!me.$annotation.is(evt.relatedTarget) && !$.contains(this, evt.relatedTarget)){
            hideAnnotation();
          }
        }
      }).find('.close').click(function(){
        clearTimeout(me.annotationTimeout);
        me.$annotation.stop().hide();
        return false;
      });

      function hideAnnotation(f){
        me.annotationTimeout = setTimeout(function (){
          me.$annotation.stop().hide();
        }, 3000);
      }
    }

    // question
    _proto.initQuestion = function(cfg){
      var me = this;
      me.$question.on('click', '.choiceItem', function(){
        var $this = $(this),
          question = me.questions[me.nextQuestion];
        me.$question.hide();
        if($this.data('value') === question.answer[0]){
          question.passed = true;
          me.nextQuestion++;
          me.$questionRight.show();
          setTimeout(function(){
            me.$questionRight.fadeOut('fast', function(){
              SewisePlayer.doPlay();
            });
          }, 2000);
        }else{
          switch(question.operation){
            case 'goto':
              me.$questionSkipThumb.attr('src', question.operationDetail[1]);
              me.$questionSkipButton.off('click').click(function(){
                me.$questionWrongSkip.fadeOut('fast', function(){
                  SewisePlayer.doSeek(question.operationDetail[0]);
                  SewisePlayer.doPlay();
                });
                return false;
              });
              me.$questionWrongSkip.show();
              break;
            case 'tips':
              me.$questionWarning.text(question.operationDetail);
              me.$questionWrongWarn.show();
              break;
            default:
              me.$questionWrongContinue.show();
              setTimeout(function(){
                me.$questionWrongContinue.fadeOut('fast', function(){
                  SewisePlayer.doPlay();
                });
              });
          }
        }
      });
      me.$questionWrongWarn.find('#doItAgain').click(function(){
        me.$questionWrongWarn.hide();
        me.$question.show();
      });
    };

    //加载字幕
    _proto.loadSubtitle = function(cfg){
        var _self = this;

        $.getJSON(_self.srtJson, function(json){
            //console.log('json', json);
            if (json == '' || json == undefined){
                console.log('Subtitle load error.');
            } else {
                _self.subtitleLoaded = true;
                _self.subtitleJson = json;
                _self.setSubtitle(json, cfg);
            }
        }).fail(function(){
            _self.subtitleContainer.html('<li>Failed to load subtitles.</li>');
        });
    };

    //设置字幕DOM
    var tplHiliteItem = $.trim($('#tplHighlightItem').remove().html());
    _proto.setSubtitle = function(subtitle, cfg){
        var _self = this;

        if(this.playReady === true && this.subtitleLoaded === true){

          _self.subtitleContainer.empty();

          var srt = subtitle.subtitles;

          // subtitles
          var subtitleSchema = subtitle.subtitleSchema,
            tokenIndex = subtitleSchema.indexOf('token'),
            stIndex = subtitleSchema.indexOf('st'),
            etIndex = subtitleSchema.indexOf('et'),
            scoreIndex = subtitleSchema.indexOf('score');

          // annotations
          var annotationSchema = subtitle.annotationSchema;
          if(annotationSchema){
            var annotationSerie = subtitle.annotations,
              aStIndex = annotationSchema.indexOf('st'),
              aEtIndex = annotationSchema.indexOf('et'),
              aContentIndex = annotationSchema.indexOf('content'),
              annotations = [],
              currentAnnotation = 0,
              isInAnnotation;
            for(var aIndex = 0, aLength = annotationSerie.length; aIndex < aLength; aIndex++){
              annotations.push({
                st: annotationSerie[aIndex][aStIndex],
                et: annotationSerie[aIndex][aEtIndex],
                content: annotationSerie[aIndex][aContentIndex]
              });
            }
            this.annotations = annotations;
          }

          // highlights
          var highlightSchema = subtitle.highlightSchema;
          if(highlightSchema){
            var hStIndex = highlightSchema.indexOf('st'),
              hEtIndex = highlightSchema.indexOf('et'),
              hTitleIndex = highlightSchema.indexOf('title'),
              hBriefIndex = highlightSchema.indexOf('brief'),
              hThumbIndex = highlightSchema.indexOf('thumbnail'),
              highlightSerie = subtitle.highlights,
              highlights = [],
              $hiliteList = $('#hiliteList');
            if(highlightSerie.length){
              $('#buttonHilite').removeClass('disable');
              for(var hIndex = 0, hLength = highlightSerie.length; hIndex < hLength; hIndex++){
                var hCurrent = highlightSerie[hIndex];
                highlights.push(tplHiliteItem.
                  // replace(/\{link\}/g, '#').
                  replace(/\{number\}/g, hIndex + 1).
                  replace(/\{thumb\}/g, hCurrent[hThumbIndex]).
                  replace(/\{heading\}/g, hCurrent[hTitleIndex]).
                  replace(/\{start\}/g, hCurrent[hStIndex]).
                  replace(/\{end\}/g, hCurrent[hEtIndex])
                );
              }
              $hiliteList.html(highlights.join('')).on('click', '.hiliteLink', function(){
                if (_self.playReady){
                    SewisePlayer.doSeek($(this).data('start'));
                    // _self.$highlights.fadeOut('fast');
                }
              });
            }else{
              $('.tab a').eq(1).addClass('disable');
            }
          }

          // appendix
          var $appendixContent = $('#appendixContent');
          if(subtitle.appendix){
            $appendixContent.html(subtitle.appendix);
          }else{
            $('.tab a').eq(2).addClass('disable');
          }

          // questions
          var questionSchema = subtitle.questionSchema;
          if(questionSchema){
            var choiceSchema = subtitle.choiceSchema,
              questionSerie = subtitle.questions,
              questions = [];
            for(var index = 0, questionCount = questionSerie.length, questionItem, questionObject; index < questionCount; index++){
              questionItem = questionSerie[index];
              questionObject = {};
              for(var qKeyIndex = 0, qKeyLength = questionSchema.length, choiceSerie; qKeyIndex < qKeyLength; qKeyIndex++){
                if(questionSchema[qKeyIndex] === 'choices'){
                  choiceSerie = questionItem[qKeyIndex];
                  questionObject.choices = [];
                  for(var choiceIndex = 0, choiceCount = choiceSerie.length, choiceItem, choiceObject; choiceIndex < choiceCount; choiceIndex++){
                    choiceItem = choiceSerie[choiceIndex];
                    choiceObject = {};
                    for(var cKeyIndex = 0, cKeyLength = choiceSchema.length; cKeyIndex < cKeyLength; cKeyIndex++){
                      choiceObject[choiceSchema[cKeyIndex]] = choiceItem[cKeyIndex];
                    }
                    questionObject.choices.push(choiceObject);
                  }
                }else{
                  questionObject[questionSchema[qKeyIndex]] = questionItem[qKeyIndex];
                }
              }
              questions[index] = questionObject;
            }
            this.questions = questions;
            this.nextQuestion = 0;
          }

          var subtitleLength = subtitle.length;
          var wordIndex = 0;
          var lineTpl = '<li class="line"></li>';
          var wordTpl = '<span class="word"></span>';
          var subtitleArray = _self.subtitleArray = [];

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
                      lineObj.append('<pre class="space' + (isInAnnotation ? ' comment annotation" data-annotationIndex="' + currentAnnotation + '"' : '"') + '> </pre>');
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

                  // annotation
                  if(annotations && annotations[currentAnnotation] && wordProperties.st === annotations[currentAnnotation].st){
                    isInAnnotation = true;
                  }
                  if(isInAnnotation){
                    wordObj.addClass('comment annotation');
                    wordObj.data('annotationindex', currentAnnotation);
                  }

                  if(isInAnnotation && wordProperties.et === annotations[currentAnnotation].et){
                    isInAnnotation = false;
                    currentAnnotation++;
                  }
              }

              lineObj.appendTo(_self.subtitleContainer);
          }


          //所有行的jq对象
          _self.lineElemArray = _self.subtitleContainer.find('.line');

          //console.log('_self.lineElemArray', _self.lineElemArray);

          _self.subtitleIsLoaded = true;
          //是否自动滚动字幕
          _self.config.isAutoScroll = true;

          if (cfg.subtitleCallbackFunc){
              cfg.subtitleCallbackFunc(_self.lineElemArray);
          }
        }
    };

    //加载字幕
    _proto.initEvent = function(){
        var _self = this;

        _self.subtitleScrollCont.on('scroll', function(){
            _self.stopMoveScroll();
        });

        _self.subtitleScrollCont.on('click', '.word', function(e){
            e.stopPropagation();

            if (_self.playReady && _self.config.wordClickEvent){
                var word = $(this);

                SewisePlayer.doSeek(word.attr('st'));
            }
        });
    };

    //播放时
    _proto.progress = function(time){
        var _self = this,
          nextQuestion;
        _self.currentTime = time;

        //自动播放字幕
        if (_self.subtitleIsLoaded){
            var array = _self.subtitleArray;
            var wordLength = array.length;

            for (var i = 0; i < wordLength; i++){
                var wordObj = array[i];

                if (wordObj.data('st') <= time && wordObj.data('et') >= time){
                    if (_self.config.currentWordIndex != i){
                        //当前行
                        var currentLine = wordObj.parent('li');
                        var currentLineIndex = currentLine.data('index');

                        if (_self.config.currentLineIndex == 0){
                            _self.subtitleElem.find('#line_' + _self.config.currentLineIndex).addClass('current-line');
                        }
                        //设置当前行样式
                        if (_self.config.currentLineIndex != currentLineIndex){
                            _self.subtitleElem.find('#line_' + _self.config.currentLineIndex).removeClass('current-line');
                            _self.config.currentLineIndex = currentLineIndex;
                            currentLine.addClass('current-line');
                        }
                        //设置当前词样式
                        _self.subtitleElem.find('#word_' + _self.config.currentWordIndex).removeClass('current-word');
                        _self.config.currentWordIndex = i;
                        wordObj.addClass('current-word');
                        //滚动字幕框
                        _self.moveScroll(currentLine);
                    }
                }
            }

            // show question
            // if(nextQuestion && time)
            if(questions){
              nextQuestion = _self.questions[_self.nextQuestion];
              if(_self.$functionAside.hasClass('learn-mode') && nextQuestion && nextQuestion.time < time && !nextQuestion.passed){
                _self.showQuestion(nextQuestion);
              }
            }
        }
    };

    // show question
    var tplChoice = $.trim($('#tplChoice').remove().html());
    _proto.showQuestion = function(question){
      var choices = question.choices,
        choiceFragments = [];

      SewisePlayer.noramlScreen();
      this.pause();

      this.$questionStem.html(question.question);
      for(var index = 0, length = choices.length; index < length; index++){
        choiceFragments.push(tplChoice.
          replace(/\{value\}/g, choices[index].value).
          replace(/\{description\}/g, choices[index].description)
        )
      }
      this.$choice.html(choiceFragments.join(''));
      this.$question.show();
    };

    //滚动字幕框
    _proto.moveScroll = function(line){
        var _self = this;

        if (_self.config.isAutoScroll && !_self.config.isClipMode){
            _self.subtitleScrollCont.scrollTop(line.position().top - 48);
        }
    };

    //停止滚动字幕框
    _proto.stopMoveScroll = function(){
        var _self = this;

        _self.config.isAutoScroll = false;
        clearTimeout(_self.config.scrollTimer);
        _self.config.scrollTimer = window.setTimeout(function(){
            _self.config.isAutoScroll = true;
        }, 3000);
    };

    //暂停
    _proto.pause = function(){
        SewisePlayer.doPause();
    };

    //重新播放
    _proto.replay = function(){
        SewisePlayer.doSeek(0);
    };

    //获取当前播放时间
    _proto.getCurrentTime = function(){
        return this.currentTime;
    };

    //获取当前行
    _proto.getCurrentLineIndex = function(){
        return this.config.currentLineIndex;
    };

    //播放完后
    /*_proto.finish = function(){
        var _self = this;
        var api = _self.config.api;
        var cfg = _self.config.cfg;

        if (cfg.finishCallback){
            cfg.finishCallback();
        }
    };*/

  return {
    player: player,
    /* this is a shim for sewise player, as it doesn't support custom container */
    load: function(container, callback){
      if(typeof container == 'string'){
        container = document.getElementById(container);
      }
      if(container){
        var script = document.createElement('script');
        script.onload = callback;
        script.src = '/source/dist/scripts/lib/sewise.player.min.js';
        container.appendChild(script);
      }
    }
  };
});