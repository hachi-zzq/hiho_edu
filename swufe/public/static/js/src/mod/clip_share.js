/*
 * 碎片分享滑块控件
 * 与player.js 相关，基于flowplayer
 */

var JLib = JLib || {};

(function($, win){

    JLib.clipCtl = function(){
        this.config = {};
        this.player = null;
        this.mask = null;
    };
    //prototype
    var _proto = JLib.clipCtl.prototype;

    //初始化
    _proto.init = function(cfg){
        var _clip = this;
        _clip.config.cfg = cfg;

        //视频字幕容器
        _clip.container = cfg.container;
        //视频预览容器
        _clip.videoCont = null;

        //字幕分享控件最外元素
        _clip.ctlObj = _clip.container.find('#origin_subtitle_col');
        _clip.ctlObjBox = _clip.ctlObj.find('.subtitle-box');

        //装载字幕有滚动条的元素
        _clip.ctlScrollObj = _clip.ctlObj.find('.subtitle-cont');

        //装载字幕的UL
        _clip.ctlSubtitleCont = _clip.ctlObj.find('.subtitle');

        //字幕行高
        _clip.config.lineHeight = cfg.lineHeight;
        //分享前所播放的行
        _clip.config.currentLine = cfg.currentLine;

        //初始化碎片分享控件元素
        _clip.initShareDom();
        //字幕容器事件
        _clip.subtitleElemEvent();
        //开始杆
        _clip.startElemEvent();
        //结束杆
        _clip.endElemEvent();
        //控件鼠标放开事件
        _clip.mouseup();
        //开始滑块
        _clip.sliderStartEvent();
        //结束滑块
        _clip.sliderEndEvent();
    };

    //初始化碎片分享控件元素
    _proto.initShareDom = function(){
        var _clip = this;
        var config = _clip.config;

        _clip.ctlObj.addClass('clip-subtitle-mode');

        //开始杆元素
        _clip.startElem = $('<span class="clip-share-start hidden" id="select_start" title="Clip start"></span>').appendTo(_clip.ctlObjBox);
        //开始杆元素处在鼠标按下激活状态
        _clip.startElemActive = false;

        //结束杆元素
        _clip.endElem = $('<span class="clip-share-end hidden" id="select_end" title="Clip end"></span>').appendTo(_clip.ctlObjBox);
        //结束杆元素处在鼠标按下激活状态
        _clip.endElemActive = false;

        //杆元素宽高
        _clip.config.ctlElemWidth = _clip.startElem.width();
        _clip.config.ctlElemHeight = _clip.startElem.height();

        //分享、预览、关闭
        var shareActionHtml = [
            '<div class="share-action-cont" id="share_action_cont">',
                '<a href="#" class="btn-clip-preview preview" title="Preview">Preview</a>',
                //bbc demo 用到share-link
                //'<div class="share-link"><a href="javascript:void(0);" target="_blank" title="View Clip">View Clip</a></div>',
                '<div class="social">',
                    '<span class="txt">Share to</span>',
                    '<div class="jiathis_style_32x32">',
                        '<a class="jiathis_button_fb" title="Facebook"></a>',
                        '<a class="jiathis_button_twitter" title="Twitter"></a>',
                        '<a class="jiathis_button_googleplus" title="Google+"></a>',
                        '<a class="jiathis_button_tsina" title="Sina weibo"></a>',
                        '<a class="jiathis_button_weixin" title="Weixin"></a>',
                        '<a class="jiathis_button_pinterest" title="Pinterest"></a>',
                        '<script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"><\/script>',
                    '</div>',
                '</div>',
                '<a class="ico ico-close-s2 close" href="#"></a>',
            '</div>'
        ].join('');

        _clip.shareActionElem = $(shareActionHtml).appendTo(_clip.ctlObj);
        //_clip.shareActionLink = _clip.shareActionElem.find('.share-link a');

        //视频预览
        _clip.shareActionElem.on('click', '.preview', function(e){
            e.preventDefault();

            _clip.clipPreview(config.clipStartTime, config.clipEndTime);
        });

        _clip.shareActionElem.on('mouseenter', '.jiathis_style_32x32 .jiathis_txt', function(e){
            jiathis_config.url = config.shareUrl;
            JLib.tools.setShareSummary(e, config.cfg.share.title);
        });

        //关闭分享
        _clip.shareActionElem.on('click', '.close', function(e){
            e.preventDefault();

            _clip.destroy();
        });

        //范围滑块选择工具条
        var sliderBarHtml = [
            '<div class="clip-share-bar" id="clip_share_bar">',
                '<div class="bar">',
                    '<div class="slider" id="slider_start" style="left: -17px;">',
                        '<span class="el" title="slider start"></span>',
                        '<span class="time"><span></span></span>',
                    '</div>',
                    '<div class="slider" id="slider_end" style="left: 0;">',
                        '<span class="el" title="slider end"></span>',
                        '<span class="time"><span></span></span>',
                    '</div>',
                    '<span class="range" id="slider_range"></span>',
                '</div>',
            '</div>'
        ].join('');

        //滑块对象
        var sliderBar = _clip.sliderBar = {};
        //滑块最外层容器
        sliderBar.elem = $(sliderBarHtml).appendTo(_clip.ctlObj);
        sliderBar.bar = sliderBar.elem.find('.bar');
        sliderBar.barWidth = sliderBar.bar.width();
        //滑块开始元素，包括控制元素，时间元素
        sliderBar.start = sliderBar.elem.find('#slider_start');
        //滑块开始控制元素，有拖动事件
        sliderBar.startCtl = sliderBar.start.find('.el');
        //滑块开始控制元素是否激活
        sliderBar.startActive = false;
        //滑块开始时间元素
        sliderBar.stElem = sliderBar.start.find('.time');
        sliderBar.stElem.click(function(){
            $(this).addClass('hidden');
        });
        sliderBar.stElem.time = sliderBar.stElem.find('span');

        //滑块结束元素，包括控制元素，时间元素
        sliderBar.end = sliderBar.elem.find('#slider_end');
        //滑块结束控制元素，有拖动事件
        sliderBar.endCtl = sliderBar.end.find('.el');
        //滑块结束控制元素是否激活
        sliderBar.endActive = false;
        //滑块结束时间元素
        sliderBar.etElem = sliderBar.end.find('.time');
        sliderBar.etElem.click(function(){
            $(this).addClass('hidden');
        });
        sliderBar.etElem.time = sliderBar.etElem.find('span');

        //滑块元素宽度
        sliderBar.ctlWidth = sliderBar.start.width();
        //滑块所选范围条元素
        sliderBar.rangeElem = sliderBar.elem.find('#slider_range');

        //点击空白处关闭分享状态
        var ctlScrollObjEvent = function(e){
            if (!_clip.config.mouseIsMove && !_clip.isDestroy){
                _clip.destroy();
                _clip.ctlScrollObj.off('click', ctlScrollObjEvent);
            }
        };
        _clip.ctlScrollObj.on('click', ctlScrollObjEvent);
    };

    //关闭分享
    _proto.destroy = function(){
        var _clip = this;
        var config = _clip.config;

        _clip.ctlObj.removeClass('clip-subtitle-mode');

        _clip.shareActionElem.addClass('destroy');
        //删除clip相关元素
        setTimeout(function(){
            _clip.shareActionElem.remove();
        }, 200);
        _clip.startElem.remove();
        _clip.endElem.remove();
        if (_clip.videoCont){
            _clip.videoCont.remove();
        }

        _clip.sliderBar.elem.remove();
        //_clip.sliderBar = null;

        //清除选择高亮
        _clip.subtitleLine.eq(config.currentLine).addClass('current-line');
        _clip.ctlSubtitleCont.find('.selected-line').removeClass('selected-line');
        _clip.ctlSubtitleCont.find('.selected-word').removeClass('selected-word');
        _clip.ctlSubtitleCont.find('.selected-space').removeClass('selected-space');

        _clip.ctlObjBox.off('mousedown').off('mousemove');

        //恢复点词播放
        _clip.player.config.wordClickEvent = true;
        //恢复字幕自动滚动
        _clip.autoScroll();

        _clip.isDestroy = true;
    };

    //设置元素值拖动范围
    _proto.setLimit = function(cfg){
        var _clip = this;

        if (!_clip.ctlObjBoxWidth){
            _clip.ctlObjBoxWidth = _clip.ctlObjBox.width();
            _clip.ctlObjBoxHeight = _clip.ctlObjBox.height();
        }
    };

    //字幕容器的事件
    _proto.subtitleElemEvent = function(){
        var _clip = this;
        var config = _clip.config;
        var ctlObjBox = _clip.ctlObjBox;
        var body = $('body');
        var downLeft = 0;
        var downTop = 0;
        var upLeft = 0;
        var upTop = 0;

        ctlObjBox.mousedown(function(e){
            _clip.setLimit();
            config.mouseIsMove = false;

            //如果不是通过开始、结束杆冒泡上来的
            if (!_clip.startElemActive && !_clip.endElemActive){
                _clip.subtitleElemActive = true;
                downLeft = e.pageX - ctlObjBox.offset().left;
                downTop = e.pageY - ctlObjBox.offset().top;

                body.mouseup(bodyMouseup);
            }
        });

        ctlObjBox.mousemove(function(e){
            config.mouseIsMove = true;
            if (!_clip.startElemActive && !_clip.endElemActive){
                if (_clip.subtitleElemActive){
                    var moveLeft = e.pageX - ctlObjBox.offset().left;
                    var moveTop = e.pageY - ctlObjBox.offset().top;
                    var minNum = 10;

                    if (Math.abs(moveLeft - downLeft) > minNum || Math.abs(moveTop - downTop) > minNum){
                        _clip.subtitleElemMoved = true;
                    }
                }
            }
        });

        function bodyMouseup(e){
            _clip.subtitleElemActive = false;

            if (_clip.subtitleElemMoved){
                _clip.subtitleElemMoved = false;

                upLeft = e.pageX - ctlObjBox.offset().left;
                upTop = e.pageY - ctlObjBox.offset().top;

                var startTop = 0;
                var startLeft = 0;
                var endTop = 0;
                var endLeft = 0;
                var topTemp = 0;
                var leftTemp = 0;

                var minL = 16;
                var maxL = _clip.ctlObjBoxWidth + 16;
                var minT = 10;
                var maxT = _clip.ctlObjBoxHeight + 10;

                //修正极限
                downLeft = downLeft < minL ? minL : downLeft;
                downLeft = downLeft > maxL ? maxL : downLeft;
                //downTop = downTop < minT ? minT : Math.floor(downTop / config.lineHeight) * config.lineHeight + 10;
                //downTop = downTop > maxT ? maxT : Math.floor(downTop / config.lineHeight) * config.lineHeight + 10;
                downTop = downTop < minT ? minT : downTop;
                downTop = downTop > maxT ? maxT : downTop;

                upLeft = upLeft < minL ? minL : upLeft;
                upLeft = upLeft > maxL ? maxL : upLeft;
                //upTop = upTop < minT ? minT : Math.floor(upTop / config.lineHeight) * config.lineHeight + 10;
                //upTop = upTop > maxT ? maxT : Math.floor(upTop / config.lineHeight) * config.lineHeight + 10;
                upTop = upTop < minT ? minT : upTop;
                upTop = upTop > maxT ? maxT : upTop;

                //如果鼠标按下时的坐标高于放开时，交换双方坐标
                if ((downTop > upTop) || (downTop == upTop && downLeft > upLeft)){
                    topTemp = downTop;
                    downTop = upTop;
                    upTop = topTemp;

                    leftTemp = downLeft;
                    downLeft = upLeft;
                    upLeft = leftTemp;
                }

                startTop = downTop;
                startLeft = downLeft - config.ctlElemWidth;
                startLeft = startLeft < minL ? minL : startLeft;
                endTop = upTop - config.ctlElemHeight;
                endLeft = upLeft;

                if (endTop - startTop < 0){
                    endTop = startTop;
                }

                if (endLeft - startLeft < config.ctlElemWidth){
                    endLeft = startLeft + config.ctlElemWidth;
                }

                //清除用鼠标选择的文本高亮色
                _clip.clearWinSelection();
                _clip.startElem.css({
                    "top": startTop,
                    "left": startLeft
                });

                _clip.endElem.css({
                    "top": endTop,
                    "left": endLeft
                });

                //设置被选的字幕
                _clip.setSelected({
                    "top": startTop,
                    "left": startLeft + config.ctlElemWidth,
                    "right": endLeft,
                    "bottom": endTop + config.ctlElemHeight
                });

                _clip.setSliderByClip({
                    "target": "start",
                    "startTop": startTop,
                    "endTop": endTop
                });

                _clip.setClipInfo();
                _clip.updateElemPos();
                _clip.setSliderTime();
                _clip.getFragmentUrl(config.clipStartTime, config.clipEndTime);
            }

            body.off('mouseup', bodyMouseup);
        }

    };

    //开始杆元素事件
    _proto.startElemEvent = function(){
        var _clip = this;
        var config = _clip.config;

        var start = _clip.startElem;
        //鼠标按下时开始元素的定位top
        var startTop = 0;
        var startLeft = 0;
        var endTop = 0;
        var endLeft = 0;
        var scrollTop = 0;

        start.click(function(e){
            e.stopPropagation();
        });

        start.mousedown(function(e){
            var startData = _clip.mousedown({
                "target": "start",
                "X": e.pageX,
                "Y": e.pageY
            });

            startTop = startData.top;
            startLeft = startData.left;
            endTop = startData.otherTop;
            endLeft = startData.otherLeft;
            scrollTop = startData.scrollTop;
        });

        $('body').mousemove(function(e){
            if (_clip.startElemActive){
                _clip.mousemove({
                    "target": "start",
                    "elemTop": startTop,
                    "elemLeft": startLeft,
                    "otherTop": endTop,
                    "otherLeft": endLeft,
                    "X": e.pageX,
                    "Y": e.pageY,
                    "scrollTop": scrollTop
                });
            }
        });
    };

    //结束杆元素事件
    _proto.endElemEvent = function(){
        var _clip = this;
        var config = _clip.config;

        var end = _clip.endElem;
        //鼠标按下时开始元素的定位top
        var startTop = 0;
        var startLeft = 0;
        var endTop = 0;
        var endLeft = 0;
        var scrollTop = 0;

        end.click(function(e){
            e.stopPropagation();
        });

        end.mousedown(function(e){
            var startData = _clip.mousedown({
                "target": "end",
                "X": e.pageX,
                "Y": e.pageY
            });

            startTop = startData.otherTop;
            startLeft = startData.otherLeft;
            endTop = startData.top;
            endLeft = startData.left;
            scrollTop = startData.scrollTop;
        });

        $('body').mousemove(function(e){
            if (_clip.endElemActive){
                _clip.mousemove({
                    "target": "end",
                    "elemTop": endTop,
                    "elemLeft": endLeft,
                    "otherTop": startTop,
                    "otherLeft": startLeft,
                    "X": e.pageX,
                    "Y": e.pageY,
                    "scrollTop": scrollTop
                });
            }
        });
    };

    //杆 鼠标按下事件
    _proto.mousedown = function(cfg){
        var _clip = this;

        _clip.setLimit();

        //清除用鼠标选择的文本高亮色
        _clip.clearWinSelection();

        //被拖动的元素
        var targetElem = null;
        //另外一个元素
        var otherElem = null;

        $('body').addClass('disable-select-text');
        _clip.ctlScrollObj.addClass('disable-select-text');

        if (cfg.target == 'start'){
            _clip.targetElem = targetElem = _clip.startElem;
            otherElem = _clip.endElem;
            _clip.startElemActive = true;

        }
        if (cfg.target == 'end'){
            _clip.targetElem = targetElem = _clip.endElem;
            otherElem = _clip.startElem;
            _clip.endElemActive = true;
        }

        //元素的原始X坐标
        _clip.config.originalX = cfg.X;
        //元素的原始Y坐标
        _clip.config.originalY = cfg.Y;

        return {
            "top": targetElem.position().top,
            "left": targetElem.position().left,
            "otherTop": otherElem.position().top,
            "otherLeft": otherElem.position().left,
            "scrollTop": _clip.ctlScrollObj.scrollTop()
        };
    };

    //杆 鼠标移动事件
    _proto.mousemove = function(cfg){
        var _clip = this;
        var config = _clip.config;

        //被拖动的元素
        var targetElem = null;
        //另外一个元素
        var otherElem = null;
        //拖动范围
        var minL = 16 - config.ctlElemWidth;
        var minT = 0;
        var maxL = 0;
        var maxT = 0;

        var subTop = 0;
        var subLeft = 0;
        var subRight = 0;
        var subBottom = 0;

        _clip.addMask();

        //针对webkit核心，拖动时仍然会导致滚动条滚动，要算上滚动前后的高度差
        var top = cfg.elemTop + (cfg.Y - config.originalY) + (_clip.ctlScrollObj.scrollTop() - cfg.scrollTop);
        var left = cfg.elemLeft + (cfg.X - config.originalX);

        if (cfg.target == 'start'){
            targetElem = _clip.startElem;
            otherElem = _clip.endElem;

            //minL = 4;
            minT = 10;
            maxL = _clip.ctlObjBoxWidth - config.ctlElemWidth + 16;
            maxT = cfg.otherTop;

            top = Math.floor(top / config.lineHeight) * config.lineHeight + 10;
            if (left > (cfg.otherLeft - config.ctlElemWidth) && top > (maxT - config.ctlElemHeight)){
                top = maxT - config.ctlElemHeight;
            }
            if (top < minT){
                top = minT;
            }
            if (top > maxT){
                top = maxT
            }

            if (top >= cfg.otherTop && left > (cfg.otherLeft - config.ctlElemWidth)){
                left = cfg.otherLeft - config.ctlElemWidth;
            }
            if (left < minL){
                left = minL;
            }
            if (left > maxL){
                left = maxL;
            }

            //设置被选的字幕相关参数
            subTop = top;
            subLeft = left + config.ctlElemWidth;
            subRight = cfg.otherLeft;
            subBottom = cfg.otherTop + config.ctlElemHeight;

            _clip.setSliderByClip({
                "target": "start",
                "startTop": top,
                "endTop": cfg.otherTop
            });
        }

        if (cfg.target == 'end'){
            targetElem = _clip.endElem;
            otherElem = _clip.startElem;

            minT = cfg.otherTop;
            maxT = _clip.ctlObjBoxHeight - config.ctlElemHeight + 10;
            maxL = _clip.ctlObjBoxWidth + 16;

            top = Math.floor(top / config.lineHeight) * config.lineHeight + 10;
            if (left < (cfg.otherLeft + config.ctlElemWidth) && top < (minT + config.ctlElemHeight)){
                top = minT + config.ctlElemHeight;
            }
            if (top > maxT){
                top = maxT;
            }
            if ( top < minT){
                top = minT;
            }

            if (top <= cfg.otherTop && left < (cfg.otherLeft + config.ctlElemWidth)){
                left = cfg.otherLeft + config.ctlElemWidth;
            }
            if (left > maxL){
                left = maxL;
            }
            if (left < minL){
                left = minL;
            }

            //设置被选的字幕相关参数
            subTop = cfg.otherTop;
            subLeft = cfg.otherLeft + config.ctlElemWidth;
            subRight = left;
            subBottom = top + config.ctlElemHeight;

            _clip.setSliderByClip({
                "target": "end",
                "startTop": cfg.otherTop,
                "endTop": top
            });
        }

        targetElem.css({"left": left, "top": top});

        //设置被选的字幕
        _clip.setSelected({
            "top": subTop,
            "left": subLeft,
            "right": subRight,
            "bottom": subBottom
        });

        _clip.setClipInfo();
        _clip.setSliderTime();
    };

    //鼠标放开事件
    _proto.mouseup = function(){
        var _clip = this;
        var config = _clip.config;
        var $body = $('body');

        $body.mouseup(function(){
            if (_clip.startElemActive || _clip.endElemActive || _clip.sliderBar.startActive || _clip.sliderBar.endActive){
                //设置开始杆非激活
                if (_clip.startElemActive){
                    _clip.startElemActive = false;
                }
                //设置结束杆非激活
                if (_clip.endElemActive){
                    _clip.endElemActive = false;
                }
                //设置开始滑块非激活
                if (_clip.sliderBar.startActive){
                    _clip.sliderBar.startActive = false;
                }
                //设置结束滑块非激活
                if (_clip.sliderBar.endActive){
                    _clip.sliderBar.endActive = false;
                }

                //取消 各种屏蔽拖动操作时的选择文字手段
                $body.removeClass('disable-select-text');
                _clip.ctlScrollObj.removeClass('disable-select-text');
                _clip.delMask();
                //更新选择杆的定位位置，贴近所选的文字边缘
                _clip.updateElemPos();

                _clip.getFragmentUrl(config.clipStartTime, config.clipEndTime);
            }
        });
    };

    //根据选择杆的定位设置拖动滑块
    _proto.setSliderByClip = function(cfg){
        var _clip = this;
        var config = _clip.config;
        var sliderBar = _clip.sliderBar;
        var start = sliderBar.start;
        var end = sliderBar.end;

        var boxHeight = _clip.ctlObjBoxHeight - config.lineHeight;
        //滑块定位top极限值
        var startMinL = -sliderBar.ctlWidth;
        var startLeft = sliderBar.barWidth * (cfg.startTop - 10) / boxHeight - sliderBar.ctlWidth;
        startLeft = startLeft < startMinL ? startMinL : startLeft;

        start.css({'left': startLeft});

        var endMaxL = sliderBar.barWidth;
        var endLeft = sliderBar.barWidth * (cfg.endTop - 10) / boxHeight;
        endLeft = endLeft > endMaxL ? endMaxL : endLeft;
        end.css({'left': endLeft});

        _clip.setSliderRange(startLeft + sliderBar.ctlWidth, endLeft - startLeft - sliderBar.ctlWidth);

    };

    //开始滑块元素事件
    _proto.sliderStartEvent = function(){
        var _clip = this;
        var config = _clip.config;

        var startCtl = _clip.sliderBar.startCtl;
        //鼠标按下时开始元素的定位top
        var startLeft = 0;
        var endLeft = 0;

        startCtl.click(function(){
            _clip.ctlScrollObj.scrollTop(_clip.startElem.position().top - config.lineHeight * 2);
        });

        startCtl.mousedown(function(e){
            var startData = _clip.sliderMousedown({
                "target": "start",
                "X": e.pageX
            });

            startLeft = startData.left;
            endLeft = startData.otherLeft;
        });

        $('body').mousemove(function(e){
            if (_clip.sliderBar.startActive){
                _clip.sliderMousemove({
                    "target": "start",
                    "elemLeft": startLeft,
                    "otherLeft": endLeft,
                    "X": e.pageX
                });
            }
        });
    };

    //结束滑块元素事件
    _proto.sliderEndEvent = function(){
        var _clip = this;
        var config = _clip.config;

        var endCtl = _clip.sliderBar.endCtl;
        var startLeft = 0;
        var endLeft = 0;

        endCtl.click(function(){
            _clip.ctlScrollObj.scrollTop(_clip.endElem.position().top - config.lineHeight * 8);
        });

        endCtl.mousedown(function(e){
            var startData = _clip.sliderMousedown({
                "target": "end",
                "X": e.pageX
            });

            startLeft = startData.otherLeft;
            endLeft = startData.left;
        });

        $('body').mousemove(function(e){
            if (_clip.sliderBar.endActive){
                _clip.sliderMousemove({
                    "target": "end",
                    "elemLeft": endLeft,
                    "otherLeft": startLeft,
                    "X": e.pageX
                });
            }
        });
    };

    //滑块元素鼠标按下事件
    _proto.sliderMousedown = function(cfg){
        var _clip = this;
        var config = _clip.config;
        var sliderBar = _clip.sliderBar;

        _clip.setLimit();

        //清除用鼠标选择的文本高亮色
        _clip.clearWinSelection();

        //被拖动的元素
        var targetElem = null;
        //另外一个元素
        var otherElem = null;

        $('body').addClass('disable-select-text');
        _clip.ctlScrollObj.addClass('disable-select-text');

        if (cfg.target == 'start'){
            targetElem = sliderBar.start;
            otherElem = sliderBar.end;
            sliderBar.startActive = true;

        }
        if (cfg.target == 'end'){
            targetElem = sliderBar.end;
            otherElem = sliderBar.start;
            sliderBar.endActive = true;
        }

        //元素的原始X坐标
        _clip.config.originalX = cfg.X;

        return {
            "left": targetElem.position().left,
            "otherLeft": otherElem.position().left
        };
    };

    //滑块元素鼠标拖动事件
    _proto.sliderMousemove = function(cfg){
        var _clip = this;
        var config = _clip.config;
        var sliderBar = _clip.sliderBar;

        //被拖动的元素
        var targetElem = null;

        var startMinL = -sliderBar.ctlWidth;
        var endMaxL = sliderBar.barWidth;

        var left = cfg.elemLeft + (cfg.X - config.originalX);

        if (cfg.target == 'start'){
            targetElem = sliderBar.start;

            var startMaxL = cfg.otherLeft - sliderBar.ctlWidth;

            if (left < startMinL){
                left = startMinL;
            }
            if (left > startMaxL){
                left = startMaxL;
            }
            //设置所选范围条
            _clip.setSliderRange(left + sliderBar.ctlWidth, cfg.otherLeft - left);
            _clip.setClipBySlider({
                "target": "start",
                "startLeft": left,
                "endLeft": cfg.otherLeft
            });

        } else if (cfg.target == 'end'){
            targetElem = sliderBar.end;

            var endMinL = cfg.otherLeft + sliderBar.ctlWidth;
            if (left < endMinL){
                left = endMinL;
            }
            if (left > endMaxL){
                left = endMaxL;
            }
            //设置所选范围条
            _clip.setSliderRange(cfg.otherLeft + sliderBar.ctlWidth, left - cfg.otherLeft);
            _clip.setClipBySlider({
                "target": "end",
                "startLeft": cfg.otherLeft,
                "endLeft": left
            });
        }

        targetElem.css({'left': left});

        _clip.setClipInfo();
        _clip.setSliderTime();
    };

    //设置滑块所选范围条
    _proto.setSliderRange = function(left, width){
        var _clip = this;

        _clip.sliderBar.rangeElem.css({'left':left, 'width': width});
    };

    //根据拖动滑块的定位设置选择杆
    _proto.setClipBySlider = function(cfg){
        var _clip = this;
        var config = _clip.config;
        var sliderBar = _clip.sliderBar;

        var boxHeight = _clip.ctlObjBoxHeight - config.lineHeight;

        //选择杆定位top最大值
        var maxTop = _clip.ctlObjBoxHeight - config.ctlElemHeight + 10;
        //开始杆top
        var clipStartTop = boxHeight * ((cfg.startLeft + sliderBar.ctlWidth) / sliderBar.barWidth) + 10;
        //clipStartTop = clipStartTop > maxTop ? maxTop : clipStartTop;
        clipStartTop = Math.floor(clipStartTop / config.lineHeight) * config.lineHeight + 10;
        //开始杆left
        var clipStartLeft = 16 - config.ctlElemWidth;
        //结束杆top
        var clipEndTop = boxHeight * ((cfg.endLeft) / sliderBar.barWidth) + 10;
        //clipEndTop = clipEndTop > maxTop ? maxTop : clipEndTop;
        clipEndTop = Math.floor(clipEndTop / config.lineHeight) * config.lineHeight + 10;
        //结束杆left
        var clipEndLeft = _clip.ctlObjBoxWidth + 16;

        _clip.startElem.css({"top": clipStartTop, "left": clipStartLeft});
        _clip.endElem.css({"top": clipEndTop, "left": clipEndLeft});

        _clip.setSelected({
            "top": clipStartTop,
            "left": clipStartLeft + config.ctlElemWidth,
            "right": clipEndLeft,
            "bottom": clipEndTop + config.ctlElemHeight
        });

        if (cfg.target == 'start'){
            _clip.ctlScrollObj.scrollTop(clipStartTop - config.lineHeight * 2);
        } else if (cfg.target == 'end'){
            _clip.ctlScrollObj.scrollTop(clipEndTop - config.lineHeight * 8);
        }
    };

    //设置拖动滑块的两个时间
    _proto.setSliderTime = function(){
        var _clip = this;
        var config = _clip.config;
        var sliderBar = _clip.sliderBar;

        if (config.clipStartTime != undefined){
            sliderBar.stElem.time.text(_clip.getTipsTime(config.clipStartTime));
            sliderBar.etElem.time.text(_clip.getTipsTime(config.clipEndTime));

            sliderBar.stElem.removeClass('hidden');
            sliderBar.etElem.removeClass('hidden');
        } else {
            sliderBar.stElem.addClass('hidden');
            sliderBar.etElem.addClass('hidden');
        }

    };

    //拖动滑块时在整个网页上添加一个层防止文字内容等因为鼠标的拖动被选中
    _proto.addMask = function(p){
        var _clip = this;

        if (!_clip.mask){
            _clip.mask = $('<div class="clip-mask" onselectstart="return false;"></div>').appendTo($('body'));
        }
    };

    _proto.delMask = function(p){
        var _clip = this;

        if (_clip.mask){
            _clip.mask.remove();
            _clip.mask = null;
        }
    };

    //清除用鼠标选择的文本高亮色
    _proto.clearWinSelection = function(){
        if (win.getSelection){
            win.getSelection().removeAllRanges();
        } else {
            document.selection.empty();
        }
    };

    //将时间转成3'40"的形式
    _proto.getTipsTime = function(t){
        var _clip = this;

        t = Math.floor(t);
        var m = Math.floor(t / 60);
        var s = t - m * 60;
        return m + '\' ' + s + '"';
    };

    _proto.setSubtitleLine = function(data, player){
        var _clip = this;
        var config = _clip.config;

        if (!data){
            alert('Subtitle loading.');
            _clip.destroy();
        } else {
            _clip.player = player;
            //设置不能点词播放
            _clip.player.config.wordClickEvent = false;
            //停止字幕自动滚动
            _clip.stopAutoScroll();

            _clip.subtitleLine = data;
            var len = _clip.subtitleLine.length;
            for (var i = 0; i < len; i++){
                var line = _clip.subtitleLine.eq(i);
                line.data('top', line.position().top).attr('top', line.position().top);
            }

            if (config.currentLine == undefined){
                _clip.setCtlPos(0);
            } else {
                _clip.setCtlPos(config.currentLine);
            }
        }

    };

    //设置元素初始定位值
    _proto.setCtlPos = function(index){
        var _clip = this;
        var config = _clip.config;

        _clip.setLimit();

        var line = _clip.subtitleLine.eq(index);
        var nextLine = null;
        var len = _clip.subtitleLine.length;
        var lineTemp = len - 1 - index;

        if (lineTemp >= 4){
            nextLine = _clip.subtitleLine.eq(index + 4);
        } else {
            nextLine = _clip.subtitleLine.eq(index + lineTemp);
        }
        var startTop = parseFloat(line.attr('top'));
        var endTop = parseFloat(nextLine.attr('top'));
        var startLeft = 16 - config.ctlElemWidth;
        var endLeft = line.width() + 16;

        _clip.startElem.css({"top": startTop, "left": startLeft}).removeClass('hidden');
        _clip.endElem.css({"top": endTop, "left": endLeft}).removeClass('hidden');

        //设置被选的字幕
        _clip.setSelected({
            "top": startTop,
            "left": startLeft + config.ctlElemWidth,
            "right": endLeft,
            "bottom": endTop + config.ctlElemHeight
        });

        _clip.setClipInfo();
        _clip.updateElemPos();

        _clip.setSliderByClip({
            "target": "start",
            "startTop": startTop,
            "endTop": endTop
        });
        _clip.setSliderTime();

        _clip.getFragmentUrl(config.clipStartTime, config.clipEndTime);
        //显示开始杆
        _clip.ctlScrollObj.scrollTop(_clip.startElem.position().top - config.lineHeight * 2);
    };

    //设置字幕不滚动
    _proto.stopAutoScroll = function(){
        var _clip = this;
        var player = _clip.player;

        if (player){
            player.config.isClipMode = true;
        }
    };

    //设置字幕滚动
    _proto.autoScroll = function(){
        var _clip = this;
        var player = _clip.player;

        if (player){
            player.config.isClipMode = false;
        }
    };

    //设置被选择的字幕。cfg Object {top, left, right, bottom}
    _proto.setSelected = function(cfg){
        var _clip = this;
        var config = _clip.config;
        var startLine = null;
        var startWord = null;
        var startWordInLineIndex = -1;
        var endLine = null;
        var endWord = null;

        var len = _clip.subtitleLine.length;
        for (var i = 0; i < len; i++){
            var line = _clip.subtitleLine.eq(i);
            var lineTop = parseFloat(line.attr('top'));
            startLine = line;

            line.removeClass('selected-line');
            line.find('.word').removeClass('selected-word');
            line.find('.space').removeClass('selected-space');

            if (cfg.top >= lineTop && cfg.top < (lineTop + line.height())){
                line.addClass('selected-line');

                var lineWord = line.find('.word');
                var lineWordLen = lineWord.length;

                for (var j = 0; j < lineWordLen; j++){
                    var word = lineWord.eq(j);
                    var wordTop = word.position().top;
                    var wordLeft = word.position().left;


                    if (cfg.top >= wordTop && cfg.top < (wordTop + word.height()) && cfg.left >= wordLeft && cfg.left < (wordLeft + word.width())){
                        startWord = word;
                        startWordInLineIndex = j;
                    }

                    if (startWord != null){
                        word.addClass('selected-word');
                        word.next('.space').addClass('selected-space');
                    }
                }

                break;
            }
        }

        for (i = parseFloat(startLine.attr('index')); i < len; i++){
            line = _clip.subtitleLine.eq(i);
            lineTop = parseFloat(line.attr('top'));

            //找到结束所在的行
            if (cfg.bottom > lineTop && cfg.bottom <= (lineTop + line.height())){
                endLine = line;
                lineWord = line.find('.word');
                lineWordLen = lineWord.length;

                //如果结束杆和开始杆在同一行
                if (parseFloat(endLine.attr('index')) == parseFloat(startLine.attr('index'))){
                    if (startWord != null){
                        for (j = startWordInLineIndex; j < lineWordLen; j++){
                            word = lineWord.eq(j);
                            wordTop = word.position().top;
                            wordLeft = word.position().left;

                            if (cfg.bottom > wordTop && cfg.bottom <= (wordTop + word.height()) && cfg.right >= wordLeft && cfg.right < (wordLeft + word.width())){
                                endWord = word;
                            }

                            if (endWord != null){
                                word.removeClass('selected-word');
                                word.next('.space').removeClass('selected-space');
                            }
                        }
                    }
                }
                //如果开始和结束不在同一行
                else {
                    for (j = 0; j < lineWordLen; j++){
                        word = lineWord.eq(j);
                        wordTop = word.position().top;
                        wordLeft = word.position().left;
                        word.addClass('selected-word');
                        word.next('.space').addClass('selected-space');

                        if (cfg.bottom > wordTop && cfg.bottom <= (wordTop + word.height()) && cfg.right >= wordLeft && cfg.right < (wordLeft + word.width())){
                            endWord = word;
                        }

                        if (endWord != null){
                            word.removeClass('selected-word');
                            word.next('.space').removeClass('selected-space');
                        }
                    }
                }
            } else if (i != parseFloat(startLine.attr('index'))){
                if (endLine == null){
                    line.addClass('selected-line');
                    line.find('.word').addClass('selected-word');
                    line.find('.space').addClass('selected-space');
                } else {
                    line.removeClass('selected-line');
                    line.find('.word').removeClass('selected-word');
                    line.find('.space').removeClass('selected-space');
                }
            }
        }
    };

    //修正开始、结束杆的定位，以贴近所选择的字
    _proto.updateElemPos = function(){
        var _clip = this;
        var config = _clip.config;

        var len = _clip.selectedWords.length;

        if (len > 0){
            var startSelected = _clip.selectedWords.eq(0);
            var startLeft = startSelected.position().left - config.ctlElemWidth;
            var startTop = startSelected.position().top;

            var endSelected = _clip.selectedWords.eq(len -1);
            var endLeft = endSelected.position().left + endSelected.width();
            var endTop = endSelected.position().top;

            _clip.startElem.css({'left': startLeft, 'top': startTop});
            _clip.endElem.css({'left': endLeft, 'top': endTop});

            _clip.setSliderByClip({
                "target": "start",
                "startTop": startTop,
                "endTop": endTop
            });
        }
    };

    //设置所选择部分的各种信息
    _proto.setClipInfo = function(){
        var _clip = this;
        var config = _clip.config;

        _clip.selectedWords = _clip.ctlScrollObj.find('.selected-word');
        var len = _clip.selectedWords.length;
        config.clipStartTime = _clip.selectedWords.eq(0).attr('st');
        config.clipEndTime = _clip.selectedWords.eq(len - 1).attr('et');
    };
    //碎片分享地址
    _proto.getFragmentUrl = function(st, et){
        var _clip = this;
        var config = _clip.config;

        var url = '/fragment/create/' + JLib.config.playerGUID + '?st=' + st + '&et=' + et;

        $.get(url, {},
            function(data){
                data = JLib.Util.stringToJSON(data);
                //成功
                if (data.status == 1) {
                    config.shareUrl = JLib.config.hostname + '/fragment/' + data.fragment_guid;
                    //_clip.shareActionLink.attr('href', config.shareUrl);
                }
                //失败
                else {
                    alert(data.message);
                }
        }).fail(function(){
            //alert('Sorry! System busy. Please retry later.');
        });
    };

    //视频预览
    _proto.clipPreview = function(st, et){
        var _clip = this;
        var config = _clip.config;

        //暂停原视频
        if (_clip.player){
            _clip.player.pause();
        }

        var url = '/video_review?guid=' + JLib.config.playerGUID + '&st=' + st + '&et=' + et;

        if (_clip.videoCont == null){
            _clip.videoCont = $('<div class="video-preview-cont" id="video_preview_cont"><div class="video-box"></div><span class="label">Preview</span><div class="loading-s2"></div></div>').appendTo(_clip.container);
        }
        var loading = _clip.videoCont.find('.loading-s2');
        loading.removeClass('hidden');
        var previewTime = _clip.videoCont.find('.tips em').text('');

        $.get(url, {},
            function(data){
                data = JLib.Util.stringToJSON(data);
                var videoJson = null;
                if (!!data.SD){
                    videoJson = data.SD
                }
                if (!!data.HD){
                    videoJson = data.HD
                }
                if (!!data.FULLHD){
                    videoJson = data.FULLHD
                }
                console.log(videoJson);
                var tpl = [
                    '<div id="share_player" class="flowplayer video-cont no-toggle" data-ratio="0.75">',
                        '<video autoplay>',
                            '<source id="video_src" type="video/flv" src="">',
                            '<source id="video_m3u8" type="application/x-mpegurl" src=""></source>',
                            '<track id="video_srt" src=""></track>',
                        '</video>',
                    '</div>'
                ].join('');

                var videoCont = _clip.videoCont.find('.video-box');
                loading.addClass('hidden');
                videoCont.empty();
                var video = $(tpl);
                video.find('#video_src').attr('src', videoJson.FLV.src);
                video.find('#video_m3u8').attr('src', videoJson.M3U8.src);
                video.find('#video_srt').attr('src', data.subtitle);
                video.appendTo(videoCont);
                video.flowplayer({
                    "swf": "/static/js/lib/flowplayer/5.4.6/flowplayer.swf"
                });
                var videoApi = video.flowplayer();
                videoApi.bind('ready', function(e, api, time){
                    //设置预览标签视频时间
                    previewTime.text(_clip.getTipsTime(videoApi.video.duration));
                });

        }).fail(function(){
            //alert('Sorry! System busy. Please retry later.');
        });


    };


})(jQuery, window);


