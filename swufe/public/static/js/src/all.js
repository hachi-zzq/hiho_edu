/*
 * 列表方式加载便于开发模式下调试脚本。
 * 待发布时再打包压缩列表中的脚本文件成单一文件。
 * base文件夹内是通用组件
 * mod文件夹内是一些模块
 */
(function($){
    //要加载的js列表
    var jsList = [
        'base/util.js',
        'mod/form.js',
        'mod/dialog.js',
        'mod/upload.js',
        'mod/tools.js',
        'mod/action.js'
    ];

    //取得js的基本路径
    var allJsSrc = $('#website_all_js').attr('src');
    var jsBaseUrl = allJsSrc.substring(0, allJsSrc.lastIndexOf('all.js'));

    //拼接
    var js = [];
    for (var i = 0; i < jsList.length; i++){
        js.push('<script src="' + jsBaseUrl + jsList[i] + '"><\/script>');
    }

    document.write(js.join(''));
})(jQuery);