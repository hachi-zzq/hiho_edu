define(['jquery'], function(){
  var commentTpl = {};
  //回复框
  commentTpl.replyForm = [
    '<div class="comment-box reply-box">',
      '<form action="" name="replyForm" id="reply_form">',
        '<textarea class="textarea" name="replyContent" id="reply_content" cols="30" rows="10"></textarea>',
        '<div class="action">',
          '<span class="comment-num"><em class="num">200</em> words</span>',
          '<input type="submit" class="btn-s4" value="Post" />',
        '</div>',
      '</form>',
    '</div>'
  ].join('');

  function isLogin(){
    if (JLib.config.userId < 0){
      alert('Please sign in first.');
      return false;
    }
    return true;
  }

  function emptyTips(cfg){
    var tpl = [
      '<div class="tips-cont tips-cont-empty">',
        '<div class="tips-box">',
          '<div class="tips-info">',
            '<p class="tips-tit"></p>',
          '</div>',
        '</div>',
      '</div>'
    ].join('');
    var $tpl = $(tpl);
    $tpl.find('.tips-tit').text(cfg.text);
    $tpl.appendTo(cfg.target);
  }

  function commentMod(cfg){
    this.config = cfg;
    this.config.listItems = this.config.listCont.find('.list ul');
    this.config.commentMax = 200;
    this.config.replyMax = 200;
    this.config.hasMoreComment = true;
    this.config.page = 1;
  };

  var _proto = commentMod.prototype;

  _proto.init = function(){
    var _self = this;

    //评论
    _self.comment();
    //删除评论
    _self.commentDel();
    //回复
    _self.reply();
    //删除回复
    //_self.replyDel();
    //加载更多评论
    //_self.commentMore();
    //more按钮事件
    _self.moreEvent();
  };

  //评论
  _proto.comment = function(){
    var _self = this;
    var cfg = _self.config;

    var commentForm = cfg.commentBox.find('#comment_form');
    var inputTxt = cfg.commentBox.find('.textarea');
    var num = cfg.commentBox.find('.comment-num .num');
    num.text(cfg.commentMax);

    inputTxt.keyup(function(e){
      var txt = $.trim(inputTxt.val());

      var n = cfg.commentMax - txt.length;
      num.text(n);
      if (n < 0){
        num.addClass('warn-color');
      } else {
        num.removeClass('warn-color');
      }
    });

    commentForm.submit(function(e){
      e.preventDefault();

      if (isLogin()){
        var txt = $.trim(inputTxt.val());

        if (txt == ''){
          inputTxt.focus();
        } else if(txt.length > cfg.commentMax) {
          alert('Up to ' + cfg.commentMax + ' characters can only comment.');
        } else {
          var time = _self.getCurrentTime();
          console.log("playTime", _self.getPlayTime());
          $.post("/comment", {"play_time": time, "user_id": JLib.config.userId, "video_id": cfg.videoId, "fragment_id": cfg.fragmentId, "content": txt, "reply_id": 0},
            function(data){

              if (data.status == 1){
                cfg.listCont.find('.tips-cont-empty').remove();

                $(data.commentHtml).prependTo(cfg.listItems);
                inputTxt.val('');
                num.text(cfg.commentMax);
              } else {
                alert(data.message);
              }

          }, 'json').fail(function(){
            alert('Sorry! System busy. Please retry later.');
          });
        }
      }
    });
  };

  //回复
  _proto.reply = function(){
    var _self = this;
    var cfg = _self.config;

    cfg.listCont.on('click', '.reply', function(e){
      e.preventDefault();

      if (isLogin()){
        var _reply = $(this);
        var $item = _reply.parents('li').eq(0);
        var itemRel = $item.data('rel');
        var $itemInfo = $item.find('.item-info');

        if (cfg.replyDom){
          cfg.replyDom.remove();
        }

        cfg.replyDom = $(commentTpl.replyForm).appendTo($itemInfo);

        var userName = $item.find('.user').text();
        var replyForm = cfg.replyDom.find('#reply_form');
        var inputTxt = cfg.replyDom.find('.textarea');
        inputTxt.val('@' + userName + ' ');
        var num = cfg.replyDom.find('.comment-num .num');
        num.text(cfg.replyMax);
        var txtLen = inputTxt.val().length;

        function checkNum(){
          var txt = $.trim(inputTxt.val());

          var n = cfg.replyMax - txt.length;
          num.text(n);
          if (n < 0){
            num.addClass('warn-color');
          } else {
            num.removeClass('warn-color');
          }
        }

        inputTxt.keyup(function(e){
          checkNum();
        }).focus(function(){
          checkNum();
        });

        //设置光标位置
        if(document.selection){
          var range = inputTxt[0].createTextRange();
          range.moveEnd('character', -txtLen);
          range.moveStart('character', txtLen);
          range.select();
        } else {
          inputTxt[0].setSelectionRange(txtLen, txtLen);
          inputTxt.focus();
        }

        replyForm.submit(function(e){
          e.preventDefault();
          var txt = $.trim(inputTxt.val());

          if (txt == ''){
            inputTxt.focus();
          } else if(txt.length > cfg.replyMax) {
            alert('Up to ' + cfg.replyMax + ' characters can only reply.');
          } else {
            // console.log("playTime", _self.getPlayTime());
            var time = _self.getCurrentTime();

            //$.post("/static/source_demo/json/comment.json", {"play_time": time, "user_id": JLib.config.userId, "video_id": cfg.videoId, "fragment_id": cfg.fragmentId, "content": txt, "reply_id": itemRel.id},
            $.post("/comment", {"play_time": time, "user_id": JLib.config.userId, "video_id": cfg.videoId, "fragment_id": cfg.fragmentId, "content": txt, "reply_id": itemRel.id},
              function(data){

                if (data.status == 1){
                  $(data.commentHtml).prependTo(cfg.listItems);
                  $(win).scrollTop(cfg.listCont.offset().top - 150);
                  cfg.replyDom.remove();
                  cfg.replyDom = null;
                } else {
                  alert(data.message);
                }

            }, 'json').fail(function(){
              alert('Sorry! System busy. Please retry later.');
            });
          }
        });
      }
    });
  };

  //获取播放器播放时间
  _proto.getCurrentTime = function(){
    var _self = this;
    var cfg = _self.config;

    return cfg.player.getCurrentTime();
  };

  //删除评论、回复
  _proto.commentDel = function(){
    var _self = this;
    var cfg = _self.config;

    cfg.listCont.on('click', '.delete', function(e){
      e.preventDefault();

      if (isLogin()){
        var confirm = window.confirm("Sure want to delete this comment?");

        if (confirm){
          var _del = $(this);
          var $item = _del.parents('li').eq(0);
          var itemRel = $item.data('rel');

          //$.post("/static/source_demo/json/comment.json", {"reply_id": itemRel.id},
          $.post("/delComment", {"reply_id": itemRel.id},
            function(data){

              if (data.status == 1){
                $item.remove();
              } else {
                alert(data.message);
              }
          }, 'json').fail(function(){
            alert('Sorry! System busy. Please retry later.');
          });
        }

      }
    });
  };

  //加载评论
  _proto.commentMore = function(){
    var _self = this;
    var cfg = _self.config;
    var moreComment = cfg.listCont.find('#more_comment');
    var moreBtn = moreComment.find('.load');
    var loading = moreComment.find('.loading');

    if (cfg.hasMoreComment){
      loading.removeClass('hidden');
      moreBtn.addClass('hidden');

      //$.get("/static/source_demo/json/load_comment.json", {"page": cfg.page, "video_id": cfg.videoId, "fragment_id": cfg.fragmentId},
      $.get("/loadComment", {"page": cfg.page, "video_id": cfg.videoId, "fragment_id": cfg.fragmentId},
        function(data){
          if (data == '' && cfg.page == 1){
            cfg.hasMoreComment = false;
            moreComment.remove();

            emptyTips({
              "text": "Comment is empty.",
              "target": cfg.listCont
            });
          }
          else if (data == ''){
            cfg.hasMoreComment = false;
            moreComment.remove();
            $('<p class="no-more">No more comments.</p>').appendTo(cfg.listCont);
          }
          else {
            $(data).appendTo(cfg.listItems);

            cfg.page++;
            moreBtn.removeClass('hidden');
          }

          loading.addClass('hidden');
      }).fail(function(){
        //alert('Sorry! System busy. Please retry later.');
        loading.addClass('hidden');
        moreBtn.removeClass('hidden');
      });
    }
  };

  //more按钮事件
  _proto.moreEvent = function(){
    var _self = this;
    var cfg = _self.config;
    var moreBtn = cfg.listCont.find('#more_comment .load');

    if (moreBtn[0]){
      moreBtn.click(function(e){
        e.preventDefault();

        _self.commentMore();
      });
    }
  };

  return {
    isLogin: isLogin,
    emptyTips: emptyTips,
    commentMod: commentMod
  }

});