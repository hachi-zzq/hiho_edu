require(['config'], function(){
  require(['infrastructure'], function(){
    require(['blockui', 'sewisecontrol', 'clip'], function(blockUI, sewiseControl, clip){

      var $playerHolder = $('#playerHolder'),
        $addToNoteDialog = $('#addToNoteDialog'),
        $createNoteBookButton = $addToNoteDialog.find('#createNoteBookButton'),
        $createNewPlaylist = $('#createNewPlaylist'),
        videoInfo = $playerHolder.data();

      window.JLib = window.JLib || {};
      JLib.config = JLib.config || {};
      // JLib.config.pageName = 'play';
      if(videoInfo.videotype === 'fragment'){
        JLib.config.playerJSON = '/subtitle/fragment?video_playid=' + videoInfo.videoplayid + '&st=' + videoInfo.starttime + '&et=' + videoInfo.endtime + '&language=' + videoInfo.language + '&type=json&version=2';
      }else{
        JLib.config.playerJSON = '/subtitle?video_guid=' + videoInfo.guid + '&language=' + videoInfo.language + '&type=json&version=2';
      }
      JLib.config.playerGUID = videoInfo.guid;
      JLib.config.playid = videoInfo.playid;
      JLib.config.hostname = window.location.origin;
      JLib.config.userId = videoInfo.userId;
      JLib.config.videoId = videoInfo.guid;
      JLib.config.fragmentId = "0";

      window.jiathis_config = {
          url: window.location.href,
          title: $('title').text(),
          summary: "I have a new video clip on Swufe, everyone to watch. @Autotiming",
          pic: window.location.origin + '/vi/' + videoInfo.guid,
          shortUrl: false,
          hideMore: false
      }

      // tags
      var $tags = $('.tab a'),
        $tabs = $('.tab-content');
      $tags.click(function(){
        var index = $tags.index(this),
          $this = $(this);
        if(!$this.hasClass('disable')){
          $tags.removeClass('current');
          $this.addClass('current');
          $tabs.hide().eq(index).show();
        }
        return false;
      });

      // shrink
      var $container = $('.player-container');
      $('.shrink-btn a').click(function(){
        $container.toggleClass('shrinked');
        return false;
      });

      sewiseControl.load('playerHolder', function(){
        SewisePlayer.setup({
          server: 'vod',
          videourl: videoInfo.resourceflv,
          title: videoInfo.title,
          skin: 'vodWhite',
          logo: '/blank',
          playername: ' ',
          copyright: ' '
        });


        //搜索高亮
        var subtitleSearchForm = $('#subtitle_search');
        var subtitleInput = subtitleSearchForm.find('.input');
        var originSubtitleCol = $('#subtitle');
        var subtitleCont = originSubtitleCol.find('.subtitle-content');
        var subtitleBox = originSubtitleCol.find('.subtitle-box');
        var subtitleBoxHeight = 0;
        var searchNum = 0;
        var numElem = $('<span class="key-count"></span>').appendTo(subtitleSearchForm);
        //标搜索的词所在的位置
        var keywordPos = originSubtitleCol.find('#keyword_pos');
        var keywordPosWidth = keywordPos.width();

        //鼠标表单提交（如果设置input失去焦点时提交在chrome和ff下会有各自的bug）
        subtitleSearchForm.submit(function (e) {
          e.preventDefault();

          setHighlightWord($.trim(subtitleInput.val()), 'searchInput');
        });

        function setHighlightWord(keyword, from) {
          //console.log('setHighlightWord');
          searchNum = 0;

          var hl = subtitleCont.find('.hl-cont');
          hl.each(function () {
            var _self = $(this);
            _self.children().insertAfter(_self);
            _self.remove();
          });
          keywordPos.empty();

          if (keyword == '') {
            numElem.text('');
          } else {
            clearWinSelection();

            if (window.find) {
              //console.log('window.find');
              while (true) {
                var check = window.find(keyword);

                if (!check)
                  break;

                var sel = window.getSelection();

                var start = sel.getRangeAt(0).startContainer.parentNode;
                var end = sel.getRangeAt(0).endContainer.parentNode;

                if (!$(start).hasClass('word') || !$(end).hasClass('word'))
                  continue;

                var p = $(start);
                var next = start;
                while (next != null && next != end) {
                  p = p.add(next);
                  next = next.nextSibling;
                }
                p = p.add(end);
                p.wrapAll('<i class="hl-cont hl-word"></i>');

                searchNum++;

                setKeywordPos({
                  "elem": $(start),
                  "cont": keywordPos,
                  "contWidth": keywordPosWidth,
                  "subtitleBox": subtitleBox
                });
              }
            } else if (document.body.createTextRange) {
              //console.log('createTextRange');
              var range = document.body.createTextRange();

              while (range.findText(keyword)) {
                var start = $(range.parentElement());
                if (start.hasClass('word')) {
                  start.wrapAll('<i class="hl-cont hl-word"></i>');
                }
                //折叠到终点
                range.collapse(false);

                searchNum++;

                setKeywordPos({
                  "elem": start,
                  "cont": keywordPos,
                  "contWidth": keywordPosWidth,
                  "subtitleBox": subtitleBox
                });
              }
            }

            subtitleCont.scrollTop(0);
            $(window).scrollTop(0);

            clearWinSelection();

            if (from == 'searchInput') {
              if (searchNum == 0) {
                numElem.text('未找到关键字');
              } else if (searchNum == 1) {
                numElem.text('找到 1 个关键字');
              } else {
                numElem.text('找到 ' + searchNum + ' 个关键字');
              }
            }
          }
        }

        //设置关键词标识
        function setKeywordPos(cfg) {
          var top = parseFloat(cfg.elem.parents('.line').eq(0).position().top);
          var elem = $('<span class="kw"></span>').appendTo(cfg.cont);
          subtitleBoxHeight = subtitleBoxHeight == 0 ? cfg.subtitleBox.height() : subtitleBoxHeight;
          var left = top / subtitleBoxHeight * cfg.contWidth;
          elem.css('left', left).attr('top', top);
        }

        //点击关键词标识跳转到相关关键词
        keywordPos.on('click', '.kw', function (e) {
          e.stopPropagation();

          var top = $(this).attr('top');
          subtitleCont.scrollTop(top);
        });

        var originPlayer = window.op = new sewiseControl.player();
        var $originPlayer = $('#playerHolder');

        var lineElemArray = null;

        originPlayer.init({
          "videoElem": $originPlayer,
          "subtitleElem": originSubtitleCol,
          "srtJson": JLib.config.playerJSON,
          "mod": 'page',
          //字幕加载完后回调
          "subtitleCallbackFunc": function (data) {
            lineElemArray = data;

            //根据?k=helloworld
            var matchSearch = (/(?:\?|&)k\=([^&]+)/g).exec(document.location.search);
            if (matchSearch != null) {
              setHighlightWord(decodeURIComponent(matchSearch[1].replace(/\+/g, ' ')), 'searchPage');
            }
          }
          /*,
           //播放完成后回调
           "finishCallback": function(){
           playEndCont.removeClass('hidden');
           }*/
        });
        //重播
        /*btnReplay.click(function(e){
         e.preventDefault();

         playEndCont.addClass('hidden');
         originPlayer.replay();
         });*/

        var originJiathisConfig = jiathis_config;
        var shareTitle = jiathis_config.title;
        jiathis_config.title = '#swufe#';

        //分享
        var videoShare = $('.video-share');
        videoShare.on('mouseenter', '.jiathis_style_32x32 .jiathis_txt', function (e) {
          jiathis_config = originJiathisConfig;
          setShareSummary(e, shareTitle);
        });

        //分享
        var btnClipShare = $('#btn_clip_share');
        if (btnClipShare[0]) {
          jiathis_config = {
            url: originJiathisConfig.url,
            title: originJiathisConfig.title,
            summary: originJiathisConfig.summary,
            pic: originJiathisConfig.pic,
            shortUrl: false,
            hideMore: false
          };

          btnClipShare.click(function (e) {
            e.preventDefault();

            clearWinSelection();

            try{
              originPlayer.pause();
              var currentLine = originPlayer.getCurrentLineIndex();

              var clipShare = new clip.clipShare();
              clipShare.init({
                "container": $('#origin_video_subtitle'),
                "lineHeight": 24,
                "currentLine": currentLine,
                "share": {"title": shareTitle},
                addShow: addShow
              });
              clipShare.setSubtitleLine(lineElemArray, originPlayer);
            }catch(e){}
          });
        }

        //播放完后
        //焦点图
        /*var focusPic = new jTools.focusPic({
         "focusEl": $('#focus_pic'),
         "speed": 3000,
         "showItemNum": 4
         });
         focusPic.init();*/

        //复制页面分享地址
        /*jTools.copyToClip({
          btn: $('#copy_origin_link'),
          target: $('#origin_link'),
          type: 'text',
          afterCopy: function () {
            alert('Copy succeed.');
          }
        });*/

        //收藏
        var favActiveClass = 'ico-fav-active';//已收藏
        $('#ico_fav').click(function (e) {
          e.preventDefault();
          var _self = $(this);
          var _self = $(this)

          if(_self)
          $.post('/favourite', {"video_id": JLib.config.videoId, "fragment_id": JLib.config.fragmentId},
            function (data) {

              if (data.status == 1) {
                if (_self.hasClass(favActiveClass)) {
                  _self.removeClass(favActiveClass);
                } else {
                  _self.addClass(favActiveClass);
                }
              } else {
                alert(data.message);
              }
            }, 'json').fail(function () {
              alert('Sorry! System busy. Please retry later.');
            });
        });

        //评论
       /* var commentList = $('#comment_list');
        var commentMod = new comment.commentMod({
          "commentBox": $('#comment_col .comment-box'),
          "listCont": commentList,
          "player": originPlayer,
          "videoId": JLib.config.videoId,
          "fragmentId": JLib.config.fragmentId
        });
        commentMod.init();*/
      });

      function clearWinSelection(){
        if (window.getSelection){
          window.getSelection().removeAllRanges();
        } else {
          document.selection.empty();
        }
      }

      function setShareSummary(e, title, from){
        var target = $(e.target);
        jiathis_config.summary =  ' 我在swufe上剪辑了一段“' + title + '”的视频，快来看看吧。@autotiming';
      }


      /* 视频剪辑、笔记 */

      function addShow(){
        $.ajax({
          'url':'/note/getMyNoteList',
          'dataType': 'json',
          'success':function(data){
            if(data.msgCode != 0){
              location.href = '/login';
            }else{
              if((len =data.list.length) !=0){
                var html = '';
                for(i=0;i<len;i++){
                  html +='<option value="'+data.list[i]['id']+'">'+data.list[i]['title']+'</option>';
                }
                // console.log(html);
                $('#playlist_id').empty().append(html);
              }
              $addToNoteDialog.attr('class',' ').attr('class','modal-wrap');
            }
          },
          'error':function(){
            alert('服务异常，请稍后再试');
          }
        })
        return false;
      }

      $('#addToNote').click(addShow);

      $('#cloaseAddToNoteDialog').click(function(){
        $addToNoteDialog.hide();
        return false;
      });

      $('#addToNoteButton').click(function addInMyPlaylist(){

        if(videoInfo.videotype !== 'fragment'){
          //create fragment
          var st = window.startTime;
          var et = window.endTime;
          var url = '/fragment/getWithPlayIdAndStEt/?playid=' + videoInfo.playid + '&st=' + st + '&et=' + et;
          $.ajax({
            type:"GET",
            url:url,
            async:false,//设置同步ajax
            dataType: 'json',
            success:function(data){
              //成功
              if (data.status == 1) {
                JLib.config.shareUrl = JLib.config.hostname + '/fragment/' + data.fragment_guid;
                JLib.config.fragment_guid = data.fragment_guid;
                addFragmentToNote();
              }
              //失败
              else {
                alert(data.message);
                return false;
              }
            }
          });
        }else{
          JLib.config.fragment_guid = videoInfo.guid;
          addFragmentToNote();
        }

        return false;
      });

      function addFragmentToNote(){
        $.ajax({
          'url':'/my/note/addInMyPlaylist',
          'type':'POST',
          'data':'playlist_id='+$('#playlist_id').val()+'&title='+$('#title').val()+'&description='+$('#description').val()+'&guid='+JLib.config.fragment_guid,
          'success':function(res){
            // console.log(res);
            res = JSON.parse(res);
            if(res.status == -1){
              $.blockUI({ fadeIn: 700, fadeOut: 700, timeout: 500 ,message:'<p style="color:white"> 碎片创建失败!</p>'});
              return false;
            }else if(res.status == -2){
              $.blockUI({ fadeIn: 700, fadeOut: 700, timeout: 500 ,message:'<p style="color:white"> 碎片已经存在!</p>'});
              return false;
            }else{
              $.blockUI({ fadeIn: 700, fadeOut: 700, timeout: 500 ,message:'<p style="color:white">添加成功!</p>'});
              $addToNoteDialog.attr('class','modal-wrap hidden');
              return false;
            }
          },
          'error':function(){
            $.blockUI({ fadeIn: 700, fadeOut: 700, timeout: 500 ,message:'<p style="color:white">ajax error!</p>'});
          }
        })
      }

      $createNoteBookButton.click(function(){
        $createNewPlaylist.removeClass('hidden');
        $addToNoteDialog.addClass('hidden');
        return false;
      });

      $('#cancelAddToNoteButton').click(function(){
        $addToNoteDialog.attr('class','modal-wrap hidden');
        return false;
      });

      $('#closeCreateNewPlaylist').click(function(){
        $createNewPlaylist.attr('class','modal-wrap hidden');
        return false;
      });

      $('#createNewPlaylistConfirm').click(function createPlaylist(){
        $.ajax({
          'url':'/note/create',
          'type':'POST',
          'data':$('#create_form').serialize(),
          'beforeSend':function(){
            //$.blockUI({message:'<img src="/static/hiho-edu/img/busy.gif"/>'});
          },
          'success':function(data){
            console.log(data);
            data =  JSON.parse(data);
            if(data.status == -2 || data.status ==-1){
              $.blockUI({ fadeIn: 700, fadeOut: 700, timeout: 500 ,message:'<p style="color:white">'+data.message+'</p>'});
              return;
            }
            $.blockUI({ fadeIn: 700, fadeOut: 700, timeout: 500 ,message:'<p style="color:white">添加成功</p>'});
            $('#playlist_id').append('<option value="'+data.data.id+'">'+data.data.title+'</option>');
            $addToNoteDialog.attr('class','modal-wrap');
            $createNewPlaylist.attr('class','modal-wrap hidden');
          },
          'complete':function(){

          },
          'error':function(){
            $.blockUI({ fadeIn: 700, fadeOut: 700, timeout: 500 ,message:'<p style="color:white">ajax error!</p>'});
          }
        });
        return false;
      });

      $('#createNewPlaylistCancel').click(function(){
        $createNewPlaylist.attr('class','modal-wrap hidden');
        return false;
      });

      // $(document).ready(function(){
        var $like = $(".like");
        $like.click(function(){
          var video_id = videoInfo.playid,
            $this = $(this),
            $repeat = $('.success'),
            $login = $('.login');
          if(!$this.hasClass('liked')){
            $.post(
              '/favorite/add',
              {video_id:video_id},
              function(data){
                if(data.status == 0) {
                  var liked = $(".like").addClass('liked').children('span').text();
                  liked++;
                  $(".like").addClass("liked").children('span').text(liked);
                }
                else if(data.status == -1) {
                  $repeat.fadeIn();
                  setTimeout(function(){
                    $repeat.fadeOut();
                  }, 2000);
                }else if(data.status == -2){
                  $login.fadeIn();
                  setTimeout(function(){
                    $login.fadeOut();
                  }, 2000);
                }
              },
              'json'
            );
          }else{
            $.ajax({
              url: '/favorite/delete',
              type: 'POST',
              dataType: 'json',
              data: {
                playid: video_id
              }
            }).done(function(res){
              if(res){
                if(res.status == 0){
                  var $count = $('.like').removeClass('liked').find('.like-count');
                  $count.text(parseInt($.trim($count.text())) - 1);
                }else if(res.status == -1){
                  $repeat.fadeIn();
                  setTimeout(function(){
                    $repeat.fadeOut();
                  }, 2000);
                }else if(res.status == -2){
                  $login.fadeIn();
                  setTimeout(function(){
                    $login.fadeOut();
                  }, 2000);
                }
              }
            });
          }
          return false;
        });
        $(".close").click(function(){
          $(".notification").hide();
        });

        /*$("#textComment").keyup(function(e){
          var max = 124;
          if(e.keyCode == 8){
            var currentNum = $("#textComment").val().length;
            remain = max - currentNum;
            if(remain <= max) {
              $(".text-count>strong").html(remain);
            }
          }
          else{
            var content = $("#textComment").val();
            var currentNum = content.length;
            var remain = max - currentNum;
            if(currentNum <= max) {
              $(".text-count>strong").html(remain);
            }
            else{
              var last = content.substr(0, max);
              $("#textComment").val(last);
            }
          }
        });

        $("#btnAddComment").click(function(){
          var video_id = $("#hiddenVideoID").val();
          var content = $("#textComment").val();
          var playing_time = 2.0;
    //      var playing_time = JLib.player.prototype;
    //      console.log(playing_time);return;

          $.post(
            '/comment/add',
            {video_id:video_id, playing_time:playing_time, content:content},
            function(data){
              var obj = $.parseJSON(data);
              if(obj.status == 0){
                $("#textComment").val('');

                var avatar = obj.comment.avatar;
                var name = obj.comment.userName;
                var created = obj.comment.created_at;
                var content = obj.comment.content;

                var commentHtml = '<div class="comment-item">\
                          <div class="avatar">\
                            <img src="' + avatar + '" alt="">\
                          </div>\
                          <div class="comment-content">\
                            <div class="comment-user">\
                              <!--<a href="javascript:void(0);" class="reply">回复</a>-->\
                              <span class="name">' + name + '</span>\
                              <span>' + created + '</span>\
                            </div>\
                            <div class="comment-info">\
                              <p>' + content + '</p>\
                            </div>\
                            <div class="text-input hidden">\
                              <div class="input-w">\
                                <textarea></textarea>\
                              </div>\
                              <div class="input-action">\
                                <button class="button">回复</button>\
                                <span class="text-count">还剩<strong>124</strong>字</span>\
                              </div>\
                            </div>\
                          </div>\
                        </div>';
                var toInsert = $(".comment-list").find(".comment-item");
                if(toInsert.length > 0){
                  $(commentHtml).prependTo(".comment-item:first");
                }
                else{
                  $(".comment-list").prepend(commentHtml);
                }
              }
            }
          );
        });

        $(".reply").click(function(){
          $(this).parent().siblings(".text-input").show();
        });*/
      // });

    });
  });
});