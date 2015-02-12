require(['config'], function(){
  require(['infrastructure'], function(){
    require(['blockui', 'sewisecontrol'], function(blockUI, sewiseControl){

      var $playerHolder = $('#playerHolder'),
        listInfo = $playerHolder.data(),
        $noteList = $('#noteList'),
        $noteItem = $noteList.find('li'),
        playerLoaded,
        noteIndex = 0,
        $noteIndex = $('#noteIndex'),
        $currentTitle = $('#currentTitle');

      // shrink
      var $container = $('.player-container');
      $('.shrink-btn a').click(function(){
        $container.toggleClass('shrinked');
        return false;
      });

      // get video addresses
      $noteItem.each(function(index){
        var $note = $(this);
        $.ajax({
          url: '/note/getFragmentPlayUrl/' + $note.data('playid'),
          dataType: 'json'
        }).done(function(res){
          if(res.msgCode === 0){
            $note.data('source', res.data.FLV.src);
            if(!index){
              $note.addClass('active');
              loadPlayer(res.data.FLV.src, $note.data('title'));
              $currentTitle.text($note.data('title'));
            }
          }
        });
      });

      // click to play
      $noteList.on('click', '.playLink', function(){
        var $note = $(this).parents('.note-item'),
          source = $note.data('source');
        if(playerLoaded && !$note.hasClass('active') && source){
          $noteItem.removeClass('active');
          $note.addClass('active');
          SewisePlayer.toPlay(source, $note.data('title'), 0, true);
          noteIndex = $noteItem.index($note);
          $noteIndex.text(noteIndex + 1);
          $currentTitle.text($note.data('title'));
        }
        return false;
      });

      function loadPlayer(source, title){
        sewiseControl.load('playerHolder', function(){
          SewisePlayer.setup({
            server: 'vod',
            videourl: source,
            title: title,
            skin: 'vodWhite',
            logo: '#',
            playername: ' ',
            copyright: ' '
          });
          playerLoaded = true;
        });
      }

      window.onStop = function(){
        if(noteIndex + 1 < $noteItem.length){
          // play next note
          $noteItem.eq(++noteIndex).find('.playLink').eq(0).click();
        }
      }


      /* 视频剪辑、笔记 */
      var $addToNoteDialog = $('#addToNoteDialog'),
        $createNoteBookButton = $addToNoteDialog.find('#createNoteBookButton'),
        $createNewPlaylist = $('#createNewPlaylist'),
        selectedNoteId;

      function addShow(){
        var len,
          i;
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

      $('.addNoteToNote').click(function (){
        selectedNoteId = $(this).parents('.note-item').data('playid');
        addShow();
        return false;
      });
      
      $('#cloaseAddToNoteDialog').click(function(){
        $addToNoteDialog.hide();
        return false;
      });

      $('#addToNoteButton').click(function addInMyPlaylist(){
        $.ajax({
          'url':'/my/note/addInMyPlaylist',
          'type':'POST',
          'data':'playlist_id='+$('#playlist_id').val()+'&title='+$('#title').val()+'&description='+$('#description').val()+'&playid='+selectedNoteId,
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
        });
        return false;
      });

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


      // 收藏整个列表
      $('#addToFavPlaylists').click(function(){
        $.ajax({
          url: '/favorite/addPlaylist',
          data: {play_id: listInfo.playid},
          type: 'POST',
          dataType: 'json'
        }).done(function(res){
          if(res.status === 0){
            $.blockUI({ fadeIn: 700, fadeOut: 700, timeout: 500 ,message:'<p style="color:white">添加成功</p>'});
          }else if(res.status === -3){
            $.blockUI({ fadeIn: 700, fadeOut: 700, timeout: 500 ,message:'<p style="color:white">已收藏过该笔记</p>'});
          }else{
            $.blockUI({ fadeIn: 700, fadeOut: 700, timeout: 500 ,message:'<p style="color:white">添加失败</p>'});
          }
        });
      });


      // 收藏
      var favActiveClass = 'ico-fav-active';//已收藏
      $('.addNoteToFav').click(function (e) {
        e.preventDefault();
        var $this = $(this);

        $.post('/favorite/addFragment', {fragment_playid: $this.parents('.note-item').data('playid')},
          function (data) {

            if (data.msgCode === 0) {
              if ($this.hasClass(favActiveClass)) {
                $this.removeClass(favActiveClass);
              } else {
                $this.addClass(favActiveClass);
              }
            } else if(data.msgCode === -1){
              alert('已经收藏过此视频');
            } else {
              alert(data.message);
            }
          }, 'json').fail(function () {
            alert('添加失败，请稍后再试');
          });
        return false;
      });



    });
  });
});