/*
 * 视频、字幕
 * 基于sewise.player
 */

var JLib = JLib || {};

(function($, win){
    /*
     * 带字幕的播放器
     */
    (function(){
        JLib.player = function(){
            this.config = {
                //当前播放到的词的索引
                "currentWordIndex": 0,
                //当前播放到的行的索引
                "currentLineIndex": 0
            };
        };

        var _proto = JLib.player.prototype;

        _proto.init = function(cfg){
            var _self = this;

            _self.config.cfg = cfg;
            _self.videoElem = cfg.videoElem;
            _self.subtitleElem = cfg.subtitleElem;
            _self.subtitleScrollCont = cfg.subtitleElem.find('.subtitle-cont');
            _self.subtitleContainer = cfg.subtitleElem.find('.subtitle');
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

            //当sewise播放器准备好时回调
            win.playerReady = function(){
                //console.log("Sewise Player On Ready");
                _self.playReady = true;

                win.onStart = function(){
                    //console.log("onStart");
                    _self.isPause = false;
                };

                win.onPause = function(){
                    //console.log("onPause");
                    _self.isPause = true;
                };

                win.onStop = function(){
                    //console.log("onStop");
                };

                //进度条改变时
                win.onSeek = function(time){
                    if (_self.isPause){
                        _self.progress(time);
                    }
                };

                //播放时
                win.onPlayTime = function(time){
                    if (!_self.isPause){
                        _self.progress(time);
                    }
                };


                if (cfg.callbackFunc){
                    cfg.callbackFunc();
                }
            }
        };

        //加载字幕
        _proto.loadSubtitle = function(cfg){
            var _self = this;

            $.getJSON(_self.srtJson, function(json){
                //console.log('json', json);
                if (json == '' || json == undefined){
                    console.log('Subtitle load error.');
                } else {
                    json = JLib.Util.stringToJSON(json);
                    _self.setSubtitle(json.srt, cfg);
                }
            }).fail(function(){
                _self.subtitleContainer.html('<li>Failed to load subtitles.</li>');
            });
        };

        //设置字幕DOM
        _proto.setSubtitle = function(srt, cfg){
            var _self = this;

            _self.subtitleContainer.empty();

            var srtLength = srt.length;
            var wordIndex = 0;
            var lineTpl = '<li class="line"></li>';
            var wordTpl = '<span class="word"></span>';
            var subtitleArray = _self.subtitleArray = [];

            for (var i = 0; i < srtLength; i++){
                var lineLength = srt[i].length;
                var lineObj = $(lineTpl);
                lineObj.attr({"id": 'line_' + i}).data({"index": i});
                var pre = null;

                for (var j = 0; j < lineLength; j++){
                    var wordOjb = $(wordTpl);
                    var word = srt[i][j];
                    var wordTxt = word.token;
                    var addSpace = false;

                    if (j == 0){
                        lineObj.attr({
                            "index": i,
                            "st": srt[i][0].st,
                            "et": srt[i][lineLength - 1].et
                        });
                        lineObj.data({
                            "index": i,
                            "st": srt[i][0].st,
                            "et": srt[i][lineLength - 1].et
                        });
                    }

                    //添加词之间的空白
                    if (pre != null && /^(\w|\d)/.test(wordTxt) && (!(/^(ve|s|ll)$/i).test(wordTxt) || !(/^(\'|’)$/).test(pre))){
                        lineObj.append('<pre class="space"> </pre>');
                        addSpace = true;
                    }
                    pre = wordTxt;

                    if (addSpace){
                        wordTxt = ' ' + wordTxt;
                    }

                    wordOjb.attr({
                        "id": 'word_' + wordIndex,
                        "index": wordIndex,
                        "st": word.st,
                        "et": word.et
                    });
                    wordOjb.data({
                        "index": wordIndex,
                        "st": word.st,
                        "et": word.et
                    });
                    wordOjb.text(wordTxt);

                    wordOjb.appendTo(lineObj);
                    subtitleArray.push(wordOjb);
                    wordIndex++;
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
            var _self = this;
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
            }
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
            _self.config.scrollTimer = win.setTimeout(function(){
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
    })();

})(jQuery, window);




