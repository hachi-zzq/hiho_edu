/*
 * 浮层
 */

var JLib = JLib || {};
JLib.dialog = JLib.dialog || {};

(function($, win){
    var jDialog = JLib.dialog;
    (function(){
        /*
         * 浮层模板
         */
        jDialog.tpl = {
            "win": [
                '<div class="win-pop">',
                    '<div class="win-box">',
                        '<div class="win-hd">',
                            '<h3 id="win_title"></h3>',
                        '</div>',
                        '<div class="win-bd">',
                            '<div class="win-cont" id="win_cont"></div>',
                        '</div>',
                        '<a class="win-close" id="win_close" href="#"></a>',
                    '</div>',
                '</div>'
            ].join(''),

            "confirm": [
                '<div class="tips-cont tips-cont-alert">',
                    '<div class="tips-box">',
                        '<span class="tips-icon-l tips-alert"></span>',
                        '<div class="tips-info">',
                            '<p class="tips-tit"></p>',
                        '</div>',
                        '<div class="action win-action">',
                            '<a class="btn-s1" id="win_sure" href="#" title="Sure">Sure</a><a class="btn-s1-cancel" id="win_cancel" href="#" title="Cancel">Cancel</a>',
                        '</div>',
                    '</div>',
                '</div>'
            ].join(''),

            "alert": [
                '<div class="tips-cont tips-cont-alert">',
                    '<div class="tips-box">',
                        '<span class="tips-icon-l tips-alert"></span>',
                        '<div class="tips-info">',
                            '<p class="tips-tit"></p>',
                        '</div>',
                        '<div class="action win-action">',
                            '<a class="btn-s1" id="win_ok" href="#" title="OK">OK</a>',
                        '</div>',
                    '</div>',
                '</div>'
            ].join(''),

            "mask": '<div class="win-mask"></div>',
            "maskIframe": '<iframe src="" frameborder="0" class="win-mask-iframe"></iframe>'
        };

        /*
         * 默认浮层
         * 传递的参数为json
         * cfg.hidden 设置浮层隐藏的模式，可以设置为hidden（会给浮层加上hidden这个类名）或vhidden（会给浮层加上或vhidden这个类名），如果不设置则为hidden
         * cfg.closeMode 设置点击关闭按钮后是要隐藏浮层还是销毁浮层，如果设置为destroy则是销毁，如果设置为hide或其它情况则是隐藏
         * cfg.className 设置要添加到浮层的类名，格式为'class1'或'class1 class2 class3'
         * cfg.title 设置浮层标题，如果没设置，删掉标题 win-hd
         * cfg.html 要插入到浮层内容框的html
         */
        jDialog.winPop = function(cfg){
            var win = {};
            //隐藏模式，可以设置为hidden（会给浮层加上hidden这个类名）或vhidden（会给浮层加上或vhidden这个类名）
            var hidden =  cfg.hidden == 'vhidden' ? 'vhidden' : 'hidden';
            //设置点击关闭按钮后是要隐藏浮层还是销毁浮层，如果设置为destroy则是销毁，如果设置为hide或其它情况则是隐藏
            var closeMode = cfg.closeMode;
            //浮层jquery对象
            win.box = $(jDialog.tpl.win);
            //关闭按钮
            win.closeElem = win.box.find('#win_close');
            //标题
            win.titleElem = win.box.find('#win_title');
            //要插入内容的容器
            win.contElem = win.box.find('#win_cont');
            win.mask = $(jDialog.tpl.mask);
            win.maskIframe = $(jDialog.tpl.maskIframe);

            //设置class
            if (cfg.className != undefined){
                win.box.addClass(cfg.className);
                win.box.addClass(hidden);
            }

            //设置浮层标题，如果没有，删掉标题DOM
            if (cfg.title != undefined){
                win.titleElem.text(cfg.title);
            } else {
                win.box.find('.win-hd').remove();
            }

            //插入内容到浮层内容框
            win.contElem.append($(cfg.html));
            //将浮层、遮罩层插入页面
            $('body').append(win.box);
            //显示浮层
            win.show = function(){
                win.box.removeClass(hidden);
                $('body').append([win.mask, win.maskIframe]);

                win.keyboardEvent();
            };
            //隐藏浮层
            win.hide = function(){
                win.box.addClass(hidden);
                win.mask.remove();
                win.maskIframe.remove();
            };
            //删除浮层
            win.destroy = function(){
                win.box.remove();
                win.mask.remove();
                win.maskIframe.remove();
                win = null;
            };

            //绑定关闭按钮事件
            win.closeElem.click(function(e){
                e.preventDefault();

                //win.offKeyboardEvent();

                if (closeMode == 'destroy'){
                    win.destroy();
                } else {
                    win.hide();
                }

                if (cfg.closeCallbackFunc){
                    cfg.closeCallbackFunc();
                }
            });

            win.close = function(){
                win.closeElem.click();
            };

            //绑定键盘事件
            win.keyboardEvent = function(){
                $('body').keyup(function(e){
                    if (win){
                        e.preventDefault();

                        //按ESC键退出浮层
                        if (e.which == 27){
                            win.close();
                        }
                    }
                });
            };

            return win;
        };

        /*
         * 确认浮层，在默认浮层基础上修改
         * 传递的参数为json
         * cfg.txt 为确认的文本内容
         * cfg.btnSureFunc 为点击确认按钮后的回调函数
         */
        jDialog.winConfirm = function(cfg){
            var winTemp = jDialog.winPop({
                "title": 'Confirm',
                "className": 'win-confirm',
                "hidden": 'hidden',
                "closeMode": 'destroy',
                "html": jDialog.tpl.confirm
            });

            //描述文本
            winTemp.box.find('.tips-tit').text(cfg.txt);
            //确认按钮点击
            winTemp.box.find('#win_sure').click(function(e){
                e.preventDefault();

                cfg.btnSureFunc();
                winTemp.closeElem.click();
            });

            //取消按钮点击
            winTemp.box.find('#win_cancel').click(function(e){
                e.preventDefault();

                winTemp.closeElem.click();
            });

            return winTemp;
        };

        /*
         * 提示浮层，在默认浮层基础上修改
         * 传递的参数为json
         * cfg.txt 为确认的文本内容
         * cfg.tipsTpye 设置提示信息类型，可以为succeed、alert如果不设置则默认为succeed
         */
        jDialog.winAlert = function(cfg){
            var winTemp = jDialog.winPop({
                "title": 'Alert',
                "className": 'win-confirm win-alert',
                "hidden": 'hidden',
                "closeMode": 'destroy',
                "html": jDialog.tpl.alert
            });

            var tipsType = cfg.tipsType == undefined ? 'succeed' : cfg.tipsType;
            winTemp.box.find('.tips-cont').addClass('tips-cont-' + tipsType);
            winTemp.box.find('.tips-icon-l').addClass('tips-' + tipsType);
            //描述文本
            winTemp.box.find('.tips-tit').text(cfg.txt);
            //OK按钮点击
            winTemp.box.find('#win_ok').click(function(e){
                e.preventDefault();

                if (cfg.btnSureFunc){
                    cfg.btnSureFunc();
                }
                winTemp.closeElem.click();
            });

            return winTemp;
        };


    })();
})(jQuery, window);