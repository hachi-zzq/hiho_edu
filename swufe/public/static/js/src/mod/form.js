/*
 * 表单相关的效果
 */

var JLib = JLib || {};
JLib.form = JLib.form || {};

(function($, win){
    (function(){
        /*描述文字在input-txt上面，当input获取焦点时文字隐藏 组件*/
        JLib.form.labelInputTxt = function(cfg){
            cfg.target.each(function(){
                var _self = $(this);
                var input = _self.find('.input-txt');
                var label = _self.find('.label');
                if (!input[0]){
                    input = _self.find('.input');
                }

                if ($.trim(input.val()) != ""){
                    _self.addClass('label-input-txt-focus');
                }

                input.focus(function(){
                    _self.addClass('label-input-txt-focus');
                }).blur(function(){
                    if ($.trim(input.val()) == ""){
                        _self.removeClass('label-input-txt-focus');
                    }
                }).keyup(function(){
                    _self.addClass('label-input-txt-focus');
                });

                label.click(function(){
                    input.focus();
                });
            });
        };
    })();
})(jQuery, window);