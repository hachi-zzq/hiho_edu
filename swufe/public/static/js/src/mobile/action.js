(function($, win){
    $(function(){
        /*
         * 播放页
         */
        (function(){
            if (JLib.config.pageName == 'play'){
                var downloadCont = $('#download_cont');
                var downloadClose = downloadCont.find('.btn-close');

                downloadClose.click(function(){
                    downloadCont.remove();
                });
            }
        })();

    });
})(jQuery, window);

