<?php namespace HiHo\OpenAuth;

use HiHo\Model\User;

class OBase {

    /**
     * the return data structure
     */
    protected $retData = array(
        'code' => '',
        'msg' => '',
        'token' => array()
    );

    /**
     * set the return data
     * @author hanxiang<hanxiang.qiu@autotiming.com>
     */
    protected function setReturnData($code, $msg, $token = array()) {
        $this->retData['code'] = $code;
        $this->retData['msg'] = $msg;
        $this->retData['token'] = $token;
        return $this->retData;
    }

    protected function addNewUser($name) {
        $users = new User();
        $users->guid = \Uuid::v4();
        $users->email = $name;
        $users->password = \Hash::make('123456');
        $users->last_time = new \DateTime;
        $users->last_ip = \Request::getClientIp();
        $users->created_ip = \Request::getClientIp();
        $users->is_admin = 0;
        $users->status = User::STATUS_NORMAL;
        $users->save();
        return $users->user_id;
    }

    protected function addOpenLoginUser($newUserId, $token, $openId, $openName, $expire) {
        $user_open = new \HiHo\Model\OpenLogin();
        $user_open->user_id = $newUserId;
        $user_open->open_access_token = $token;
        $user_open->open_id = $openId;
        $user_open->open_name = $openName;
        $user_open->mode = 2;//Facebook
        $user_open->status = 1;
        $user_open->last_time = date('Y-m-d H:i:s', time());
        $user_open->expire = date('Y-m-d H:i:s', $expire);
        $user_open->extra = '';
        $user_open->save();
    }

    protected function updateOpenUser($userId, $token, $name, $expire) {
        $user_open = new \HiHo\Model\OpenLogin();
        $data = array(
            'open_access_token' => $token,
            'open_name' => $name,
            'last_time' => date('Y-m-d H:i:s', time()),
            'expire' => date('Y-m-d H:i:s', $expire)
        );
        $user_open->where('user_id', '=', $userId)->update($data);
    }

    protected function newLoginToken($user_id, $client)
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

    protected function getCurlResponse($url, $is_POST = true, $data = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($is_POST) {
            curl_setopt($ch, CURLOPT_POST, $is_POST);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $response = curl_exec($ch);

        if(curl_errno($ch)){
            //print curl_error($ch);
            return false;
        }

        curl_close($ch);

        $res_data = json_decode($response, true);
        return $res_data;
    }

}