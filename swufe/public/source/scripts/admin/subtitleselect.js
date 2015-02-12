define(function(){
  'use strict';

  function subtitleSelect(options){
    if(!(this instanceof subtitleSelect)){
      return new subtitleSelect(options);
    }

    this.$subtitleList = options.$subtitleList.css({position: 'relative'});
    this.$lines = this.$subtitleList.find('.line');
    this.$scrollBox = options.$scrollBox || this.$subtitleList.parents('.panel');
    this.lineHeight = parseInt(this.$lines.eq(0).css('lineHeight'));
    this.subtitleBorderLeftWidth = parseInt(this.$subtitleList.css('borderLeftWidth'));

    this.initElements(options);
    this.initEvents(options);

    this.onSelect = options.onSelect;
  }

  /**
    state variables:
      $subtitleList: subtitle container;
      $line: subtitle lines;
      $start: selection start handle;
      $end: selection end handle;
      lineHeight: line-height value of subtitle;
      mouseStartTop: mouse top cord on mousedown (related to $subtitleList);
      mouseStartLeft: mouse left cord on mousedown (related to $subtitleList);
      mouseEndTop: mouse top cord on mouseup (related to $subtitleList);
      mouseEndLeft: mouse left cord on mouseup (related to $subtitleList);
      isSelecting: boolean shows whether user is selecting;
   */

  var proto = subtitleSelect.prototype;

  proto.tplStartElem = '<span class="clip-share-start hidden" id="select_start" title="Clip start"></span>';
  proto.tplEndElem = '<span class="clip-share-end hidden" id="select_end" title="Clip end"></span>';
  proto.tplBubble = $.trim($('#tplBubble').remove().html());
  proto.selectedLineClass = 'selected-line';
  proto.selectedWordClass = 'selected-word';
  proto.selectedSpaceClass = 'selected-space';
  proto.highlightClass = 'word-highlight';

  proto.selected

  proto.initElements = function(options){
    // append start and end element
    this.$start = $(this.tplStartElem);
    this.$end = $(this.tplEndElem);
    this.$subtitleList.append(this.$start).append(this.$end);
    // add top cords to lines
    this.$lines.each(function(){
      var $this = $(this),
        linePosition = $this.position();
      $this.data({
        // left: linePosition.left,
        top: linePosition.top
      });
    });
    // append bubble
    this.$bubble = $(this.tplBubble).hide();
    /*this.$bubble.on('mousedown mouseup', 'a', function(evt){
      // stop propagation
      evt.stopPropagation();
    });*/
    this.$subtitleList.append(this.$bubble);
  }

  proto.initEvents = function(options){
    var me = this;

    me.$subtitleList.mousedown(function(evt){
      var subtitleOffset = me.$subtitleList.offset();
      // set selecting state
      me.isSelecting = !me.$bubble.is(evt.target) && !$.contains(me.$bubble.get(0), evt.target);
      if(me.isSelecting){
        me.mouseStartTop = evt.pageY - subtitleOffset.top;
        me.mouseStartLeft = evt.pageX - subtitleOffset.left;
        me.undoSelection();
      }
    });

    me.$subtitleList.mouseup(function(evt){
      var subtitleOffset = me.$subtitleList.offset();
      if(me.isSelecting){
        me.mouseEndTop = evt.pageY - subtitleOffset.top;
        me.mouseEndLeft = evt.pageX - subtitleOffset.left;
        // set selecting state
        me.isSelecting = false;

        me.showSelectionByCords();

        if($.isFunction(me.onSelect)){
          me.onSelect(me.getSelectedTime());
        }
      }
    });

  }

  proto.undoSelection = function(){
    var me = this,
      $selectedLines = me.$lines.filter('.' + me.selectedLineClass).removeClass(me.selectedLineClass);
    $selectedLines.children().removeClass(me.selectedWordClass + ' ' + me.selectedSpaceClass);
    me.hideSelectors();
    me.hideBubble();
  }

  proto.hideSelectors = function(){
    this.$start.hide();
    this.$end.hide();
  }

  proto.hideBubble = function(){
    this.$bubble.hide();
  }

  proto.showSelectionByCords = function(){
    var me = this,
      subtitleOffsetLeft = me.$subtitleList.offset().left + parseInt(me.$subtitleList.css('borderLeftWidth')),
      isFirstLine = true;

    me.$lines.each(function(){
      var $line = $(this),
        lineTop = $line.data('top'),
        isLastLine = lineTop + me.lineHeight > me.mouseEndTop;
      if(lineTop + me.lineHeight > me.mouseStartTop){
        if(lineTop <= me.mouseEndTop){
          $line.addClass(me.selectedLineClass).children('.word, .space').each(function(){
            var $char = $(this),
              charWidth = $char.width(),
              charOffsetLeft = $char.offset().left;
            if(!(isFirstLine && charOffsetLeft + charWidth / 2 - subtitleOffsetLeft < me.mouseStartLeft)){
              if(isLastLine && charOffsetLeft + charWidth / 2 - subtitleOffsetLeft > me.mouseEndLeft){
                return false;
              }else{
                if($char.hasClass('word')){
                  $char.addClass(me.selectedWordClass);
                }else{
                  $char.addClass(me.selectedSpaceClass);
                }
              }
            }
          });
        }else{
          return false;
        }
        if(isFirstLine){
          isFirstLine = false;
        }
      }
    });
    me.clearDefaultSelection();

    var $seletedWords = me.$subtitleList.find('.' + me.selectedWordClass),
      $seletedLines = me.$lines.filter('.' + me.selectedLineClass),
      $firstWord = $seletedWords.eq(0),
      $lastWord = $seletedWords.eq(-1);
    if($seletedWords.length){
      me.startTime = parseFloat($firstWord.data('st'));
      me.endTime = parseFloat($lastWord.data('et'));
      // show selectors
      me.$start.css({
        left: $seletedWords.eq(0).offset().left - subtitleOffsetLeft - me.$start.width(),
        top: $seletedLines.eq(0).data('top')
      }).removeClass('hidden').show();
      me.$end.css({
        left: $lastWord.offset().left + $lastWord.width() - subtitleOffsetLeft,
        top: $seletedLines.eq(-1).data('top') + me.lineHeight - me.$end.height()
      }).removeClass('hidden').show();
      // show bubble
      me.$bubble.css({
        left: $lastWord.offset().left + $lastWord.width() - subtitleOffsetLeft - me.$bubble.width() / 2,
        top: $seletedLines.eq(-1).data('top') + me.lineHeight - me.$end.height() - me.$bubble.height() - 12
      }).show();
    }else{
      me.startTime = 0;
      me.endTime = 0;
      me.hideSelectors();
      me.hideBubble();
    }
  }

  proto.clearDefaultSelection = function(){
    if (window.getSelection) {
      window.getSelection().removeAllRanges();
    } else {
      document.selection.empty();
    }
  };

  proto.turnToMark = function(){
    this.$subtitleList.find('.' + this.selectedWordClass + ', .' + this.selectedSpaceClass).addClass(this.highlightClass);
    this.undoSelection();
  }

  proto.getSelectedTime = function(){
    return {
      start: this.startTime,
      end: this.endTime
    }
  }

  /*proto.getScrollTop = function(options){
    return this.$scrollBox.scrollTop();
  }*/


  return {
    subtitleSelect: subtitleSelect
  };


});