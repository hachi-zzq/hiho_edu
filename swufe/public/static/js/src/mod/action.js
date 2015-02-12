(function ($, win) {
    var jUtil = JLib.Util;
    var jForm = JLib.form;
    var jDialog = JLib.dialog;
    var jUpload = JLib.upload;
    var jTools = JLib.tools;
    var pageName = JLib.config.pageName;

    $(function () {
        /*
         * 整站
         */
        (function () {
            /*描述文字在input-txt上面，当input获取焦点时文字隐藏 组件*/
            jForm.labelInputTxt({"target": $('.label-input-txt')});
        })();

        /*
         * 登录 / 注册 / 验证email / 修改email / 修改密码 / 重置密码
         */
        (function () {
            if (
                pageName == 'signIn'
                    || pageName == 'signUp'
                    || pageName == 'validateEmail'
                    || pageName == 'changeEmail'
                    || pageName == 'changePassword'
                    || pageName == 'resetPassword') {

                //表单
                var signForm = $('#sign_form');
                var btnSign = signForm.find('#btn_sign');

                signForm.validationEngine("attach", {
                    "promptPosition": "centerRight",
                    "addFailureCssClassToField": 'validation-error',
                    "maxErrorsPerField": 1,
                    "ajaxFormValidation": true,
                    "ajaxFormValidationMethod": 'post',
                    "onBeforeAjaxFormValidation": function (form, options) {
                        btnSign.validationEngine('showPrompt', 'loading', 'load');
                    },
                    "onAjaxFormComplete": function (status, form, json, options) {
                        if (status) {
                            if (json.status == 0) {
                                //登录页提交成功后跳转到指定的链接
                                if (pageName == 'signIn') {
                                    location.href = json.url;
                                }
                                //注册成功提示验证邮箱
                                else if (pageName == 'signUp') {
                                    btnSign.validationEngine('showPrompt', json.message, 'error');
                                }
                                //修改密码 / 重置密码 成功，两秒后自动跳转到指定的链接
                                else if (pageName == 'changePassword' || pageName == 'resetPassword') {
                                    btnSign.validationEngine('showPrompt', json.message, 'pass');

                                    setTimeout(function () {
                                        location.href = json.url;
                                    }, 3000);
                                }
                                else {
                                    btnSign.validationEngine('showPrompt', json.message, 'pass');
                                }
                            } else {
                                btnSign.validationEngine('showPrompt', json.message, 'error');
                            }
                        } else {
                            btnSign.validationEngine('showPrompt', 'System error', 'error');
                        }
                    }
                });
            }
        })();

        /*
         * 播放页 by sewise.player (playSewise 为 HIHO sewise播放页；bbcPlay 为 bbc demo页)
         */
        (function () {
            if (pageName == 'playSewise' || pageName == 'bbcPlay') {
                //搜索高亮
                var subtitleSearchForm = $('#subtitle_search');
                var subtitleInput = subtitleSearchForm.find('.input');
                var originSubtitleCol = $('#origin_subtitle_col');
                var subtitleCont = originSubtitleCol.find('.subtitle-cont');
                var subtitleBox = originSubtitleCol.find('.subtitle-box');
                var subtitleBoxHeight = 0;
                var searchNum = 0;
                var numElem = $('<span class="num"></span>').appendTo(subtitleSearchForm);
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
                        jTools.clearWinSelection();

                        if (window.find) {
                            //console.log('window.find');
                            while (true) {
                                var check = window.find(keyword);

                                if (!check)
                                    break;

                                var sel = window.getSelection();
                                console.log(sel.rangeCount);
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
                        $(win).scrollTop(0);

                        jTools.clearWinSelection();

                        if (from == 'searchInput') {
                            if (searchNum == 0) {
                                numElem.text('No matches');
                            } else if (searchNum == 1) {
                                numElem.text('1 match');
                            } else {
                                numElem.text(searchNum + ' matches');
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

                var originPlayer = new JLib.player();
                var $originPlayer = $('#origin_player');

                var lineElemArray = null;

                originPlayer.init({
                    "videoElem": $originPlayer,
                    "subtitleElem": originSubtitleCol,
                    "srtJson": JLib.config.playerJSON,
                    //字幕加载完后回调
                    "subtitleCallbackFunc": function (data) {
                        lineElemArray = data;

                        //根据?k=helloworld
                        var matchSearch = (/(?:\?|&)k\=([^&]+)/g).exec(document.location.search);
                        if (matchSearch != null) {
                            setHighlightWord(decodeURIComponent(matchSearch[1].replace(/\+/g, ' ')), 'searchPage');
                        }
                    }
                });

                var originJiathisConfig = jiathis_config;
                var shareTitle = jiathis_config.title;
                var shareFrom = 'sewise';
                jiathis_config.title = '#sewise#';

                if (pageName == 'bbcPlay') {
                    jiathis_config.title = ' ';
                    shareFrom = 'bbc';
                }

                //分享
                var videoShare = $('.video-share');
                videoShare.on('mouseenter', '.jiathis_style_32x32 .jiathis_txt', function (e) {
                    jiathis_config = originJiathisConfig;
                    jTools.setShareSummary(e, shareTitle);
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

                        jTools.clearWinSelection();

                        originPlayer.pause();
                        var currentLine = originPlayer.getCurrentLineIndex();

                        var clipShare = new JLib.clipCtl();
                        clipShare.init({
                            "container": $('#origin_video_subtitle'),
                            "lineHeight": 24,
                            "currentLine": currentLine,
                            "share": {"title": shareTitle, "from": shareFrom}
                        });
                        clipShare.setSubtitleLine(lineElemArray, originPlayer);
                    });
                }

                //复制页面分享地址
                jTools.copyToClip({
                    btn: $('#copy_origin_link'),
                    target: $('#origin_link'),
                    type: 'text',
                    afterCopy: function () {
                        alert('Copy succeed.');
                    }
                });

                if (pageName == 'playSewise') {
                    //收藏
                    var favActiveClass = 'ico-fav-active';//已收藏
                    $('#ico_fav').click(function (e) {
                        e.preventDefault();
                        var _self = $(this);

                        $.post('/favourite', {"video_id": JLib.config.videoId, "fragment_id": JLib.config.fragmentId},
                            function (data) {
                                data = jUtil.stringToJSON(data);

                                if (data.status == 1) {
                                    if (_self.hasClass(favActiveClass)) {
                                        _self.removeClass(favActiveClass);
                                    } else {
                                        _self.addClass(favActiveClass);
                                    }
                                } else {
                                    alert(data.message);
                                }
                            }).fail(function () {
                                alert('Sorry! System busy. Please retry later.');
                            });
                    });

                    //评论
                    var commentList = $('#comment_list');
                    var commentMod = new jTools.commentMod({
                        "commentBox": $('#comment_col .comment-box'),
                        "listCont": commentList,
                        "player": originPlayer,
                        "videoId": JLib.config.videoId,
                        "fragmentId": JLib.config.fragmentId
                    });
                    commentMod.init();
                }
            }
        })();

        /*
         * 旧版播放页 by flowplayer
         */
        (function () {
            if (pageName == 'play') {
                //搜索高亮
                var subtitleSearchForm = $('#subtitle_search');
                var subtitleInput = subtitleSearchForm.find('.input');
                var originSubtitleCol = $('#origin_subtitle_col');
                var subtitleCont = originSubtitleCol.find('.subtitle-cont');
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
                        jTools.clearWinSelection();

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
                        $(win).scrollTop(0);

                        jTools.clearWinSelection();

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

                var originPlayer = win.op = new JLib.player();
                var $originPlayer = $('#origin_player');

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
                    }/*,
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
                    jTools.setShareSummary(e, shareTitle);
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

                        jTools.clearWinSelection();

                        originPlayer.pause();
                        var currentLine = originPlayer.getCurrentLineIndex();

                        var clipShare = new JLib.clipCtl();
                        clipShare.init({
                            "container": $('#origin_video_subtitle'),
                            "lineHeight": 24,
                            "currentLine": currentLine,
                            "share": {"title": shareTitle}
                        });
                        clipShare.setSubtitleLine(lineElemArray, originPlayer);
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
                jTools.copyToClip({
                    btn: $('#copy_origin_link'),
                    target: $('#origin_link'),
                    type: 'text',
                    afterCopy: function () {
                        alert('Copy succeed.');
                    }
                });

                //收藏
                var favActiveClass = 'ico-fav-active';//已收藏
                $('#ico_fav').click(function (e) {
                    e.preventDefault();
                    var _self = $(this);

                    $.post('/favourite', {"video_id": JLib.config.videoId, "fragment_id": JLib.config.fragmentId},
                        function (data) {
                            data = jUtil.stringToJSON(data);

                            if (data.status == 1) {
                                if (_self.hasClass(favActiveClass)) {
                                    _self.removeClass(favActiveClass);
                                } else {
                                    _self.addClass(favActiveClass);
                                }
                            } else {
                                alert(data.message);
                            }
                        }).fail(function () {
                            alert('Sorry! System busy. Please retry later.');
                        });
                });

                //评论
                var commentList = $('#comment_list');
                var commentMod = new jTools.commentMod({
                    "commentBox": $('#comment_col .comment-box'),
                    "listCont": commentList,
                    "player": originPlayer,
                    "videoId": JLib.config.videoId,
                    "fragmentId": JLib.config.fragmentId
                });
                commentMod.init();
            }
        })();

        /*
         * 视频Wall
         */
        /*(function(){
         if (pageName == 'wall'){
         var wallListCont = $('#wall_list_cont');
         var wallListContOffsetTop = wallListCont.offset().top;
         var $loading = wallListCont.siblings('.loading');
         //加载中
         var loading = true;
         //已经加载所有数据
         var isLoadAll = false;
         //已经加载过数据
         var hasLoaded = false;
         var page = 1;
         //jiathis
         var jiaThisJS = $('<script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"><\/script>');
         //列
         var wallItem = wallListCont.find('.item ul');
         var wallItemLen = wallItem.length;
         //这次要插入的列
         var toItemNum = 0;
         var $win = $(win);

         //获取列表
         function getWall(){
         jiaThisJS.remove();
         var requestData = {};

         if (page != 1){
         requestData.page = page;
         }
         page++;

         $.get('/wall/getdata', requestData,
         function(data){
         if (data){
         loading = false;
         hasLoaded = true;
         $loading.addClass('hidden');

         $(data).each(function(i){
         $(this).hide().appendTo(wallItem.eq(toItemNum)).fadeIn();
         toItemNum++;

         if (toItemNum >= wallItemLen){
         toItemNum = 0;
         }
         });

         //分享
         jiaThisJS.appendTo($('body'));
         }
         //为空
         else if (data == "" && !hasLoaded) {
         isLoadAll = true;
         $loading.addClass('hidden');
         jTools.emptyTips({
         "text": "Result is empty",
         "target": wallListCont
         });
         } else {
         isLoadAll = true;
         $loading.addClass('hidden');
         }


         }).fail(function(){
         loading = false;
         });
         }
         getWall();

         $win.scroll(function(){
         if (!isLoadAll && !loading && wallListContOffsetTop + wallListCont.height() - $win.height() * 1.5 <  $win.scrollTop()){
         loading = true;
         $loading.removeClass('hidden');

         getWall();
         }
         });

         //分享
         wallListCont.on('mouseenter', '.jiathis_style_32x32 .jiathis_txt', function(e){
         var _self = $(this);
         var li = _self.parents('li').eq(0);
         var link = li.find('.info .name a');
         var rel = li.data('rel');

         jiathis_config.url = link.attr('href');
         jiathis_config.title = '#hiho clip#';
         JLib.tools.setShareSummary(e, link.text());
         });

         //评论浮层
         var winWallTpl = [
         '<div class="win-wall-cont">',
         '<div class="video-comment clear">',
         '<div class="video-col">',
         '<div id="video_player" class="flowplayer video-cont" data-ratio="0.75">',
         '<video>',
         '<source id="video_flv" type="video/flv" src="">',
         '<source id="video_m3u8" type="application/x-mpegurl" src=""></source>',
         '<track id="video_srt" src=""></track>',
         '</video>',
         '</div>',
         '<div class="video-info">',
         '<p class="title" id="video_title"></p>',
         '<p class="desc" id="video_desc"></p>',
         '</div>',
         '</div>',
         '<div class="comment-col" id="comment_col">',
         '<div class="comment-cont">',
         '<div class="share-cont" id="share_cont">',
         '<div class="share-intro" id="share_intro">',
         '<div class="user-pic"><img src="" alt=""/></div>',
         '<div class="share-info">',
         '<p class="explain"><em></em> <span class="time"></span></p>',
         '<div class="count">',
         '<span class="ico-s ico-browse"></span><span class="ico-s ico-comment"></span><a href="#"  class="ico-s ico-like ico-like-light"></a>',
         '</div>',

         '<div class="social">',
         '<div class="jiathis_style_32x32">',
         '<a class="jiathis_button_fb" title="Facebook"></a>',
         '<a class="jiathis_button_twitter" title="Twitter"></a>',
         '<a class="jiathis_button_googleplus" title="Google+"></a>',
         '<a class="jiathis_button_tsina" title="Sina weibo"></a>',
         '<a class="jiathis_button_weixin" title="Weixin"></a>',
         '<a class="jiathis_button_pinterest" title="Pinterest"></a>',
         '</div>',
         '</div>',
         '</div>',
         '</div>',
         '</div>',
         '<div class="comment-box">',
         '<form action="" name="commentForm" id="comment_form">',
         '<input type="text" class="input-txt" /><input type="submit" class="btn-s1" value="Comment" />',
         '</form>',
         '<span class="comment-num"></span>',
         '</div>',
         '<div class="comment-list" id="comment_list">',
         '<div class="list">',
         '<ul></ul>',
         '</div>',
         '<a class="more-comment" id="more_comment" href="#">more</a>',
         '</div>',
         '</div>',
         '</div>',
         '</div>',
         '</div>'
         ].join('');

         wallListCont.on('click', '.ico-comment', function(e){
         e.preventDefault();
         console.log('ico-comment');
         var _self = $(this);
         var $item = _self.parents('li').eq(0);

         $.post("/static/source_demo/json/wall.json", {},
         function(data){
         //data = $.parseJSON(data);

         if (data.status == 1){
         var win = jDialog.winPop({
         "html": winWallTpl,
         "className": 'win-wall',
         "hidden": 'hidden',
         "closeMode": 'destroy'
         });
         win.show();

         //视频
         var $player = win.box.find('#video_player');
         win.box.find('#video_flv').attr('src', data.flv);
         win.box.find('#video_m3u8').attr('src', data.m3u8);
         win.box.find('#video_srt').attr('src', data.srt);
         $player.flowplayer({
         "swf": "/static/js/lib/flowplayer/5.4.6/flowplayer.swf"
         });

         //用户信息、分享
         var link = $item.find('.info .name a');
         var rel = $item.data('rel');
         var desc = $.trim($item.find('.video-info .desc').text().replace(/[\r\n]/g, ""));
         var userPic = $item.find('.user-pic img');

         win.box.find('#video_title').text(link.text());
         win.box.find('#video_desc').text(desc);

         var shareIntro = win.box.find('#share_intro');
         shareIntro.find('.user-pic img').attr({
         'src': userPic.attr('src'),
         'alt': userPic.attr('alt')
         });
         shareIntro.find('.explain em').text($item.find('.explain').text());
         shareIntro.find('.explain .time').text($item.find('.share-info .time').text());

         shareIntro.find('.ico-browse').text($item.find('.ico-browse').text());
         shareIntro.find('.ico-comment').text($item.find('.ico-comment').text());
         shareIntro.find('.ico-like').text($item.find('.ico-like').text());

         jiathis_config.url = link.attr('href');
         jiathis_config.title = link.text();
         jiathis_config.summary = jTools.text.shareSummaryEn;
         jiaThisJS.remove();
         jiaThisJS.appendTo($('body'));

         //评论
         var commentMod = new jTools.commentMod({
         "commentBox": win.box.find('.comment-box'),
         "listCont": win.box.find('#comment_list'),
         "player": $player.flowplayer()
         });
         commentMod.init();

         } else {
         alert(data.message);
         }
         }).fail(function(){
         alert('Sorry! System busy. Please retry later.');
         });
         });
         }
         })();*/

        /*
         * 搜索页
         */
        (function () {
            if (pageName == 'searchIndex') {
                //搜索建议
                // jTools.searchSuggest();
            }
        })();

        /*
         * 搜索列表页
         */
        (function () {
            if (pageName == 'newSearchResult' || pageName == 'subscribed') {
                //搜索建议
                // jTools.searchSuggest();

                var searchForm = $('#site_search');
                var searchInput = searchForm.find('input[name="keywords"]');
                var btnAdd = $('#add_keyword');

                //将搜索词添加到订阅
                btnAdd.click(function () {
                    var word = $.trim(searchInput.val());

                    if (word !== '') {
                        $.get('/static/html_demo/default/test.json', {"word": word},function (data) {
                            data = jUtil.stringToJSON(data);
                            if (data.errorType == 0) {
                                alert('Subscription succeed.');
                            } else {
                                alert(data.message);
                            }
                        }).fail(function () {
                            alert('Sorry! System busy. Please retry later.');
                        });
                    }
                });

                var searchListCont = $('#search_list_cont');
                //搜索列表每一项
                var items = null;
                //列表长度
                var len = 0;
                var keyword = searchInput.val();
                //当前加载的项
                var index = 0;

                //延时加载列表中每项的搜索结果
                setTimeout(function () {
                    items = searchListCont.find('.item');
                    len = items.length;

                    // getSubtitle();
                    setAllFragmentsToTimeLine();
                }, 500);

                function setAllFragmentsToTimeLine() {
                    // 遍历所有 VIDEOS 结果
                    items.each(function (i) {
                        // 获得视频时长\GUID 和 itemTemp 对象
                        setTimeLine($(this), $(this).data('length'));
                    });
                }

                // 此前浓哥写的一个异步递归方法, 暂时弃用
                function getSubtitle() {
                    var itemTemp = items.eq(index);

                    //$.get("/static/source_demo/json/search_item.json", {"videoGuid": itemTemp.data('rel').id, "keywords": keyword},
                    $.get("/search/getSingleSubtitleFt", {"videoGuid": itemTemp.data('rel').id, "keywords": keyword},
                        function (data) {
                            data = jUtil.stringToJSON(data);
                            if (data.errorType == 0) {
                                itemTemp.find('.info-cont').append(data.dom);

                                setTimeLine(itemTemp, data.videoTime);
                            }

                            itemTemp.find('.loading-s3').remove();

                            index++;
                            if (index < len) {
                                getSubtitle();
                            }
                        });
                }

                // 设置 SPAN 到时间线
                function setTimeLine(item, videoTime) {
                    var subtitleResult = item.find('.subtitle-result');
                    var subtitle = subtitleResult.find('.subtitle');
                    var times = subtitleResult.find('.time');
                    var timeLine = item.find('.time-line');
                    var operate = item.find('.operate');
                    var totalTime = videoTime;

                    // 根据内容多少设置load more、collapsed
                    if (subtitle.height() > subtitleResult.height()) {
                        operate.removeClass('hidden');
                    } else {
                        operate.remove();
                    }
                    // 设置关键词在时间线上的位置
                    times.each(function () {
                        var _self = $(this);
                        var t = jTools.convertTime(_self.text()) / totalTime * 100;

                        if (t >= 0 && t <= 100) {
                            $('<span></span>').css('left', t + '%').appendTo(timeLine);
                        }
                    });

                    timeLine.removeClass('hidden');
                }

                //点击load more、collapsed事件
                searchListCont.on('click', '.list .operate', function () {
                    var _self = $(this);
                    var item = _self.parents('.items').eq(0);
                    if (item.hasClass('subtitle-all')) {
                        item.removeClass('subtitle-all');
                    } else {
                        item.addClass('subtitle-all');
                    }
                });

                //点击关键词时跳转
                if (searchListCont[0]) {
                    searchListCont.on('click', '.keyword', function () {
                        var _self = $(this);

                        var parent = _self.parent();
                        var link = parent.attr('link');
                        var st = parseFloat(parent.attr('data'));
                        var nextLine = parent.next();
                        var et = st + 5;

                        if (nextLine[0]) {
                            et = parseFloat(nextLine.attr('data'));
                        }

                        link = link + '?st=' + st + '&et=' + et;

                        win.open(link);
                    });
                }

                /*
                 * 订阅列表页
                 */
                (function () {
                    if (pageName == 'subscribed') {
                        //修改订阅
                        var setting = {};
                        setting.cont = $('#subscribed_setting');
                        setting.num = setting.cont.find('.status span');

                        setting.cont.on('click', 'li', function () {
                            var _self = $(this);

                            if (_self.hasClass('selected')) {
                                _self.removeClass('selected');
                            } else {
                                _self.addClass('selected');
                            }
                        });

                        setting.cont.on('click', '.ico-close-s1', function (e) {
                            e.stopPropagation();

                            var _self = $(this);
                            var item = _self.parents('li').eq(0);
                            item.remove();
                        });

                        setting.cont.on('click', '#btn_subscribed', function (e) {
                            var _self = $(this);

                            //订阅的词
                            var item = setting.cont.find('li');
                            //要显示在订阅列表中的词
                            var selected = item.filter('.selected');
                            setting.num.text(item.length);
                            console.log(selected);
                        });
                    }
                })();

            }
        })();

        /*
         * 订阅页
         */
        (function () {
            if (pageName == 'subscription') {
                //搜索建议
                // jTools.searchSuggest();

                var $win = $(win);
                var subscription = {};
                subscription.cont = $('#subscription_cont');
                subscription.header = subscription.cont.find('.hd');
                subscription.header.height = subscription.header.height();
                subscription.headerBox = subscription.header.find('.hd-box');
                //触发固定在顶部的位置
                subscription.changePos = subscription.header.find('.title-s2').offset().top;
                //已经订阅数
                subscription.subcribedNum = JLib.config.subcribedNum;

                //根据滚动条设置订阅词固定在顶部
                function setHeader() {
                    if ($win.scrollTop() > (subscription.changePos - 100)) {
                        subscription.cont.addClass('fixed');
                        subscription.header.css('height', subscription.header.height);
                    } else {
                        subscription.cont.removeClass('fixed');
                        subscription.header.css('height', 'auto');
                    }
                }

                setHeader();
                $win.scroll(function () {
                    setHeader();
                });

                //推荐列表、订阅、取消订阅、订阅全部、换一组推荐
                subscription.list = subscription.cont.find('.list ul');

                //订阅按钮
                subscription.list.on('click', '.btn-subscribe', function (e) {
                    e.preventDefault();

                    var _self = $(this);
                    subscribe(_self, 0, 1);
                });

                //订阅(最多订阅20条) elem : 订阅按钮列表, index : 当前订阅按钮索引, total : 所有订阅按钮数
                function subscribe(elem, index, total) {
                    if (subscription.subcribedNum < 20) {
                        var elemTemp = elem.eq(index);
                        var item = elemTemp.parents('li').eq(0);
                        var rel = item.data('rel');

                        $.get("/static/source_demo/json/subscription.json", {"id": rel.id},
                            function (data) {
                                data = jUtil.stringToJSON(data);
                                subscription.subcribedNum = data.subcribedNum;

                                if (data.status == 1) {
                                    $('<a class="btn-s3 btn-subscribed" href="#" title="Unsubscribe">Subscribed</a>').insertAfter(elemTemp);
                                    elemTemp.remove();
                                } else {
                                    alert(data.message);
                                }

                                index++;
                                if (index < total) {
                                    subscribe(elem, index, total)
                                }
                            }).fail(function () {
                                //alert('Sorry! System busy. Please retry later.');
                            });
                    } else {
                        alert('Subscriptions has reached the upper limit.');
                    }
                }

                //取消订阅按钮
                subscription.list.on('click', '.btn-subscribed', function (e) {
                    e.preventDefault();

                    var _self = $(this);
                    var item = _self.parents('li').eq(0);
                    var rel = item.data('rel');

                    $('<a class="btn-s4 btn-subscribe" href="#" title="Subscribed">Subscribe</a>').insertAfter(_self);
                    _self.remove();
                });

                //订阅全部、换一组推荐
                subscription.subscribAll = subscription.cont.find('#subscribe_all');
                subscription.btnChange = subscription.cont.find('#btn_change');
                subscription.btnChange.changing = false;

                //全部订阅
                subscription.subscribAll.click(function (e) {
                    e.preventDefault();

                    //推荐列表中所有订阅按钮
                    var btn = subscription.list.find('.btn-subscribe');
                    subscribe(btn, 0, btn.length);
                });

                //换一组推荐
                subscription.btnChange.click(function (e) {
                    e.preventDefault();

                    if (!subscription.btnChange.changing) {
                        subscription.btnChange.changing = true;

                        subscription.list.empty();
                        var loading = $('<div class="loading-s3"></div>').appendTo(subscription.cont);

                        $.get('/static/source_demo/json/change_subscription.json', {},
                            function (data) {
                                data = jUtil.stringToJSON(data);

                                loading.remove();
                                if (data.status == 1) {
                                    subscription.list.append(data.dom);
                                } else {
                                    alert(data.message);
                                }

                                subscription.btnChange.changing = false;
                            }).fail(function () {
                                loading.remove();
                                subscription.btnChange.changing = false;
                                //alert('Sorry! System busy. Please retry later.');
                            });
                    }

                });
            }
        })();

        /*
         * 评论
         */
        /*(function(){
         if (pageName == 'personalComment'){
         var commentCont = $('#personal_comment_cont');

         commentCont.on('click', '.delete', function(e){
         e.preventDefault();

         var confirm = window.confirm("Sure want to delete this comment?");

         if (confirm){
         var _self = $(this);
         var $item = _self.parents('tr').eq(0);
         var rel = $item.data('rel');

         $.post("/static/html_demo/default/test.json", {"id": rel.id},
         function(data){
         data = jUtil.stringToJSON(data);

         if (data.status == 1){
         $item.remove();
         } else {
         alert(data.message);
         }
         }).fail(function(){
         alert('Sorry! System busy. Please retry later.');
         });
         }
         });

         //回复
         var replyHtml = [
         '<div class="form win-form">',
         '<form action="" name="replyForm" id="reply_form">',
         '<div class="row">',
         '<label for="reply_cont" class="row-label">Cotent：</label>',
         '<div class="row-info">',
         '<textarea class="textarea w300" name="replyCont" id="reply_cont" cols="30" rows="10"></textarea>',
         '</div>',
         '</div>',
         '<div class="row action">',
         '<input type="submit" id="btn_submit_reply" class="btn-s1" value="Submit" data-prompt-position="bottomLeft" />',
         '</div>',
         '</form>',
         '<span class="reply-num">200</span>',
         '</div>'
         ].join('');

         commentCont.on('click', '.reply', function(e){
         e.preventDefault();

         var _self = $(this);
         var $item = _self.parents('tr').eq(0);
         var rel = $item.data('rel');

         var replyWin = jDialog.winPop({
         "html": replyHtml,
         "className": 'win-reply',
         "hidden": 'hidden',
         "closeMode": 'destroy'
         });
         replyWin.show();

         var replyMax = 200;
         var replyForm = replyWin.box.find('#reply_form');
         var textarea = replyWin.box.find('.textarea');
         var num = replyWin.box.find('.reply-num');
         num.text(replyMax);

         textarea.keyup(function(e){
         var txt = $.trim(textarea.val());

         var n = replyMax - txt.length;
         num.text(n);
         if (n < 0){
         num.addClass('warn-color');
         } else {
         num.removeClass('warn-color');
         }
         });

         replyForm.submit(function(e){
         e.preventDefault();

         var txt = $.trim(textarea.val());

         if (txt == ''){
         textarea.focus();
         } else if(txt.length > replyMax) {
         alert('Up to ' + replyMax + ' characters can only reply.');
         } else {
         $.post("/comment", {"play_time": 0, "user_id": JLib.config.userId, "video_id": cfg.videoId, "fragment_id": cfg.fragmentId, "content": txt, "reply_id": 0},
         function(data){
         data = jUtil.stringToJSON(data);

         if (data.status == 1){
         alert('Reply succeed.');
         replyWin.closeElem.click();
         } else {
         alert(data.message);
         }

         }).fail(function(){
         alert('Sorry! System busy. Please retry later.');
         });
         }
         });
         });
         }
         })();*/

        /*
         * 个人中心 基本资料页
         */
        (function () {
            if (pageName == 'profileInfo') {
                //修改用户头像
                jTools.setUserPic();

                /*
                 * 昵称ajax验证 ajax[ajaxNickname]]，返回/static/source_demo/json/check_nickname.json
                 * [fileId, true, "message"]
                 * fileId 为 该元素的id，true 或 false 为该元素验证是否通过，message为验证信息
                 */

                //昵称表单
                var profileInfoForm = $('#profile_info_form');
                var personalInfoFormAction = profileInfoForm.attr('action');
                var btnSubmitPersonal = profileInfoForm.find('#btn_profile_info');

                profileInfoForm.validationEngine("attach", {
                    "promptPosition": "centerRight",
                    "addFailureCssClassToField": 'validation-error',
                    "maxErrorsPerField": 1,
                    "onValidationComplete": function (form, valid) {
                        if (valid) {
                            btnSubmitPersonal.validationEngine('showPrompt', 'loading', 'load');

                            $.post(personalInfoFormAction, form.serialize(),
                                function (data) {
                                    data = jUtil.stringToJSON(data);

                                    if (data.status == 1) {
                                        btnSubmitPersonal.validationEngine('showPrompt', data.message, 'pass');
                                    } else {
                                        btnSubmitPersonal.validationEngine('showPrompt', data.message, 'error');
                                    }
                                }).fail(function () {
                                    btnSubmitPersonal.validationEngine('showPrompt', 'Sorry! System busy. Please retry later.', 'error');
                                });
                        }
                    }
                });
            }
        })();

        /*
         * 个人中心 其它资料页
         */
        (function () {
            if (pageName == 'profileExtInfo') {
                //修改用户头像
                jTools.setUserPic();

                //日历
                $("#birthday").datepicker({
                    dateFormat: 'yy-mm-dd',
                    maxDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "c-100:c"
                });

                //表单
                var profileInfoForm = $('#profile_info_form');
                var btnSubmitPersonal = profileInfoForm.find('#btn_profile_info');

                profileInfoForm.validationEngine("attach", {
                    "promptPosition": "centerRight",
                    "addFailureCssClassToField": 'validation-error',
                    "maxErrorsPerField": 1,
                    "ajaxFormValidation": true,
                    "ajaxFormValidationMethod": 'post',
                    "onBeforeAjaxFormValidation": function (form, options) {
                        btnSubmitPersonal.validationEngine('showPrompt', 'loading', 'load');
                    },
                    "onAjaxFormComplete": function (status, form, json, options) {
                        if (status) {
                            if (json.status == 0) {
                                btnSubmitPersonal.validationEngine('showPrompt', 'Succeed', 'pass');
                            } else {
                                btnSubmitPersonal.validationEngine('showPrompt', json.message, 'error');
                            }
                        } else {
                            btnSubmitPersonal.validationEngine('showPrompt', 'System error', 'error');
                        }
                    }
                });
            }
        })();

        /*
         * 个人中心 我的评论 || 收到的评论
         */
        (function () {
            if (pageName == 'profileComments' || pageName == 'profileReceivedComments') {
                //修改用户头像
                jTools.setUserPic();
            }
        })();

    });
})(jQuery, window);

