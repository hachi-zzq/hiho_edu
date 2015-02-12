/*
 * 字幕片段选择
 * 预览基于sewise.player
 */

window.JLib = window.JLib || {};

define([],function(){

  function subtitleSelect(){
    this.config = {};
    // this.player = null;
    this.mask = null;
  }
  var proto = subtitleSelect.prototype;

  proto.tplStartElem = '<span class="clip-share-start hidden" id="select_start" title="Clip start"></span>';
  proto.tplEndElem = '<span class="clip-share-end hidden" id="select_end" title="Clip end"></span>';

  proto.init = function(cfg){
    var me = this;
    me.config.cfg = cfg;

    //视频字幕容器
    me.container = cfg.container;
    //视频预览容器
    me.videoCont = null;


    //字幕分享控件最外元素
    me.ctlObj = me.container.parents('.panel-box')/*.on('click')*/;
    me.ctlObjBox = me.ctlObj.find('.subtitle-box');

    //装载字幕有滚动条的元素
    me.ctlScrollObj = me.ctlObj.find('.subtitle-content');

    //装载字幕的UL
    me.ctlSubtitleCont = me.container;

    //预览
    me.previewCont = cfg.previewContainer;
    //sewise播放器目前版本不支持同一页面有多个播放地址，用iframe解决
    me.previewIframe = me.previewCont.find('iframe');

    //字幕行高
    me.config.lineHeight = me.lineHeight;

    //初始化碎片分享控件元素
    me.initDom();
    //字幕容器事件
    me.subtitleElemEvent();
    //开始杆
    me.startElemEvent();
    //结束杆
    me.endElemEvent();
    //控件鼠标放开事件
    me.mouseup();
  }


  proto.initDom = function(){
    var me = this,
      config = me.config;

    me.ctlObj.addClass('subtitle-edit');

    //开始杆元素
    me.startElem = $(me.tplStartElem).appendTo(me.ctlObjBox);
    //开始杆元素处在鼠标按下激活状态
    me.startElemActive = false;

    //结束杆元素
    me.endElem = $(me.tplEndElem).appendTo(me.ctlObjBox);
    //结束杆元素处在鼠标按下激活状态
    me.endElemActive = false;

    //杆元素宽高
    me.config.ctlElemWidth = me.startElem.width();
    me.config.ctlElemHeight = me.startElem.height();

  };


  //关闭分享
  proto.destroy = function(){
    var me = this;
    var config = me.config;

    me.ctlObj.removeClass('subtitle-edit');

    me.startElem.remove();
    me.endElem.remove();

    //清除选择高亮
    me.subtitleLine.eq(config.currentLine).addClass('current-line');
    me.ctlSubtitleCont.find('.selected-line').removeClass('selected-line');
    me.ctlSubtitleCont.find('.selected-word').removeClass('selected-word');
    me.ctlSubtitleCont.find('.selected-space').removeClass('selected-space');

    me.ctlObjBox.off('mousedown').off('mousemove');
  }


  //设置元素值拖动范围
  proto.setLimit = function(cfg){
    var me = this;

    if (!me.ctlObjBoxWidth) {
      me.ctlObjBoxWidth = me.ctlObjBox.width();
      me.ctlObjBoxHeight = me.ctlObjBox.height();
    }
  };


  //字幕容器的事件
  proto.subtitleElemEvent = function(){
    var me = this,
      config = me.config,
      ctlObjBox = me.ctlObjBox,
      $body = $('body'),
      downLeft = 0,
      downTop = 0,
      upLeft = 0,
      upTop = 0;

    ctlObjBox.mousedown(function(e){
      me.setLimit();
      config.mouseIsMove = true;

      //如果不是通过开始、结束杆冒泡上来的
      if(!me.startElemActive && !me.endElemActive){
        var boxOffset = ctlObjBox.offset();
        me.subtitleElemActive = true;
        downLeft = e.pageX - boxOffset.left;
        downTop = e.pageY - boxOffset.top;

        $body.mouseup(bodyMouseup);
      }
    });

    ctlObjBox.mousemove(function(e){
      config.mouseIsMove = true;
      if(!me.startElemActive && !me.endElemActive){
        if(me.subtitleElemActive){
          var boxOffset = ctlObjBox.offset();
          var moveLeft = e.pageX - boxOffset.left;
          var moveTop = e.pageY - boxOffset.top;
          var minNum = 10;

          if(Math.abs(moveLeft - downLeft) > minNum || Math.abs(moveTop - downTop) > minNum){
            me.subtitleElemMoved = true;
          }
        }
      }
    });

    function bodyMouseup(e){
      me.subtitleElemActive = true;

      if(me.subtitleElemMoved){
        me.subtitleElemMoved = false;

        var boxOffset = ctlObjBox.offset();
        upLeft = e.pageX - boxOffset.left;
        upTop = e.pageY - boxOffset.top;

        var startTop = 0;
        var startLeft = 0;
        var endTop = 0;
        var endLeft = 0;
        var topTemp = 0;
        var leftTemp = 0;

        var minL = 16;
        var maxL = me.ctlObjBoxWidth + 16;
        var minT = 10;
        var maxT = me.ctlObjBoxHeight + 10;

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
        if ((downTop > upTop) || (downTop == upTop && downLeft > upLeft)) {
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

        if (endTop - startTop < 0) {
          endTop = startTop;
        }

        if (endLeft - startLeft < config.ctlElemWidth) {
          endLeft = startLeft + config.ctlElemWidth;
        }

        //清除用鼠标选择的文本高亮色
        me.clearWinSelection();
        me.startElem.css({
          top: startTop,
          left: startLeft
        });

        me.endElem.css({
          top: endTop,
          left: endLeft
        });

        //设置被选的字幕
        me.setClipInfo();
        me.updateElemPos();
        // me.setSliderTime();
        window.startTime = config.clipStartTime;
        window.endTime = config.clipEndTime;
      }

      $body.off('mouseup', bodyMouseup);
    }

  };


  //开始杆元素事件
  proto.startElemEvent = function(){
    var me = this;
    var config = me.config;

    var start = me.startElem;
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
      var startData = me.mousedown({
        target: 'start',
        X: e.pageX,
        Y: e.pageY
      });

      startTop = startData.top;
      startLeft = startData.left;
      endTop = startData.otherTop;
      endLeft = startData.otherLeft;
      scrollTop = startData.scrollTop;
    });

    $('body').mousemove(function(e){
      if(me.startElemActive){
        me.mousemove({
          target: 'start',
          elemTop: startTop,
          elemLeft: startLeft,
          otherTop: endTop,
          otherLeft: endLeft,
          X: e.pageX,
          Y: e.pageY,
          scrollTop: scrollTop
        })
      }
    });
  };

  //结束杆元素事件
  proto.endElemEvent = function(){
    var me = this;
    var config = me.config;

    var end = me.endElem;
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
      var startData = me.mousedown({
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
      if(me.endElemActive){
        me.mousemove({
          target: "end",
          elemTop: endTop,
          elemLeft: endLeft,
          otherTop: startTop,
          otherLeft: startLeft,
          X: e.pageX,
          Y: e.pageY,
          scrollTop: scrollTop
        });
      }
    });

  };

  //杆 鼠标按下事件
  proto.mousedown = function(cfg){
    var me = this;

    me.setLimit();

    //清除用鼠标选择的文本高亮色
    me.clearWinSelection();

    //被拖动的元素
    var targetElem = null;
    //另外一个元素
    var otherElem = null;

    $('body').addClass('disable-select-text');
    me.ctlScrollObj.addClass('disable-select-text');

    if(cfg.target == 'start'){
      me.targetElem = targetElem = me.startElem;
      otherElem = me.endElem;
      me.startElemActive = true;
    }
    if(cfg.target == 'end'){
      me.targetElem = targetElem = me.endElem;
      otherElem = me.startElem;
      me.endElemActive = true;
    }

    //元素的原始X坐标
    me.config.orginalX = cfg.X;
    //元素的原始Y坐标
    me.config.orginalY = cfg.Y;

    var targetPostion = targetElem.position(),
      otherPosition = otherElem.position();
    return{
      top: targetPostion.top,
      left: targetPostion.left,
      otherTop: otherPosition.top,
      otherLeft: otherPosition.left,
      scrollTop: me.ctlScrollObj.startTop()
    };
  };

  //杆 鼠标移动事件
  proto.mousemove = function(cfg){
    var me = this;
    var config = me.config;

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

    me.addMask();

    //针对webkit核心，拖动时仍然会导致滚动条滚动，要算上滚动前后的高度差
    var top = cfg.elemTop + (cfg.Y - config.orginalY) + (me.ctlScrollObj.scrollTop() - cfg.scrollTop);
    var left = cfg.elemLeft + (cfg.X = config.orginalX);

    if(cfg.target == 'start'){
      targetElem = me.startElem;
      otherElem = me.endElem;

      minT = 10;
      maxL = me.ctlObjBoxWidth - config.ctlElemWidth + 16;
      maxT = cfg.otherTop;

      top = Math.floor(top / config.lineHeight) * config.lineHeight + 10;
      if(left > (cfg.otherLeft - config.ctlElemWidth) && top > (maxT - config.ctlElemHeight)){
        top = maxT - config.ctlElemHeight;
      }
      top = Math.min(Math.max(minT, top), maxT);

      if(top >= cfg.otherTop && left > (cfg.otherLeft - config.ctlElemWidth)){
        left = cfg.otherLeft - config.ctlElemWidth;
      }
      left = Math.min(Math.max(minL, left), maxL);

      //设置被选的字幕相关参数
      subTop = top;
      subLeft = left + config.ctlElemWidth;
      subRight = cfg.otherLeft;
      subBottom = cfg.otherTop + config.ctlElemHeight;

      /*me.setSliderByClip({
        target: 'start',
        startTop: top,
        endTop: cfg.otherTop
      });*/
    }

    if(cfg.target == 'end'){
      targetElem = me.endElem;
      otherElem = me.startElem;

      minT = cfg.otherTop;
      maxT = me.ctlObjBoxHeight - config.ctlElemHeight + 10;
      maxL = me.ctlObjBoxWidth + 16;

      top = Math.floor(top / config.lineHeight) * config.lineHeight + 10;
      if (left < (cfg.otherLeft + config.ctlElemWidth) && top < (minT + config.ctlElemHeight)) {
        top = minT + config.ctlElemHeight;
      }
      top = Math.min(Math.max(minT, top), maxT);

      if (top <= cfg.otherTop && left < (cfg.otherLeft + config.ctlElemWidth)) {
        left = cfg.otherLeft + config.ctlElemWidth;
      }
      left = Math.min(Math.max(minL, left), maxL);

      //设置被选的字幕相关参数
      subTop = cfg.otherTop;
      subLeft = cfg.otherLeft + config.ctlElemWidth;
      subRight = left;
      subBottom = top + config.ctlElemHeight;

      /*me.setSliderByClip({
        target: "end",
        startTop: cfg.otherTop,
        endTop: top
      });*/

    }

    targetElem.css({"left": left, "top": top});

    //设置被选的字幕
    me.setSelected({
      top: subTop,
      left: subLeft,
      right: subRight,
      bottom: subBottom
    });

    me.setClipInfo();
    // me.setSliderTime(); 
  }

  //鼠标放开事件
  proto.mouseup = function(){
    var me = this;
    var config = me.config;
    var $body = $('body');

    $body.mouseup(function(){
      if(me.startElemActive || me.endElemActive/* || me.sliderBar.startActive || me.sliderBar.endActive*/){
        //设置开始杆非激活
        if(me.startElemActive){
          me.startElemActive = false;
        }
        //设置结束杆非激活
        if(me.endElemActive){
          me.endElemActive = false;
        }
        //设置开始滑块非激活
        /*if (me.sliderBar.startActive) {
          me.sliderBar.startActive = false;
        }*/
        //设置结束滑块非激活
        /*if (me.sliderBar.endActive) {
          me.sliderBar.endActive = false;
        }*/

        //取消 各种屏蔽拖动操作时的选择文字手段
        $body.removeClass('disable-select-text');
        me.ctlScrollObj.removeClass('disable-select-text');
        me.delMask();
        //更新选择杆的定位位置，贴近所选的文字边缘
        me.updateElemPos();

        window.startTime = config.clipStartTime;
        window.endTime = config.clipEndTime;
      }
    });
  };

  //拖动滑块时在整个网页上添加一个层防止文字内容等因为鼠标的拖动被选中
  proto.addMask = function(p){
    var me = this;

    if (!me.mask) {
      me.mask = $('<div class="clip-mask" onselectstart="return false;"></div>').appendTo($('body'));
    }
  };

  proto.delMask = function(p){
    var me = this;

    if (me.mask) {
      me.mask.remove();
      me.mask = null;
    }
  };

  //清除用鼠标选择的文本高亮色
  proto.clearWinSelection = function(){
    if (window.getSelection) {
      window.getSelection().removeAllRanges();
    } else {
      document.selection.empty();
    }
  };

  //将时间转成3'40"的形式
  proto.getTipsTime = function(t){
    t = Math.floor(t);
    var m = Math.floor(t / 60);
    var s = t - m * 60;
    return m + '\' ' + s + '"';
  };

  proto.setSubtitleLine = function(data, player){
    var me = this;
    var config = me.config;

    if (!data) {
      alert('Subtitle loading.');
      me.destroy();
    } else {
      // me.player = player;
      //设置不能点词播放
      // me.player.config.wordClickEvent = false;
      //停止字幕自动滚动
      me.stopAutoScroll();

      me.subtitleLine = data;
      var len = me.subtitleLine.length;
      for (var i = 0; i < len; i++) {
        var line = me.subtitleLine.eq(i);
        line.data('top', line.position().top).attr('top', line.position().top);
      }

      if (config.currentLine == undefined) {
        me.setCtlPos(0);
      } else {
        me.setCtlPos(config.currentLine);
      }
    }

  };


  //设置元素初始定位值
  proto.setCtlPos = function(index){
    var me = this;
    var config = me.config;

    me.setLimit();

    var line = me.subtitleLine.eq(index);
    var nextLine = null;
    var len = me.subtitleLine.length;
    var lineTemp = len - 1 - index;

    if (lineTemp >= 4) {
      nextLine = me.subtitleLine.eq(index + 4);
    } else {
      nextLine = me.subtitleLine.eq(index + lineTemp);
    }
    var startTop = parseFloat(line.attr('top'));
    var endTop = parseFloat(nextLine.attr('top'));
    var startLeft = 16 - config.ctlElemWidth;
    var lastWord = line.children('.word').eq(-1);
    var endLeft = lastWord.position().left + lastWord.width() + 16;

    me.startElem.css({"top": startTop, "left": startLeft}).removeClass('hidden');
    me.endElem.css({"top": endTop, "left": endLeft}).removeClass('hidden');

    //设置被选的字幕
    me.setSelected({
      top: startTop,
      left: startLeft + config.ctlElemWidth,
      right: endLeft,
      bottom: endTop + config.ctlElemHeight
    });

    me.setClipInfo();
    me.updateElemPos();

    /*me.setSliderByClip({
      target: "start",
      startTop: startTop,
      endTop: endTop
    });
    me.setSliderTime();*/

    window.startTime = config.clipStartTime;
    window.endTime = config.clipEndTime;
    // me.getFragmentUrl(config.clipStartTime, config.clipEndTime);
    //显示开始杆
    me.ctlScrollObj.scrollTop(me.startElem.position().top - config.lineHeight * 2);

  };


  //设置字幕不滚动
  proto.stopAutoScroll = function(){
    var me = this;
    var player = me.player;

    if (player) {
      player.config.isClipMode = true;
    }
  };

  //设置字幕滚动
  proto.autoScroll = function(){
    var me = this;
    var player = me.player;

    if (player) {
      player.config.isClipMode = false;
    }
  };

  //设置被选择的字幕。cfg Object {top, left, right, bottom}
  proto.setSelected = function(cfg){
    var me = this;
    var config = me.config;
    var startLine = null;
    var startWord = null;
    var startWordInLineIndex = -1;
    var endLine = null;
    var endWord = null;

    var len = me.subtitleLine.length;
    for(var i = 0; i < len; i++){
      var line = me.subtitleLine.eq(i);
      var lineTop = parseFloat(line.attr('top'));
      startLine = line;

      line.removeClass('selected-line');
      line.find('.word').removeClass('selected-word');
      line.find('.space').removeClass('selected-space');

      if(cfg.top >- lineTop && cfg.top < (lineTop + line.height())){
        line.addClass('selected-line');

        var lineWord = line.find('.word');
        var lineWordLen = lineWord.length;

        for(var j = 0; j < lineWordLen; j++){
          var word = lineWord.eq(j);
          var wordPostion = word.position();
          var wordTop = wordPostion.top;
          var wordLeft = wordPostion.left;

          if(cfg.top >= wordTop && cfg.top < (wordTop + word.height()) && cfg.left >= wordLeft && cfg.left < (wordLeft + word.width())){
            startWord = word;
            startWordInLineIndex = j;
          }

          if(startWord != null){
            word.addClass('selected-word');
            word.next('.space').addClass('selected-space');
          }
        }

        break;
      }
    }

    for(i = parseFloat(startLine.attr('index')); i < len; i++){
      line = me.subtitleLine.eq(i);
      lineTop = parseFloat(line.attr('top'));

      //找到结束所在的行
      if(cfg.bottom > lineTop && cfg.bottom <= (lineTop + line.height())){
        endLine = line;
        lineWord = line.find('.word');
        lineWordLen = lineWord.length;

        //如果结束杆和开始杆在同一行
        if(parseFloat(endLine.attr('index')) == parseFloat(startLine.attr('index'))){
          if(startWord != null){
            for(j = startWordInLineIndex; j < lineWordLen; j++){
              word = lineWord.eq(j);
              wordPostion = word.position();
              wordTop = wordPostion.top;
              wordLeft = wordPostion.left;

              if(cfg.bottom > wordTop && cfg.bottom <= (wordTop + word.height()) && cfg.right >= wordLeft && cfg.right < (wordLeft + word.width())){
                endWord = word;
              }

              if(endWord != null){
                word.removeClass('selected-word');
                word.next('.space').removeClass('selected-space');
              }
            }
          }
        }
        //如果开始和结束不在同一行
        else{
          for(j = 0; j < lineWordLen; j++){
            word = lineWord.eq(j);
            wordPostion = word.position();
            wordTop = wordPostion.top;
            wordLeft = wordPostion.left;
            word.addClass('selected-word');
            word.next('.space').addClass('selected-space');

            if (cfg.bottom > wordTop && cfg.bottom <= (wordTop + word.height()) && cfg.right >= wordLeft && cfg.right < (wordLeft + word.width())) {
              endWord = word;
            }

            if (endWord != null) {
              word.removeClass('selected-word');
              word.next('.space').removeClass('selected-space');
            }
          }
        }
      } else if (i != parseFloat(startLine.attr('index'))){
        if(endLine == null){
          line.addClass('selected-line');
          line.find('.word').addClass('selected-word');
          line.find('.space').addClass('selected-space');
        }else{
          line.removeClass('selected-line');
          line.find('.word').removeClass('selected-word');
          line.find('.space').removeClass('selected-space');
        }
      }
    }
  };

  //修正开始、结束杆的定位，以贴近所选择的字
  proto.updateElemPos = function(){
    var me = this;
    var config = me.config;

    var len = me.selectedWords.length;

    if(len > 0){
      var startSelected = me.selectedWords.eq(0);
      var startLeft = startSelected.position().left - config.ctlElemWidth;
      var startTop = startSelected.position().top;

      var endSelected = me.selectedWords.eq(len - 1);
      var endLeft = endSelected.position().left + endSelected.width();
      var endTop = endSelected.position().top;

      me.startElem.css({left: startLeft, top: startTop});
      me.endElem.css({left: endLeft, top: endTop});

      me.setSliderByClip({
        target: "start",
        startTop: startTop,
        endTop: endTop
      });
    }
  };

  //设置所选择部分的各种信息
  proto.setClipInfo = function(){
    var me = this;
    var config = me.config;

    me.selectedWords = me.ctlScrollObj.find('.selected-word');
    var len = me.selectedWords.length;
    config.clipStartTime = me.selectedWords.eq(0).attr('st');
    config.clipEndTime = me.selectedWords.eq(len - 1).attr('et');

    // me.setClipTimeElem(Math.floor(config.clipEndTime) - Math.floor(config.clipStartTime));
  };

  //设置显示碎片时长
  /*proto.setClipTimeElem = function (t) {
    var me = this;

    me.clipTimeElem.text(proto.getTipsTime(t));
  };*/

  //碎片分享地址
  proto.getFragmentUrl = function (st, et) {
    var me = this;
    var config = me.config;

    var url = '/fragment/create/' + JLib.config.playerGUID + '?st=' + st + '&et=' + et;
    $.get(url, {},
      function (data) {
        data = stringToJSON(data);
        //成功
        if (data.status == 1) {
          config.shareUrl = JLib.config.hostname + '/fragment/' + data.fragment_guid;
          JLib.config.fragment_guid = data.fragment_guid;
        }
        //失败
        else {
          alert(data.message);
        }
      }).fail(function () {
        //alert('Sorry! System busy. Please retry later.');
      });
  };

  //视频预览
  proto.clipPreview = function (st, et) {
    var me = this;
    var config = me.config;

    //暂停原视频
    if (me.player) {
      me.player.pause();
    }

    console.log(me.previewIframe);
    me.previewIframe[0].src = '';

    var url = '/review?guid=' + JLib.config.playerGUID + '&st=' + st + '&et=' + et;
    //var url = '/static/source_demo/json/play_sewise_preview.json';

    me.previewCont.removeClass('hidden');
    me.previewIframe[0].src = url;

  };

  function stringToJSON(data){
      if (typeof data != 'object'){
          return $.parseJSON(data);
      } else {
          return data;
      }
  };


  return {
    subtitleSelect: subtitleSelect
  };


});