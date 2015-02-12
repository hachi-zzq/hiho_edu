(function($, win){
    var jUtil = JLib.Util;
    var jForm = JLib.form;
    var jDialog = JLib.dialog;
    var jTools = JLib.tools;
    var pageName = JLib.config.pageName;

    $(function(){


        /*
         * 首页
         */
        (function(){
            if (pageName == 'index'){
                var filterForm = $('#filter_form');
                var filtersSlect = filterForm.find('select');

                filtersSlect.change(function(){
                    filterForm.submit();
                });
            }
        })();

        /*
         * 剪辑页
         */
        (function(){
            if (pageName == 'cut'){
                var originVideoSubtitle = $('#origin_video_subtitle');
                var subtitleCol = originVideoSubtitle.find('#origin_subtitle_col');
                var subtitleContainer = subtitleCol.find('.subtitle');

                //时间轴
                var timeLine = originVideoSubtitle.find('#time_line');
                //时间轴索引，每次递增10秒
                var timeLineIndex = 0;
                //时间轴索引副本，用来标识已经设置过的索引不用再设置到时间軕
                var timeLineIndexTemp = -1;

                //加载字幕
                $.getJSON(JLib.config.playerJSON, function(json){
                    //console.log('json', json);
                    if (json == '' || json == undefined){
                        console.log('字幕加载错误.');
                    } else {
                        json = JLib.Util.stringToJSON(json);
                        setSubtitle(json.srt);
                    }
                }).fail(function(){
                    subtitleContainer.html('<li>字幕加载失败，请刷新页面.</li>');
                });

                //设置字幕DOM
                function setSubtitle(srt){
                    subtitleContainer.empty();

                    var srtLength = srt.length;
                    var wordIndex = 0;
                    var lineTpl = '<li class="line"></li>';
                    var wordTpl = '<span class="word"></span>';

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
                            wordIndex++;
                        }

                        lineObj.appendTo(subtitleContainer);
                        var linePos = lineObj.position();
                        lineObj.data('top', linePos.top).attr('top', linePos.top);

                        compareTime(lineObj.data('st'));

                        if (timeLineIndex >= lineObj.data('st') && timeLineIndex != timeLineIndexTemp){
                            var itemTime = $('<span class="item-time"></span>').appendTo(timeLine);
                            itemTime.css('top', lineObj.data('top') - 5);
                            itemTime.text(jTools.convertSecond(timeLineIndex, true, false));

                            timeLineIndexTemp = timeLineIndex;
                        }

                    }

                    //所有行的jq对象
                    var lineElemArray = subtitleContainer.find('.line');

                    //碎片分享对象
                    var clipShare = new JLib.clipCtl();
                    clipShare.init({
                        "container": originVideoSubtitle,
                        "lineHeight": 24
                    });
                    clipShare.setSubtitleLine(lineElemArray);
                }

                function compareTime(time){
                    if (timeLineIndex < time){
                        timeLineIndex += 10;
                        compareTime(time);
                    }
                }

                //设置时间轴
            }
        })();



    });
})(jQuery, window);

