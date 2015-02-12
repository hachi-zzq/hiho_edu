<?php namespace HihoEdu\Controller\Admin;
use \View;
use \Input;
use \Config;
use \Redirect;

class SystemController extends AdminBaseController{
    const CONFIG_FILE = 'web_setting';

    /**
     *配置文件信息
     * @var array
     */
    private $arrConfig = array();

    /**
     * construct
     */
    public function __construct(){
        $this->arrConfig = $arrConfig = Config::get(self::CONFIG_FILE);
    }

    /**
     *系统设置首页
     * @param null
     * @return string
     * @author zhuzhengqian
     */
    public function index(){
        //取出配置文件
        $arrConfig = $this->arrConfig;
        return View::make('admin.system.index',compact('arrConfig'));
    }

    /**
     *更新系统站点设置
     * @param null
     * @return string
     * @author zhuzhengqian
     */
    public function webSetting(){
        $date = date('Ymd');
        $tarDir = public_path().'/upload/'.$date;
        if( ! is_dir($tarDir)){
            mkdir($tarDir);
        }
        //save logo
        $logoFile = Input::file('logo');
        if($logoFile){
            $randomName = date('His').rand(1,1000);
            $ext = $logoFile->guessExtension();
            $logoFile->move($tarDir,$randomName.'.'.$ext);
            $logoFileName ='/upload/'.$date.'/'.$randomName.'.'.$ext;
        }

        //save ico
        $icoFile = Input::file('ico_file');
        if($icoFile){
            $randomName = date('His').rand(1,1000);
            $ext = $icoFile->guessExtension();
            $icoFile->move($tarDir,$randomName.'.'.$ext);
            $icoFileName = '/upload/'.$date.'/'.$randomName.'.'.$ext;
        }
        //取出原有配置文件
        $configFileName = self::CONFIG_FILE.'.php';
        $configFile = app_path().'/config/'.$configFileName;
        $rawConfig = $this->arrConfig;
        $arrConfig = Input::except('logo','ico_file');
        if(isset($logoFileName)){
            $arrConfig['logo_url'] = $logoFileName;
        }
        if(isset($icoFileName)){
            $arrConfig['ico_url'] = $icoFileName;
        }
        foreach($arrConfig as $k=>$conf){
            if($k == 'analytics_code'){
                $rawConfig[$k] = json_encode($conf);
            }else{
                $rawConfig[$k] = $conf;
            }
        }
        $rawConfig = var_export($rawConfig,true);
        $code = "<?php\nreturn ".$rawConfig.';';
        file_put_contents($configFile,$code);
        return Redirect::to('/admin/system/index?active=website')->with('success_tips','设置成功');
    }

    /**
     *设置系统register设置
     * @author zhuzhengqian
     */
    public function registerSetting(){
        $configFile = app_path().'/config/'.self::CONFIG_FILE.'.php';
        //取出原有配置文件
        $arrConfig = $this->arrConfig;
        $arrConfig['register_mode'] = Input::get('register_mode');
        $arrConfig['active_email_title'] = Input::get('active_email_title');
        $arrConfig['active_email_content'] = Input::get('active_email_content');
        foreach($arrConfig as $k=>&$conf){
            if($k == 'active_email_content'){
                $arrConfig[$k] = json_encode($conf);
            }else{
                $arrConfig[$k] = $conf;
            }
        }
        $rawConfig = var_export($arrConfig,true);
        $code = "<?php\nreturn ".$rawConfig.';';
        file_put_contents($configFile,$code);
        return Redirect::to('/admin/system/index?active=register')->with('success_tips','设置成功');
    }

    /**
     *邮件服务器设置
     *@author zhuzhengqian
     */
    public function emailSetting(){
        $configFile = app_path().'/config/'.self::CONFIG_FILE.'.php';
        //取出原有配置文件
        $arrConfig = $this->arrConfig;
        $arrConfig['send_email'] = Input::get('send_email');
        $arrConfig['smtp_address'] = Input::get('smtp_address');
        $arrConfig['smtp_port'] = Input::get('smtp_port');
        $arrConfig['smtp_username'] = Input::get('smtp_username');
        $arrConfig['smtp_password'] = Input::get('smtp_password');

        $rawConfig = var_export($arrConfig,true);
        $code = "<?php\nreturn ".$rawConfig.';';
        file_put_contents($configFile,$code);
        return Redirect::to('/admin/system/index?active=email')->with('success_tips','设置成功');
    }

    /**
     *学院信息设置
     * @author zhuzhengqian
     */
    public function schoolSetting(){
        $configFile = app_path().'/config/'.self::CONFIG_FILE.'.php';
        //取出原有配置文件
        $arrConfig = $this->arrConfig;
        $arrConfig['school_name'] = Input::get('school_name');
        $arrConfig['school_description'] = Input::get('school_description');
        foreach($arrConfig as $k=>&$conf){
            if($k == 'school_description'){
                $arrConfig[$k] = json_encode($conf);
            }
        }
        $rawConfig = var_export($arrConfig,true);
        $code = "<?php\nreturn ".$rawConfig.';';
        file_put_contents($configFile,$code);
        return Redirect::to('/admin/system/index?active=school')->with('success_tips','设置成功');
    }

    /**
     *短信验证设置
     * @author zhuzhengqian
     */
    public function messageSetting(){
        $configFile = app_path().'/config/'.self::CONFIG_FILE.'.php';
        //取出原有配置文件
        $arrConfig = $this->arrConfig;
        $arrConfig['send_message'] = Input::get('send_message');
        $rawConfig = var_export($arrConfig,true);
        $code = "<?php\nreturn ".$rawConfig.';';
        file_put_contents($configFile,$code);
        return Redirect::to('/admin/system/index?active=message')->with('success_tips','设置成功');
    }
}