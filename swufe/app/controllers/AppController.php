<?php
/**
 * #app controller
 * Class AppController
 * @author zhuzhengqian
 */
class AppController extends BaseController{

    /**
     * #app下载，根据手机Android ios
     * @return mixed
     * @author zhuzhengqian
     */
    public function appDownload(){
        $downloadConf = Config::get('app_download');
        $detect = new Mobile_Detect();
        if($detect->is('AndroidOS')){
            return \Redirect::to('http://app.autotiming.com');
        }elseif($detect->is('ios')){
            header('Location:itms-services://?action=download-manifest&url=https://enduba.sinaapp.com/xn.plist');
        }else{
            return \Redirect::to('http://app.autotiming.com');
        }
    }
}