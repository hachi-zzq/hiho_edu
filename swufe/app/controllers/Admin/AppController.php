<?php namespace HihoEdu\Controller\Admin;

use \View;
use  HiHo\Tools\QRcode;
use \Input;
use \Config;
/**
 *APP的管理
 * DateTime: 14-9-1 下午1:50
 * author zhengqian.zhu <zhengqian.zhu@autotiming.com>
 */

class AppController extends AdminBaseController{

    /**
     * #下载管理
     * @return mixed
     * @author zhuzhengqian
     */
    public function index(){
        $appConfig = Config::get('app_download');
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $configFile = app_path('config/app_download.php');
            $inputData = Input::all();
            $rule = array(
                'android'=>'required',
                'ios'=>'required'
            );
            $validator = \Validator::make($inputData,$rule);

            if($validator->fails()){
                return \Redirect::to('/admin/App/download')->with('error_tips',$validator->messages()->first());
            }
            $android = trim($inputData['android']);
            $ios = trim($inputData['ios']);
            if($android && $ios){
                $rawConfig = var_export($inputData,true);
                $code = "<?php\nreturn ".$rawConfig.';';
                file_put_contents($configFile,$code);
            }

            return \Redirect::to('/admin/App/download');
        }
        return View::make('admin.app.index')
            ->with('android',$appConfig['android'])
            ->with('ios',$appConfig['ios']);
    }


}