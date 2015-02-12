(function($, win){
    var jUtil = JLib.Util;
    var jForm = JLib.form;
    var jDialog = JLib.dialog;
    var jUpload = JLib.upload;
    var jTools = JLib.tools;
    var pageName = JLib.config.pageName;

    $(function(){
        /*
         * 整站
         */
        (function(){
            /*描述文字在input-txt上面，当input获取焦点时文字隐藏 组件*/
            jForm.labelInputTxt({"target": $('.label-input-txt')});
        })();

        /*
         * 首页
         */
        (function(){
            if (pageName == 'index'){
                //搜索建议
                jTools.searchSuggest();

                //切换Most Popular
                var modPopular = {};
                modPopular.cont = $('#mod_popular');
                modPopular.filterLink = modPopular.cont.find('.filter a');
                modPopular.focusPic = modPopular.cont.find('.focus-pic');

                modPopular.filterLink.each(function(i){
                    var _self = $(this);

                    _self.click(function(e){
                        e.preventDefault();

                        modPopular.filterLink.removeClass('current');
                        _self.addClass('current');

                        modPopular.focusPic.addClass('hidden');
                        modPopular.focusPic.eq(i).removeClass('hidden');
                    });
                });

                //焦点图
                var focusNewest = new jTools.focusPic({
                    focusEl: $('#focus_newest'),
                    speed: 3000
                });
                focusNewest.init();

                var focusPopularity = new jTools.focusPic({
                    focusEl: $('#focus_popularity'),
                    speed: 3000
                });
                focusPopularity.init();
            }
        })();

        /*
         * 搜索列表页
         */
        (function(){
            if (pageName == 'searchResult'){
                //搜索建议
                jTools.searchSuggest();

                var searchForm = $('#site_search');
                var searchInput = searchForm.find('input[name="keywords"]');
                var btnAdd = $('#add_keyword');

                var searchListCont = $('#search_list_cont');
                //搜索列表每一项
                var item = null;
                //列表长度
                var len = 0;
                var keyword = searchInput.val();
                //当前加载的项
                var index = 0;

                //将搜索词添加到订阅
                var subscribeHtml = [
                    '<div class="subscribe-form">',
                        '<div class="item">',
                            '<span class="label">Keywords</span>',
                            '<p class="txt" id="word">TED</p>',
                        '</div>',
                        '<div class="item">',
                            '<span class="label">Period</span>',
                            '<div class="days" id="days">',
                                '<span class="day current" data-num="30">30 days</span><span class="day" data-num="60">60 days</span><span class="day" data-num="90">90 days</span>',
                            '</div>',
                        '</div>',
                        '<div class="item">',
                            '<span class="label">Free</span>',
                            '<p class="txt">promotion for free</p>',
                        '</div>',
                        '<div class="item action">',
                            '<a class="btn-s3" id="btn_submit" href="#" title="Submit">Submit</a>',
                        '</div>',
                    '</div>'
                ].join('');

                btnAdd.click(function(e){
                    e.preventDefault();

                    var win = jDialog.winPop({
                        "html": subscribeHtml,
                        "title": 'Subscribe keyword',
                        "className": 'win-subscribe',
                        "hidden": 'hidden',
                        "closeMode": 'destroy'
                    });

                    win.show();

                    var wordElem = win.box.find('#word');
                    var daysNum = 30;
                    var days = win.box.find('#days');
                    var dayItem = days.find('.day');
                    var btnSubmit = win.box.find('#btn_submit');
                    var word = searchInput.val();

                    wordElem.text(word);

                    dayItem.each(function(){
                        var _self = $(this);

                        _self.click(function(){
                            dayItem.removeClass('current');
                            _self.addClass('current');
                            daysNum = _self.data('num');
                        });
                    });

                    btnSubmit.click(function(e){
                        e.preventDefault();

                        $.get('/static/html_demo/default/test.json', {"days": daysNum, "word": word}, function(data){
                            data = jUtil.stringToJSON(data);
                            if (data.status == 0){
                                alert('Subscription succeed.');
                                win.close();
                            } else {
                                alert(data.message);
                            }
                        }).fail(function(){
                            alert('Sorry! System busy. Please retry later.');
                        });
                    });
                });

                //延时加载列表中每项的搜索结果
                setTimeout(function(){
                    item = searchListCont.find('.item');
                    len = item.length;

                    getSubtitle();
                }, 2000);

                function getSubtitle(){
                    var itemTemp = item.eq(index);

                    $.get("/static/source_demo/json/search_item.json", {"videoGuid": itemTemp.data('rel').id, "keywords": keyword},
                    //$.get("/search/getSingleSubtitleFt", {"videoGuid": itemTemp.data('rel').id, "keywords": keyword},
                        function(data){
                            data = jUtil.stringToJSON(data);
                            if (data.errorType == 0){
                                itemTemp.find('.info-cont').append(data.dom);

                                setTimeLine(itemTemp, data.videoTime);
                            }

                            itemTemp.find('.loading-s3').remove();

                            index++;
                            if (index < len){
                                getSubtitle();
                            }
                    });
                }

                //设置时间线
                function setTimeLine(item, videoTime){
                    var subtitleResult = item.find('.subtitle-result');
                    var subtitle = subtitleResult.find('.subtitle');
                    var times = subtitleResult.find('.time');
                    var timeLine = item.find('.time-line');
                    var operate = item.find('.operate');
                    var totalTime = jTools.convertTime(videoTime);

                    //根据内容多少设置load more、collapsed
                    if (subtitle.height() > subtitleResult.height()){
                        operate.removeClass('hidden');
                    } else {
                        operate.remove();
                    }
                    //设置关键词在时间线上的位置
                    times.each(function(){
                        var _self = $(this);
                        var t = jTools.convertTime(_self.text()) / totalTime * 100;

                        if (t >= 0 && t <= 100){
                            $('<span></span>').css('left',t + '%').appendTo(timeLine);
                        }
                    });

                    timeLine.removeClass('hidden');
                }

                //点击load more、collapsed事件
                searchListCont.on('click', '.list .operate', function(){
                    var _self = $(this);
                    var item = _self.parents('.item').eq(0);
                    if (item.hasClass('subtitle-all')){
                        item.removeClass('subtitle-all');
                    } else {
                        item.addClass('subtitle-all');
                    }
                });

                //点击关键词时跳转
                if (searchListCont[0]){
                    searchListCont.on('click', '.keyword', function(){
                        var _self = $(this);

                        var parent = _self.parent();
                        var link = parent.attr('link');
                        var st = parseFloat(parent.attr('data'));
                        var nextLine = parent.next();
                        var et = st + 5;

                        if (nextLine[0]){
                            et = parseFloat(nextLine.attr('data'));
                        }

                        link = link + '?st=' + st + '&et=' + et;

                        win.open(link);
                    });
                }

            }
        })();

        /*
         * 订阅列表页
         */
        (function(){
            if (pageName == 'subscription'){
                //修改订阅
                var setting = {};
                    setting.cont = $('#subscribed_setting');
                    setting.num = setting.cont.find('.status span');

                setting.cont.on('click', 'li', function(){
                    var _self = $(this);

                    if (_self.hasClass('selected')){
                        _self.removeClass('selected');
                    } else {
                        _self.addClass('selected');
                    }
                });

                setting.cont.on('click', '.ico-close-s1', function(e){
                    e.stopPropagation();

                    var _self = $(this);
                    var item = _self.parents('li').eq(0);
                    item.remove();
                });

                setting.cont.on('click', '#btn_subscribed', function(e){
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

        /*
         * 账户设置
         */
        (function(){
            if (pageName == 'account'){
                //修改用户头像
                jTools.setUserPic();

                //日历
                $( "#birthday" ).datepicker({
                    dateFormat: 'yy-mm-dd',
                    maxDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "c-100:c"
                });

                //表单
                var accountForm = $('#account_form');
                var btnSubmit = accountForm.find('#btn_submit');

                accountForm.validationEngine("attach", {
                    "promptPosition": "centerRight",
                    "addFailureCssClassToField": 'validation-error',
                    "maxErrorsPerField": 1,
                    "ajaxFormValidation": true,
                    "ajaxFormValidationMethod": 'post',
                    "onBeforeAjaxFormValidation": function(form, options){
                        btnSubmit.validationEngine('showPrompt', 'loading', 'load');
                    },
                    "onAjaxFormComplete": function(status, form, json, options){
                        if (status){
                            if (json.status == 0){
                                btnSubmit.validationEngine('showPrompt', 'Succeed', 'pass');
                            } else {
                                btnSubmit.validationEngine('showPrompt', json.message, 'error');
                            }
                        } else {
                            btnSubmit.validationEngine('showPrompt', 'System error', 'error');
                        }
                    }
                });
            }
        })();

        /*
         * 订阅详情
         */
        (function(){
            if (pageName == 'subscriptionDetails'){
                //表单
                var subscriptionForm = $('#subscription_form');
                var btnSubmit = subscriptionForm.find('#btn_submit');

                subscriptionForm.validationEngine("attach", {
                    "promptPosition": "centerRight",
                    "addFailureCssClassToField": 'validation-error',
                    "maxErrorsPerField": 1,
                    "ajaxFormValidation": true,
                    "ajaxFormValidationMethod": 'post',
                    "onBeforeAjaxFormValidation": function(form, options){
                        btnSubmit.validationEngine('showPrompt', 'loading', 'load');
                    },
                    "onAjaxFormComplete": function(status, form, json, options){
                        if (status){
                            if (json.status == 0){
                                btnSubmit.validationEngine('showPrompt', 'Succeed', 'pass');
                            } else {
                                btnSubmit.validationEngine('showPrompt', json.message, 'error');
                            }
                        } else {
                            btnSubmit.validationEngine('showPrompt', 'System error', 'error');
                        }
                    }
                });
            }
        })();

        /*
         * tv 详情页
         */
        (function(){
            if (pageName == 'tvDetails'){
                //焦点图
                var focusTv = new jTools.focusPic({
                    focusEl: $('#focus_tv'),
                    speed: 3000
                });
                focusTv.init();
            }
        })();

    });
})(jQuery, window);

