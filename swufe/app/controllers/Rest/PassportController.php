<?php namespace HiHo\Edu\Controller\Rest;

use HiHo\Model\Favorite;
use HiHo\Model\Fragment;
use \FragmentShare;
use \Playlist;
use \PlaylistFavorite;
use \OpenLogin;
use HiHo\Model\User;
use HiHo\Model\Subscription;
use HiHo\OpenAuth\Weibo;
use HiHo\OpenAuth\Facebook;
use HiHo\Model\UserKv;


/**
 * RestAPI 通行证
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class PassportController extends BaseController
{
    /**
     * 注册新用户, 以获得 Token
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function register()
    {
        // 表单验证规则
        $input = \Input::only('email', 'password', 'confirm_password', 'client', 'device_id');
        $rules = array(
            'email' => array('required', 'min:6', 'max:64'),
            'password' => array('required', 'min:6', 'max:64'),
            'client' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 判断用户是否存在
        if (\User::where('email', \Input::get('email'))->count()) {
            return $this->encodeResult('20204', 'Email address has been registered.');
        }

        // 写入数据库
        $user = new \User();
        $user->guid = \Uuid::v4();
        $user->email = $input['email'];
        $user->password = \Hash::make($input['password']);
        $user->last_time = new \DateTime;
        $user->last_ip = \Request::getClientIp();
        $user->created_ip = \Request::getClientIp();
        $user->is_admin = 0;
        $user->status = User::STATUS_NORMAL;;
        $user->save();

        // 返回新生成的 Token
        $token = $this->newLoginToken($user->user_id, $input['client']);

        return $this->encodeResult('10101', 'succeed', array('token' => $token->toArray(), 'verify_email' => true));
    }

    /**
     * 登录以获得 Token
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function login()
    {
        // 表单验证规则
        $input = \Input::only('email', 'password', 'client', 'device_id');
        $rules = array(
            'email' => array('required', 'min:6', 'max:64', 'email'),
            'password' => array('required', 'min:6', 'max:64'),
            'client' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }
        $currentUser = array();
        if($this->isLocked($input['email'])){
            return $this->encodeResult('20202', 'Account has bean locked.');
        }
        $loginFailKey = UserKv::LOGIN_FAIL_TIMES_KEY . '_' . $input['email'];
        // 验证登录
        if (!\Auth::attempt(array('email' => $input['email'], 'password' => $input['password'], 'status' => User::STATUS_NORMAL), true)) {
            if (FALSE) {
                Event::fire('user.login.abnormal', array($input['email']));
            }
            // TODO 应该限制连续登录的IP
            if (\Config::get('hiho.security_policy.login_fail_lock_max_times') > -1) {
                $loginFailTimes = \Cache::get($loginFailKey, 0) + 1;
                \Cache::put($loginFailKey, $loginFailTimes, \Config::get('hiho.security_policy.login_fail_interval'));
                if (\Config::get('hiho.security_policy.login_fail_lock_max_times') != -1) { //开启锁定功能
                    $needLocked = $loginFailTimes >= \Config::get('hiho.security_policy.login_fail_lock_max_times');
                    if ($needLocked) {
                        $currentUser = User::where('email', $input['email'])->orwhere('mobile', $input['email'])->first();
                        if ($currentUser) {
                            $currentUser->status = User::STATUS_LOCKED;
                            $currentUser->save();
                            $userKv = new  UserKv();
                            $userKv->user_id = $currentUser->user_id;
                            $userKv->key = UserKv::LOGIN_FAIL_LOCKED_KEY;
                            $userKv->value = date('Y-m-d H:i:s', strtotime("+" . \Config::get('hiho.security_policy.lock_user_duration') . " minute"));
                            $userKv->save();
                        }
                        return $this->encodeResult('20202', 'Account has bean locked.');
                    }
                }
            }


            return $this->encodeResult('20201', 'Email or password incorrect, or user status is not normal.');
        }
        \Cache::forget($loginFailKey);

        $user = \User::find(\Auth::user()->user_id);
        $user->last_time = date('Y-m-d H:i:s');
        $user->last_ip = Request::getClientIp();
        $user->save();

        // 返回新生成的 Token
        $token = $this->newLoginToken(\Auth::user()->user_id, $input['client']);

        return $this->encodeResult('10100', 'succeed', array('token' => $token->toArray()));
    }

    /**
     * 返回当前用户资料
     * @author Luyu<luyu.zhang@autotiming.com> zhuzhengqian<zhuzhengqian@autotiming.com>
     */
    public function showMyProfile()
    {
        // 表单验证规则
        $input = \Input::only('token');
        $rules = array(
            'token' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $v = $this->verifyToken($input['token']);
        if (!$v) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }

        // 返回用户资料
        $user = \Auth::user();
        $userArr = $user->toArray();

        // 统计信息: 订阅数, 收藏数, 碎片数
        $fragment = Fragment::where('user_id', '=', $user->user_id)->get();
        if($fragment){
            foreach($fragment as $k=>&$f){
                if( ! \Video::find($f->video_id)){
                    unset($fragment[$k]);
                }
            }
        }
        $owner_count = array(
            'favorites' => Favorite::where("user_id", $user->user_id)->count(),
            'fragments' => count($fragment),
            'playlist'=> Playlist::where('user_id',$user->user_id)->count(),
            'share'=> FragmentShare::where('user_id',$user->user_id)->count()
            ##edu木有订阅
            ## 'subscriptions' => Subscription::where('user_id', '=', $user->user_id)->count()
        );

        $userArr['owner_count'] = $owner_count;

        return $this->encodeResult('10200', 'Succeed', $userArr);
    }

    /**
     *返回用户资料
     * @param user_id
     * @author zhuzhengqian
     */
    public function showProfile(){
        // 表单验证规则
        $input = \Input::only('user_id');
        $rules = array(
            'user_id' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        if( ! $objUser = \User::find($input['user_id'])){
            return $this->encodeResult('20206','the user is not exist',null);
        }
        unset($objUser->email);
        return $this->encodeResult('10200','success',$objUser->toArray()) ;
    }

    /**
     * 修改用户资料
     * @author zhuzhengqian
     */
    public function modify()
    {
        // 表单验证规则
        $input = \Input::only('token', 'email','nickname');
        $rules = array(
            'token' => 'required',
            'email'=>'required|email'
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份,返回Userid
        $userId = $this->verifyToken($input['token']);

        if (!$userId) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }
        ##check exist email
        $email = $input['email'];
        if(\User::whereRaw("email = '{$email}' and user_id != $userId ")->first()){
            return $this->encodeResult('20104','the email is aleady exist');
        }

        $user = \User::find(\Auth::user()->user_id);
        $user->email = $email;
        $user->nickname = addslashes($input['nickname']);
        $user->save();

        return $this->encodeResult('10200', 'success.',$user->toArray());
    }

    /**
     * 修改用户密码
     * @author zhuzhengqian
     */
    public function modifyPassword()
    {
        // 表单验证规则
        $input = \Input::only('token', 'originalPassword', 'newPassword');
        $rules = array(
            'token' => array('required'),
            'newPassword' => array('required', 'min:6', 'max:64')
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20202', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $v = $this->verifyToken($input['token']);
        if (!$v) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }
        $user = \User::find(\Auth::user()->user_id);
        if ( ! \Hash::check($input['originalPassword'], $user->password)) {
            return $this->encodeResult('20205', 'password is not correct');
        }
        $user->password = \Hash::make($input['newPassword']);
        $user->save();

        return $this->encodeResult('10200','user password modify success');

    }

    /**
     * 注销登录
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string
     */
    public function logout()
    {
        $input = \Input::only('token');
        $rules = array(
            'token' => array('required'),

        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $v = $this->verifyToken($input['token']);
        if (!$v) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }

        $token = \UserToken::where('token', $input['token'])->first();
        $token->delete();
        return $this->encodeResult('10203', 'Cancellation of success');
    }

    /**
     * 验证 Email 是否可注册
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string
     */
    public function verifyEmail()
    {
        $input = \Input::only('email');
        $rules = array(
            'email' => array('required'),

        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

    }

    /**
     * 获得 Token 信息
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string
     */
    public function queryTokenInfo()
    {
        $input = \Input::only('token');
        $rules = array(
            'token' => array('required'),

        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

    }

    /**
     * 生成新的 Token
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $user_id
     * @return \UserToken
     */
    private function newLoginToken($user_id, $client, $device_id = NULL)
    {
        $today = new \Datetime();
        $modifier = '+1 days';

        $token = new \UserToken();
        $token->token = uniqid();
        $token->user_id = $user_id;
        $token->client = $client;
        $token->caycle_at = $today->modify($modifier);
        $token->save();

        return $token;
    }

    /**
     * 第三方登录
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     * @param
     * @return \UserToken
     */
    public function openLogin(){
        $input = \Input::only('mode', 'token', 'uid', 'client');

        $mode = $input['mode'];
        $token = $input['token'];
        $uid = $input['uid'];
        $client = $input['client'];

        if ($mode == 'weibo') {//weibo
            $weibo = new Weibo();
            $data = $weibo->handler($token, $uid, $client);
            return $this->encodeResult($data['code'], $data['msg'], $data['token']);
        }
        elseif ($mode == 'facebook') {//Facebook
            $facebook = new Facebook();
            $data = $facebook->handler($token, $client);
            return $this->encodeResult($data['code'], $data['msg'], $data['token']);
        }
        else {
            return $this->encodeResult('20207', 'not supported mode');
        }
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
}
