/*
 * 针对jquery.uploadify上传控件相关自定义函数
 */

var JLib = JLib || {};
JLib.upload = JLib.upload || {};

(function($, win){
    (function(){
        /*
         * 上传控件选择发生错误时调用
         * file, errorCode, errorMsg三个为jquery.uploadify上传控件的onSelectError函数的参数
         * cfg为自定义参数：tipsElem为显示提示信息的元素
         */
        JLib.upload.selectError = function(file, errorCode, errorMsg, cfg){
            var tipsElem = cfg.tipsElem;
            switch (errorCode) {
                //选择的文件数量超过限制
                case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
                    tipsElem.text('Select the number of files exceeds the limit');
                    break;
                //选择的文件大小超过限制
                case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
                    tipsElem.text('Select the file size exceeds the limit. Audio file limit of 100MB, subtitle files up to 10MB');
                    break;
                //不能上传大小为0的文件
                case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
                    tipsElem.text('Can not upload an empty file');
                    break;
                //文件类型不在设置的范围内
                case SWFUpload.INVALID_FILETYPE:
                    tipsElem.text('Within the scope of the file type is not set');
                    break;
                default :

            }
            tipsElem.removeClass('hidden');
        };
    })();
})(jQuery, window);