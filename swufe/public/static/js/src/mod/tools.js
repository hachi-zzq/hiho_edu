/*
 * 小工具集合
 */

var JLib = JLib || {};
JLib.tools = JLib.tools || {};

(function($, win){
    var jUtil = JLib.Util;
    var jDialog = JLib.dialog;
    var jTools = JLib.tools;

    (function(){
        jTools.isLogin = function(){
            if (JLib.config.userId < 0){
                /*jDialog.winAlert({
                    'txt': 'Please sign in first.',
                    'tipsType': 'warn'
                }).show();*/
                alert('Please sign in first.');
                return false;
            }

            return true;
        }
    })();

    (function(){
        /*复制文字到剪刀板*/
        jTools.copyToClip = function(cfg){
            if (cfg.btn != undefined){
                cfg.btn.zclip({
                    path: '/static/js/lib/jquery.zclip/1.1.1/ZeroClipboard.swf',
                    copy: function(){
                        if (cfg.type == 'text'){
                            return cfg.target.text();
                        } else {
                            return cfg.target.val();
                        }
                    },
                    afterCopy: function(){
                        if (cfg.afterCopy){
                            cfg.afterCopy();
                        } else {
                            jDialog.winAlert({
                                "txt": 'Copy succeed.'
                            }).show();
                        }
                    }
                });
            }

        };
    })();

    /*
	 * 焦点图
	 * cfg {"focusEl", "speed", "moveSpeed", "showItemNum"}
	 * focusEl 焦点图元素
	 * speed 每次移动的时间间隔，如果没设置默认为2000ms
	 * moveSpeed 移动的开始到结束的时间，如果没设置默认为300ms
	 * showItemNum 一次显示的数量，如果没设置默认为1
	 */
	(function(){
		jTools.focusPic = function(cfg){
			this.focusEl = cfg.focusEl;
			this.picCont = this.focusEl.find('.pic');
			this.picContUl = this.picCont.find('.focus-ul');
			this.picContLi = this.picContUl.find('.focus-li');

			this.width = this.picContLi.outerWidth(true);
			this.speed = cfg.speed === undefined ? 2000 : cfg.speed;
			this.moveSpeed = cfg.moveSpeed === undefined ? 300 : cfg.moveSpeed;
			this.picNum = this.picContLi.length;
			this.current = 0;
            this.showItemNum = cfg.showItemNum == undefined ? 1 : cfg.showItemNum;

			this.l = this.focusEl.find('.l');
			this.r = this.focusEl.find('.r');

			this.numArea = this.focusEl.find('.num');
			this.numItem = this.numArea.find('span');

			this.timeout;
		}

		jTools.focusPic.prototype = {
			constructor : jTools.focusPic,

			move : function (item){
				var _self = this;

				var left = item * this.width;
				_self.picContUl.animate({left: '+=' + left}, 200);

				if (_self.numArea[0]){
					_self.numItem.eq(_self.current).addClass('cur').siblings().removeClass('cur');
				}
			},

			moveL : function (){
				var _self = this;

				_self.current--;

				if (_self.current < 0){
					_self.current = _self.picNum - _self.showItemNum;
					_self.move(-(_self.picNum - _self.showItemNum));
				}
				else {
					_self.move(1);
				}
			},

			moveR : function (){
				var _self = this;

				_self.current++;

				if (_self.current > (_self.picNum - _self.showItemNum)){
					_self.current = 0;
					_self.move(_self.picNum - _self.showItemNum);
				}
				else {
					_self.move(-1);
				}
			},

			auto : function (){
				var _self = this;

				_self.moveR();

				_self.timeout = setTimeout(function(){ _self.auto()}, _self.speed);
			},

			stop : function (){
				var _self = this;

				clearTimeout(_self.timeout);
			},

			numEvent : function (){
				var _self = this;

				_self.numItem.click(function(){
					var i = _self.current;

					_self.current = $(this).index();
					_self.move(i - _self.current);
				});
			},

			lEnent : function (){
				var _self = this;

				_self.l.click(function(){
					_self.moveL();
				});
			},

			rEnent : function (){
				var _self = this;

				_self.r.click(function(){
					_self.moveR();
				});
			},

			init : function (){
				var _self = this;

                //如果列表总数大于一次能显示的数目
                if (_self.picNum > _self.showItemNum){
                    _self.picContUl.width(_self.picNum * _self.width);

                    if(_self.l[0]){
                        _self.lEnent();
                    }
                    if(_self.r[0]){
                        _self.rEnent();
                    }

                    if (_self.numArea[0]){
                        _self.numEvent();
                    }

                    var timeoutGo;
                    var go = function(){
                        timeoutGo = setTimeout(function(){ _self.auto()}, _self.speed);
                    };

                    _self.focusEl.mouseover(function(){
                        _self.stop();
                        clearTimeout(timeoutGo);
                    }).mouseleave(function(){
                        go();
                    });

                    go();
                } else {
                    _self.l.remove();
                    _self.r.remove();
                }

			}
		};
	})();

    //搜索建议
    (function(){
        jTools.searchSuggest = function(){
            var searchForm = $('#site_search');
            var searchInput = searchForm.find('input[name="keywords"]');
            var searchWd = searchForm.find('#search_wd');
            var suggestResult = null;
            searchForm.submit(function(e){
                if ($.trim(searchInput.val()) == ""){
                    searchInput.focus();

                    e.preventDefault();
                }
            });

            searchInput.autocomplete({
                source: function(request, response){
                    var val = $.trim(searchInput.val());

                    if (val != ''){
                        //$.get("/static/source_demo/json/autocomplete.json", {"q": val},
                        $.get("/search/su", {"q": $.trim(searchInput.val())},
                            function(data){
                                data = jUtil.stringToJSON(data);

                                suggestResult = data.result;
                                response(data.result);
                        });
                    }
                },
                select: function(event, ui){
                    //console.log('select', ui);
                    for (var i in suggestResult){
                        if (ui.item.value == suggestResult[i]){
                            searchWd.val(i);
                        }
                    }
                    searchInput.val(ui.item.value);
                    searchForm.submit();
                }
            });
        };
    })();

    //清除选择范围
    (function(){
        jTools.clearWinSelection = function(){
            if (win.getSelection){
                win.getSelection().removeAllRanges();
            } else {
                document.selection.empty();
            }
        };
    })();

    //修改用户头像
    (function(){
        jTools.setUserPic = function(e, title){
            //用户头像
            var userPicForm = $('#user_pic_form');
            var userPicCont = $('#user_pic');
            var userImg = userPicCont.find('img');
            var userPicInputFile = userPicCont.find('input');
            var userPicTips = userPicCont.find('#user_pic_tips');

            //上传头像后端回调函数
            JLib.uploadUserPicCallback = function(data){
                console.log(data);
                data = jUtil.stringToJSON(data);

                if (data.status == 1){
                    userImg.attr('src', data.path);
                }

                userPicTips.text(data.message);
            };

            userPicInputFile.change(function(){
                userPicForm.submit();
                userPicTips.text('Uploading...');
            });

            //删除头像
            /*var deletePhoto = userPicCont.find('#delete_photo');
            deletePhoto.click(function(e){
                e.preventDefault();

                $.get('/my/del_avatar', {},
                    function(data){
                        data = jUtil.stringToJSON(data);

                        if (data.status == 1){
                            userImg.attr('src', userImgDefaultUrl);
                        }

                        userPicTips.text(data.message);
                }).fail(function(){
                    userPicTips.text('Sorry! System busy. Please retry later.');
                });
            });*/
        };
    })();

    //分享方案
    (function(){
        jTools.setShareSummary = function(e, title, from){
            var target = $(e.target);

            jiathis_config.summary =  ' 我在swufe上剪辑了一段“' + title + '”的视频，快来看看吧。@autotiming';
//            if (target.hasClass('jtico_tsina')){
//                if (from == 'hiho' || from == undefined){
//                    jiathis_config.summary =  ' 我在hiho.com上剪辑了一段“' + title + '”的视频，快来看看吧。@autotiming';
//                } else if (from == 'bbc') {
//                    jiathis_config.summary =  '我剪辑了一段“' + title + '”的视频，快来看看吧。';
//                }
//            } else {
//                if (from == 'hiho' || from == undefined){
//                    jiathis_config.summary =  ' I clip a while "' + title + '" videos on hiho.com, Check it out. @ autotiming';
//                } else if (from == 'bbc') {
//                    jiathis_config.summary =  'I clip a while "' + title + '" videos, Check it out.';
//                }
//
//            }
            console.log('setShareSummary', jiathis_config);
        };
    })();

    //将时间格式01:32:12转换为秒
    (function(){
        jTools.convertTime = function(time){
            var t = time.split(':');
            return t[0] * 3600 + t[1] * 60 + (+t[2]);
        };
    })();

    //将秒转换为00:00:00 或 00:00，cn为true时转换为00小时00分00秒 或 00分00秒
    (function(){
        jTools.convertSecond = function(second, full, cn){
            var h = Math.floor(second / 60 / 60);
            var m = Math.floor((second - h * 60 * 60) / 60);
            var s = second - h * 60 * 60 - m * 60;

            function addZero(n){
                return n < 10 ? '0' + n : n;
            }
            h = addZero(h);
            m = addZero(m);
            s = addZero(s);

            if (cn){
                var time = s + '秒';

                if (m !== '00'){
                    time = m + '分' + time;
                }

                if (h !== '00'){
                    time = h + '小时' + time;
                }

                return time;
            }

            //full为true时返回00:00:00
            if (full){
                return h + ':' + m + ':' + s;
            } else {
                var time = m + ':' + s;

                if (h !== '00'){
                    time = h + ':' + time;
                }

                return time;
            }
        };
    })();

    //结果为空
    (function(){
        jTools.emptyTips = function(cfg){
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
        };
    })();

    //评论 (播放页、Wall页浮层)
    (function(){
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

        jTools.commentMod = function(cfg){
            this.config = cfg;
            this.config.listItems = this.config.listCont.find('.list ul');
            this.config.commentMax = 200;
            this.config.replyMax = 200;
            this.config.hasMoreComment = true;
            this.config.page = 1;
        };

        var _proto = jTools.commentMod.prototype;

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

                if (jTools.isLogin()){
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
                                data = jUtil.stringToJSON(data);

                                if (data.status == 1){
                                    cfg.listCont.find('.tips-cont-empty').remove();

                                    $(data.commentHtml).prependTo(cfg.listItems);
                                    inputTxt.val('');
                                    num.text(cfg.commentMax);
                                } else {
                                    alert(data.message);
                                }

                        }).fail(function(){
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

                if (jTools.isLogin()){
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
                            console.log("playTime", _self.getPlayTime());
                            var time = _self.getCurrentTime();

                            //$.post("/static/source_demo/json/comment.json", {"play_time": time, "user_id": JLib.config.userId, "video_id": cfg.videoId, "fragment_id": cfg.fragmentId, "content": txt, "reply_id": itemRel.id},
                            $.post("/comment", {"play_time": time, "user_id": JLib.config.userId, "video_id": cfg.videoId, "fragment_id": cfg.fragmentId, "content": txt, "reply_id": itemRel.id},
                                function(data){
                                    data = jUtil.stringToJSON(data);

                                    if (data.status == 1){
                                        $(data.commentHtml).prependTo(cfg.listItems);
                                        $(win).scrollTop(cfg.listCont.offset().top - 150);
                                        cfg.replyDom.remove();
                                        cfg.replyDom = null;
                                    } else {
                                        alert(data.message);
                                    }

                            }).fail(function(){
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

                if (jTools.isLogin()){
                    var confirm = window.confirm("Sure want to delete this comment?");

                    if (confirm){
                        var _del = $(this);
                        var $item = _del.parents('li').eq(0);
                        var itemRel = $item.data('rel');

                        //$.post("/static/source_demo/json/comment.json", {"reply_id": itemRel.id},
                        $.post("/delComment", {"reply_id": itemRel.id},
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

                            jTools.emptyTips({
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

    })();
})(jQuery, window);