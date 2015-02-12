<?php

use Hiho\Model\OpenLogin;
use HiHo\Other\Sms;
use HiHo\Model\User;
use HiHo\Model\UserKv;

/**
 * @author Hanxiang<hanxiang.qiu@autotiming.com>
 */
class UserController extends BaseController{

    /**
     * 用户登录页面
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function login(){
        Session::forget('login.need.code');
        if(Auth::user()) {
            return Redirect::to("/");
        }
        return View::make('login')->with('captcha',$this->getCaptchaString());
    }

    /**
     * 用户登录验证
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function loginPost()
    {
        // 表单验证规则
        $email = Input::get('email');
        $password = Input::get('password');
        $validateCode = Input::get('validate_code');
        $input = array('email' => $email, 'password' => $password, 'validate_code' => $validateCode);
        $rules = array(
            'email' => array('required', 'min:6', 'max:64'),
            'password' => array('required', 'min:3', 'max:64'),
        );
        $needCheckCode = Session::get('login.need.code'); //需要验证码
        if ($needCheckCode) {
            $rules['validate_code'] = array('required', 'min:4', 'max:4');
        }
        $v = Validator::make($input, $rules);
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -2, "message" => $messages, "captcha"=>$this->getCaptchaString()));
        }
        if ($needCheckCode) {
            // 判断验证码是否正确
            if (strtolower($validateCode) != strtolower(Session::get('validateCode'))) {
                return json_encode(array("status" => -3, "message" => "validate code error", "captcha"=>$this->getCaptchaString()));
            }
        }
        $needCode = false;
        $needLocked = $this->isLocked($email); //锁定账户
        if ($needLocked) {
            return json_encode(array("status" => -100, "message" => "账户已经被锁定", "captcha"=>$this->getCaptchaString()));
        }
        $loginFailKey = UserKv::LOGIN_FAIL_TIMES_KEY . '_' . $email;
        // 验证登录
        if (!Auth::attempt(array('email' => $email, 'password' => $password, 'status' => User::STATUS_NORMAL), true)) {
            if (!Auth::attempt(array('mobile' => $email, 'password' => $password, 'status' => User::STATUS_NORMAL), true)) {
                if (FALSE) {
                    Event::fire('user.login.abnormal', array($input['email']));
                }

                if (\Config::get('hiho.security_policy.login_fail_need_verification_code_max_times') > -1 or \Config::get('hiho.security_policy.login_fail_lock_max_times') > -1) {
                    $loginFailTimes = \Cache::get($loginFailKey, 0) + 1;
                    \Cache::put($loginFailKey, $loginFailTimes, \Config::get('hiho.security_policy.login_fail_interval'));
                    if (\Config::get('hiho.security_policy.login_fail_need_verification_code_max_times') != -1 and $loginFailTimes > \Config::get('hiho.security_policy.login_fail_need_verification_code_max_times')) {
                        $needCode = true;
                    }
                    if (\Config::get('hiho.security_policy.login_fail_lock_max_times') != -1) { //开启锁定功能
                        $needLocked = $loginFailTimes >= \Config::get('hiho.security_policy.login_fail_lock_max_times');
                        if ($needLocked) {
                            $currentUser = User::where('email', $email)->orwhere('mobile', $email)->first();
                            if ($currentUser) {
                                $currentUser->status = User::STATUS_LOCKED;
                                $currentUser->save();
                                $userKv = new  UserKv();
                                $userKv->user_id = $currentUser->user_id;
                                $userKv->key = UserKv::LOGIN_FAIL_LOCKED_KEY;
                                $userKv->value = date('Y-m-d H:i:s', strtotime("+" . \Config::get('hiho.security_policy.lock_user_duration') . " minute"));
                                $userKv->save();
                            }
                            return json_encode(array("status" => -100, "message" => "账户已经被锁定", "captcha"=>$this->getCaptchaString()));
                        }
                    }
                    if ($needCode) {
                        Session::put('login.need.code', true);
                    }
                }

                return json_encode(array("status" => -1, "message" => "Email or password incorrect, or user status is not normal.", "needCode" => $needCode, "captcha"=>$this->getCaptchaString()));
            }
        }
        \Cache::forget($loginFailKey);
        Session::forget('login.need.code');
        Session::put('user_id', Auth::user()->user_id);
        Session::put('email', $email);

        //用户最后登录时间
        $user = \User::find(\Auth::user()->user_id);
        $user->last_ip = Request::getClientIp();
        $user->last_time = date('Y-m-d H:i:s');
        $user->save();

        if (Auth::user()->is_admin) {
            //存储管理员信息  by zhuzhengqian
            Session::put('is_admin', 1);
            Session::put('objAdmin', Auth::user());


            return json_encode(array("status" => 1, "message" => "sign in success", "url" => action("HomeController@index")));
        }
        return json_encode(array("status" => 0, "message" => "sign in success", "url" => action("HomeController@index")));
    }

    /**
     * 用户注册页面
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function signupPhone(){
        if(Auth::user()) {
            return Redirect::to("/");
        }
        return View::make('signup_phone');
    }

    /**
     * 用户注册验证
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function signupPost(){
        // 表单验证规则
        $input = Input::only('email', 'password', 'confirm_password');
        $rules = array(
            'email' => array('required', 'email', 'unique:users'),
            'password' => array('required', 'min:6', 'max:64'),
        );
        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => $messages->first()));
        }

        // 判断用户是否存在
        if (User::where('email', Input::get('email'))->count()) {
            return json_encode(array("status"=> -1, "message"=>"username has been exist"));
        }

        // 写入数据库
        $user = new User();
        $user->guid = Uuid::v4();
        $user->email = $input['email'];
        $user->avatar = "/static/img/Sunny-Face.png";
        $user->password = Hash::make($input['password']);
        $user->status = User::STATUS_NORMAL;
        $user->last_time = new DateTime;
        $user->last_ip = Request::getClientIp();
        $user->created_ip = Request::getClientIp();
        $user->is_admin = 0;
        $user->is_email_validated = User::EMAIL_VALIDATED_NO;
        $user->save();

        $userSaved = User::where('guid', $user->guid)->first();
        $userRole = new UserRole();
        $userRole->role_id = User::USER_ROLE_COMMON;//普通注册用户默认使用普通用户角色
        $userRole->user_id = $userSaved->user_id;
        $userRole->save();

        // 发送验证邮件
        $data['email'] = $input['email'];
        $data['name'] = '用户';
        $data['activate_url'] = \URL::to('/') . '/activate/' . $user->guid;
        Mail::send('emails.welcome', $data, function ($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('欢迎加入西南财经大学教材资料馆');
        });

        return json_encode(array("status"=> 0, "message"=>"register success, please check your Email inbox!"));
    }

    /** 第三方API接口redirect_url */
    public function weiboCallBack() {
        if (empty($_GET['code'])){
            return;
        }
        $code = $_GET['code'];
        $res = parent::getWeiboUser($code);

        if (!$res) {
            Redirect::action('UserController@login');
        }

        $openLogin = new \HiHo\Model\OpenLogin();
        $user = $openLogin::where('open_id', '=', $res['idstr'])->get()->first();

        //此用户未绑定过
        if (empty($user)) {
            //add a new user
            $user_t = new \User();
            $user_t->guid = $guid = \Uuid::v4();
            $user_t->email = $guid;
            $user_t->password = \Hash::make('123456');
            $user_t->last_time = new \DateTime;
            $user_t->last_ip = \Request::getClientIp();
            $user_t->created_ip = \Request::getClientIp();
            $user_t->is_admin = 0;
            $user_t->status = User::STATUS_NORMAL;
            $user_t->save();

            $newUser = $user_t::where('guid', '=', $guid)->get()->first();

            $openLogin->user_id = $newUser->user_id;
            $openLogin->open_access_token = $res['access_token'];
            $openLogin->open_id = $res['id'];
            $openLogin->open_name = $res['screen_name'];
            $openLogin->mode = 1;//weibo
            $openLogin->status = 1;
            $openLogin->last_time = date('Y-m-d H:i:s', time());
            $openLogin->expire = date('Y-m-d H:i:s', time() + $res['expires_in']);
            $openLogin->extra = '';
            $save = $openLogin->save();
            var_dump($save);
            return;
        }

        //此用户已经绑定过，更新token
        $user->open_access_token = $res['access_token'];
        $user->open_name = $res['screen_name'];
        $user->last_time = date('Y-m-d H:i:s', time());
        $user->expire = date('Y-m-d H:i:s', time() + $res['expires_in']);
        $user->save();

        \Auth::login($user);
        \Session::put('user_id', \Auth::user()->user_id);
        \Session::put('email', \Auth::user()->email);
        return \Redirect::to('/');
    }

    public function fbCallBack() {
        if (empty($_GET['code'])){
            return;
        }
        $code = $_GET['code'];
        $res = parent::getFacebookUser($code);
        return $res;
    }

    /**
     * 重置密码页面
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function forgot() {
        return View::make('forgot');
    }

    /**
     * 重置密码操作
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function forgotPost() {
        $email = Input::get('email');
        $input = array('email' => $email);
        $rules = array(
            'email' => array('required', 'min:6', 'max:64'),
        );
        $v = Validator::make($input, $rules);
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => $messages));
        }
        if (\Config::get('hiho.security_policy.password_forgot_post_max_times') != -1) {
            $ip = Request::getClientIp();
            $pwdForgotKey = UserKv::PASSWORD_FORGOT_POST_TIMES_KEY . '_' . $email . '_' . $ip;
            $postTimes = \Cache::get($pwdForgotKey, 0) + 1;
            \Cache::put($pwdForgotKey, $postTimes, \Config::get('hiho.security_policy.password_forgot_post_interval'));
            if ($postTimes > \Config::get('hiho.security_policy.password_forgot_post_max_times')) {
                return json_encode(array('status' => -2, 'message' => '账户' . $email . '密码找回太频繁等等再发吧',));
            }
        }
        $user = User::whereRaw("email = ?", array($email))->first();
        if (empty($user)) {
            return json_encode(array('status' => -1, 'message' => 'not regiested'));
        }

        $userKvIP = UserKv::getByUserAndKey($user->user_id, Userkv::USER_LAST_FORGOT_IP);
        if (!$userKvIP) {
            $userKvIP = new \UserKv();
            $userKvIP->user_id = $user->user_id;
            $userKvIP->key = Userkv::USER_LAST_FORGOT_IP;
        }
        $userKvIP->value = Request::getClientIp();
        $userKvIP->save();

        // 发送验证邮件
        $data['email'] = $email;
        $data['name'] = empty($user->nickname) ? '用户' : $user->nickname;
        $uniqueToken = md5($user->guid . uniqid());
        $data['reset_url'] = \URL::to('/') . '/resetPassword/' . $uniqueToken;

        $userkv = new \UserKv();
        $userkv->user_id = $user->user_id;
        $userkv->key = 'resetPassword';
        $userkv->value = $uniqueToken;
        $userkv->save();

        Mail::send('emails.reset_password', $data, function ($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('密码重置 - 西南财经大学教材资料馆');
        });
        return json_encode(array('status' => 0, 'message' => 'success', 'email' => $email));

    }

    /**
     * 重置密码页面
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function resetPassword($token) {
        $userkv = UserKv::where('value', '=', $token)->first();
        if(empty($userkv)){
            return View::make('no_exist');
        }

        $user = User::find($userkv->user_id);

        return View::make('reset_password')->with('user', $user)->with('token', $token);
    }

    /**
     * 重置密码操作
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function resetPasswordPost() {
        $input = Input::only('token', 'password', 'confirm_password');

        if($input['password'] != $input['confirm_password']) {
            return json_encode(array("status" => -2, "message" => "请输入正确信息"));
        }

        $rules = array(
            'token' => array('required'),
            'password' => array('required', 'min:6', 'max:64'),
        );
        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => "请输入正确信息"));
        }

        $userkv = UserKv::where('value', '=', $input['token'])->first();

        User::where('user_id', '=', $userkv->user_id)->update(array('password' => \Hash::make($input['password'])));

        UserKv::where('value', '=', $input['token'])->delete();

        return json_encode(array("status" => 0, "message" => "success"));
    }

    /**
     * 密码修改成功
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function resetSuccess($guid) {
        $user = User::whereRaw("guid = ?", array($guid))->first();
        if(empty($user)){
            App::abort(404, 'not available user');
        }
        return View::make('reset_success')->with('user', $user);
    }

    /**
     * 注销登录
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return mixed
     */
    public function logout()
    {
        Auth::logout();
        // Session::flush();
        //选择性清除session，保留管理员session by zhengqian.zhu@autotiming.com
        Session::forget('user_id');
        Session::forget('email');
        return Redirect::to('/');
    }

    /**
     * 激活用户
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getActivate($token) {
        // 验证 GUID 是否存在
        $userKv = UserKv::where('key', UserKv::USER_EMAIL_ACTIVATE_TOKEN)->where('value', $token)->first();
        if (!$userKv) {
            return Redirect::to('/')
                ->with('error_tips', 'User activation failed.');
        }
        // 激活账户
        DB::table('users')
            ->where('user_id', $userKv->user_id)
            ->update(array('status' => User::STATUS_NORMAL, 'is_email_validated' => User::EMAIL_VALIDATED_YES));
        $userKv->delete();
        // 登录身份
        $user = User::where('user_id', '=', $userKv->user_id)->first();
        Auth::login($user);

        Session::put('user_id', $user->user_id);
        Session::put('email', $user->email);

        return Redirect::to('/')
            ->with('success_tips', 'Your account activation is successful, Welcome to HiHo!');
    }

    /**
     * 发送邮件成功
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function resetSendSuccess() {
        $data = array();
        $data['email'] = Input::get('email');
        return View::make('reset_pw_send_success')->with('data', $data);
    }

    /**
     * 发送短信
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function sendSMS() {
        $inputData = Input::only('mobile', 'type');
        $rules = array(
            'mobile' => 'required',
            'type' => 'required'
        );
        $v = Validator::make($inputData, $rules);
        if($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => $messages->first()));
        }

        //check phone registered or not
        $type = $inputData['type'];
        $user = User::where('mobile', $inputData['mobile'])->first();
        if($type == 'register') {
            if ($user) {
                return json_encode(array("status" => -2, "message" => "mobile has been registered"));
            }
        }
        elseif($type == 'reset') {
            if (empty($user)) {
                return json_encode(array("status" => -3, "message" => "mobile not registered"));
            }
        }
        elseif($type == 'modify') {
            $user_id = Auth::user()->user_id;
            $userByMobile = User::where('mobile', $inputData['mobile'])->where('user_id', '<>', $user_id)->first();
            if ($userByMobile) {
                return json_encode(array("status" => -5, "message" => "mobile not availabe"));
            }
        }
        else {
            return json_encode(array("status" => -4, "message" => "type error"));
        }

        $mobile = $inputData['mobile'];
        $code = rand(1000, 9999);
        $contentFormat = \Config::get('sms.content') . \Config::get('sms.signature');
        $content = sprintf($contentFormat, $code);

        Session::put('verifyCode', $code);

        Sms::sendSMS($mobile, $content);
        return json_encode(array("status" => 0, "message" => "send success"));
    }

    /**
     * 手机号码注册验证
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function signupPhonePost() {
        $input = Input::only('mobile', 'password', 'code');
        $rules = array(
            'mobile' => array('required', 'unique:users'),
            'password' => array('required', 'min:6', 'max:16'),
            'code' => array('required')
        );

        $v = Validator::make($input, $rules);
        if($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => $messages->first()));
        }

        // 判断用户是否存在
        if (User::where('mobile', $input['mobile'])->count()) {
            return json_encode(array("status"=> -2, "message"=>"user mobile has been exist"));
        }

        if($input['code'] != Session::get('verifyCode')) {
            return json_encode(array("status"=> -3, "message"=>"code error"));
        }
        Session::forget('verifyCode');

        // 写入数据库
        $user = new User();
        $user->guid = Uuid::v4();
        $user->email = $input['mobile'];
        $user->mobile = $input['mobile'];
        $user->avatar = "/static/hiho-edu/img/avatar_default.png";
        $user->password = Hash::make($input['password']);
        $user->status = User::STATUS_NORMAL;
        $user->last_time = new DateTime;
        $user->last_ip = Request::getClientIp();
        $user->created_ip = Request::getClientIp();
        $user->is_admin = 0;
        $user->register_type = 'mobile';
        $user->save();

        Auth::attempt(array('mobile' => $input['mobile'], 'password' => $input['password'], 'status' => User::STATUS_NORMAL), true);

        return json_encode(array("status"=> 0, "message"=>"register success, please check your Email inbox!"));
    }

    /**
     * 手机号码注册step2 --输入昵称和邮箱
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function signupPhoneS2() {
        return View::make('signup_phone_step2');
    }

    /**
     * 手机号码注册step2验证
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function signupPhoneS2Post() {
        $input = Input::only('nickname', 'email');
        $rules = array(
            'email' => array('required', 'unique:users'),
            'nickname' => array('required')
        );

        $v = Validator::make($input, $rules);
        if($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => $messages->first()));
        }

        if(!Auth::user()) {
            return json_encode(array("status" => -2, "message" => "not logged in"));
        }

        // 判断用户是否存在
        if (User::where('email', $input['email'])->count()) {
            return json_encode(array("status"=> -3, "message"=>"user email has been exist"));
        }

        $user_id = Auth::user()->user_id;
        User::where('user_id', $user_id)->update(array('nickname' => $input['nickname'], 'email' => $input['email']));
        return json_encode(array("status" => 0, "message" => "signup success"));
    }

    /**
     * 手机注册成功页面
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function signupPhoneSuccess() {
        return View::make('signup_phone_success');
    }

    /**
     * 邮箱注册页面
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function signupEmail() {
        return View::make('signup_email')->with('captcha',$this->getCaptchaString());
    }

    /**
     * 邮箱注册验证
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function signupEmailPost() {
        $input = Input::only('email', 'password', 'code');
        $rules = array(
            'email' => array('required', 'email'),
            'password' => array('required', 'min:6', 'max:64')
        );
        $v = Validator::make($input, $rules);
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => $messages->first()));
        }

        // 判断用户是否存在
        $user = User::withTrashed()->whereRaw("email = ?", array($input['email']));
        if ($user->count()) {
            if ($user->first()->deleted_at) {
                // already deleted, modify the user deleted_at
                $user = User::withTrashed()
                        ->whereRaw("email = ?", array($input['email']))
                        ->update(array('deleted_at' => NULL, 'password' => Hash::make($input['password'])));
                Auth::attempt(array('email' => $input['email'], 'password' => $input['password'], 'status' => User::STATUS_NORMAL), true);
                return json_encode(array("status"=> 0, "message"=>"change existed user success"));
            }
            return json_encode(array('status' => -2, 'message' => 'the email has been already taken'));
        }

        // 判断验证码是否正确
        if (strtolower($input['code']) != Session::get('validateCode')) {
            return json_encode(array("status" => -3, "message" => "validate code error"));
        }

        // 写入数据库
        $user = new User();
        $user->guid = Uuid::v4();
        $user->email = $input['email'];
        $user->avatar = "/static/hiho-edu/img/avatar_default.png";
        $user->password = Hash::make($input['password']);
        $user->status = User::STATUS_NORMAL;
        $user->last_time = new DateTime;
        $user->last_ip = Request::getClientIp();
        $user->created_ip = Request::getClientIp();
        $user->is_admin = 0;
        $user->register_type = 'email';
        $user->save();

        Auth::attempt(array('email' => $input['email'], 'password' => $input['password'], 'status' => User::STATUS_NORMAL), true);

        $uniqueToken = md5($user->guid . uniqid());
        $userkv = new \UserKv();
        $userkv->user_id = $user->user_id;
        $userkv->key = UserKv::USER_EMAIL_ACTIVATE_TOKEN;
        $userkv->value = $uniqueToken;
        $userkv->save();

        // 发送验证邮件
        $data['email'] = $input['email'];
        $data['name'] = '用户';
        $data['activate_url'] = \URL::to('/') . '/activate/' . $uniqueToken;
        Mail::send('emails.welcome', $data, function ($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('欢迎加入西南财经大学教材资料馆');
        });

        return json_encode(array("status"=> 0, "message"=>"register success, please check your Email inbox!"));
    }

    /**
     * 邮箱注册step2
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function signupEmailS2() {
        return View::make('signup_email_step2');
    }

    /**
     * 邮箱注册step2验证
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function signupEmailS2Post() {
        $input = Input::only('nickname');
        $rules = array(
            'nickname' => 'required'
        );
        $v = Validator::make($input, $rules);
        if($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => $messages->first()));
        }

        if(!Auth::user()) {
            return json_encode(array("status" => -2, "message" => "not logged in"));
        }

        $user_id = Auth::user()->user_id;
        User::where('user_id', $user_id)->update(array('nickname' => $input['nickname']));
        return json_encode(array("status" => 0, "message" => "signup success"));
    }

    /**
     * 邮箱注册成功
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function signupEmailSuccess() {
        return View::make('signup_email_success');
    }

    /**
     * 判断账户是否被锁住
     * @param $account
     * @return bool
     * @author Haiming<haiming.wang@autotiming.com>
     */
    private function isLocked($account)
    {
        $user = User::where('email', $account)->orwhere('mobile', $account)->first();
        $isLocked = ($user and $user->status == User::STATUS_LOCKED);
        if ($isLocked) {
            if(\Config::get('hiho.security_policy.login_fail_lock_max_times') != -1){
                $uk = UserKv::where('user_id', $user->user_id)->where('key', UserKv::LOGIN_FAIL_LOCKED_KEY)->first();
                if ($uk) {
                    $diff = strtotime($uk->value) - strtotime(date('Y-m-d H:i:s'), time());
                    if ($diff < 0) {
                        $loginFailKey = UserKv::LOGIN_FAIL_TIMES_KEY . '_' . $user->email;
                        \Cache::forget($loginFailKey);
                        $loginFailKey = UserKv::LOGIN_FAIL_TIMES_KEY . '_' . $user->mobile;
                        \Cache::forget($loginFailKey);
                        $user->status = User::STATUS_NORMAL;
                        $user->save();
                        $uk->delete();
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 重置密码页面
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function reset() {
        return View::make('reset_pw')->with('captcha',$this->getCaptchaString());
    }

    /**
     * 重置密码验证 step1
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function resetPost() {
        $input = Input::only('account', 'code', 'type');
        $rules = array(
            'account' => 'required',
            'code' => 'required',
            'type' => 'required'
        );
        $v = Validator::make($input, $rules);
        if($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => $messages->first()));
        }

        //check the validate code
        if($input['code'] != Session::get('validateCode')) {
            return json_encode(array("status" => -2, "message" => "验证码不正确"));
        }

        $type = $input['type'];
        if($type == 'mobile') {
            $user = User::where('mobile', $input['account'])->first();
        }
        elseif($type == 'email') {
            $user = User::where('email', $input['account'])->first();
        }
        else {
            return json_encode(array("status" => -3, "message" => "type error"));
        }

        if(empty($user)) {
            return json_encode(array("status" => -4, "message" => "该邮箱或手机号还未注册"));
        }

        if($type == 'mobile') {
            // redirect to phone step2 page
            return json_encode(array("status" => 0, "message" => "mobile correct"));
        }
        elseif($type == 'email') {
            if (\Config::get('hiho.security_policy.password_forgot_post_max_times') != -1) {
                $ip = Request::getClientIp();
                $pwdForgotKey = UserKv::PASSWORD_FORGOT_POST_TIMES_KEY . '_' . $input['account'] . '_' . $ip;
                $postTimes = \Cache::get($pwdForgotKey, 0) + 1;
                \Cache::put($pwdForgotKey, $postTimes, \Config::get('hiho.security_policy.password_forgot_post_interval'));
                if ($postTimes > \Config::get('hiho.security_policy.password_forgot_post_max_times')) {
                    return json_encode(array('status' => -2, 'message' => '账户' . $input['account'] . '密码找回太频繁等等再发吧',));
                }
            }
            $user = User::whereRaw("email = ?", array($input['account']))->first();
            if (empty($user)) {
                return json_encode(array('status' => -4, 'message' => 'not regiested'));
            }

            $userKvIP = UserKv::getByUserAndKey($user->user_id, Userkv::USER_LAST_FORGOT_IP);
            if (!$userKvIP) {
                $userKvIP = new \UserKv();
                $userKvIP->user_id = $user->user_id;
                $userKvIP->key = Userkv::USER_LAST_FORGOT_IP;
            }
            $userKvIP->value = Request::getClientIp();
            $userKvIP->save();

            // 发送验证邮件
            $data['email'] = $input['account'];
            $data['name'] = empty($user->nickname) ? '用户' : $user->nickname;
            $uniqueToken = md5($user->guid . uniqid());
            $data['reset_url'] = \URL::to('/') . '/reset/email/' . $uniqueToken;

            $userkv = new \UserKv();
            $userkv->user_id = $user->user_id;
            $userkv->key = 'resetPassword';
            $userkv->value = $uniqueToken;
            $userkv->save();

            Mail::send('emails.reset_password', $data, function ($message) use ($data) {
                $message->to($data['email'], $data['name'])->subject('密码重置 - 西南财经大学教材资料馆');
            });
            return json_encode(array("status" => 1, "message" => "email correct"));
        }
    }

    /**
     * 通过手机号重置密码
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function resetByPhone() {
        $mobile = Input::get('mobile');
        $user = User::where('mobile', $mobile)->first();
        if(empty($user)) {
            App::abort(404, 'not available user');
        }
        return View::make('reset_pw_phone')->with('mobile', $mobile);
    }

    /**
     * 通过手机号重置密码 step2
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function resetByPhonePost() {
        $input = Input::only('mobile', 'code', 'password');
        $rules = array(
            'mobile' => 'required',
            'code' => 'required',
            'password' => 'required'
        );
        $v = Validator::make($input, $rules);
        if($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => $messages->first()));
        }

        // 判断用户是否存在
        if (User::where('mobile', $input['mobile'])->count() == 0) {
            return json_encode(array("status"=> -2, "message"=>"该手机号还未注册"));
        }

        if($input['code'] != Session::get('verifyCode')) {
            return json_encode(array("status"=> -3, "message"=>"验证码错误"));
        }
        Session::forget('verifyCode');

        User::where('mobile', $input['mobile'])->update(array('password' => \Hash::make($input['password'])));
        return json_encode(array("status" => 0, "message" => "update success"));
    }

    /**
     * 发送重置邮件成功
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function resetEmailSent() {
        $email = Input::get('email');
        $user = User::where('email', $email)->first();
        if(empty($user)) {
            App::abort(404, 'not available user');
        }
        return View::make('reset_pw_email_sent')->with('email', $email);
    }

    /**
     * 通过邮箱修改密码
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function resetByEmail($token) {
        $userkv = UserKv::where('value', '=', $token)->first();
        if(empty($userkv)){
            return View::make('no_exist');
        }

        return View::make('reset_pw_email')->with('token', $token);
    }

    /**
     * 通过邮箱修改密码验证
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function resetByEmailPost(){
        $input = Input::only('token', 'password');
        $rules = array(
            'token' => 'required',
            'password' => 'required'
        );
        $v = Validator::make($input, $rules);
        if($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => $messages->first()));
        }

        $userkv = UserKv::where('value', '=', $input['token'])->first();

        User::where('user_id', '=', $userkv->user_id)->update(array('password' => \Hash::make($input['password'])));

        UserKv::where('value', '=', $input['token'])->delete();

        return json_encode(array("status" => 0, "message" => "success"));
    }

    /**
     * 重置密码成功
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function resetPwSuccess(){
        return View::make('reset_pw_success');
    }

    /**
     * 个人设置页面
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function profile() {
        $user = Auth::user();
        if (empty($user->avatar)) {
            $user->avatar = Config::get('app.pathToSource') . '/img/avatar_default.png';
        }
        return View::make('profile')->with('user', $user);
    }

    /**
     * 个人设置页面POST操作
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function profilePost() {
        $input = Input::only('name', 'email', 'mobile', 'password', 'code', 'infoFlag');

        $user = Auth::user();
        if (!$user) {
            return json_encode(array('status' => -2, 'message' => 'not logged in'));
        }

        if ($input['infoFlag']) {
            if (!empty($input['name'])) {
                $user->nickname = strip_tags($input['name']);
            }

            if (!empty($input['email'])) {
                $userByEmail = User::where('email', $input['email'])->where('user_id', '<>', $user->user_id)->first();
                if ($userByEmail) {
                    return json_encode(array('status' => -3, 'message' => 'email has been taken'));
                }
                $user->email = $input['email'];
            }

            if (!empty($input['mobile'])) {
                $userByPhone = User::where('mobile', $input['mobile'])->where('user_id', '<>', $user->user_id)->first();
                if ($userByPhone) {
                    return json_encode(array('status' => -4, 'message' => 'mobile has been taken'));
                }
                if (empty($input['code'])) {
                    return json_encode(array('status' => -5, 'message' => 'verify code error'));
                }
                if ($input['code'] != Session::get('verifyCode')) {
                    return json_encode(array('status' => -5, 'message' => 'verify code error'));
                }
                $user->mobile = $input['mobile'];
            }
        }

        if (!empty($input['password'])) {
            if ($user->password != $input['password']) {
                return json_encode(array('status' => -6, 'message' => 'password error'));
            }
            $user->password = Hash::make($input['password']);
        }

        $user->save();
        return json_encode(array('status' => 0, 'message' => 'success'));
    }
}
