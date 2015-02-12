<?php namespace HiHo\OpenAuth;

use HiHo\Model\User;

class Weibo extends OBase{

    /**
     * handle the token and uid, make the response
     * @author hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function handler($token, $uid, $client) {
        //get user info
        $userInfo = $this->getUserInfo($token, $uid);
        if (!$userInfo) {
            return $this->setReturnData('20205', 'Cannot get user by access_token and uid');
        }

        //get token info
        $tokenInfo = $this->getTokenInfo($token);
        if (!$tokenInfo) {
            return $this->setReturnData('20206', 'Cannot get token info by access_token');
        }

        $user_open = new \HiHo\Model\OpenLogin();
        $user = $user_open::where('open_id', '=', $userInfo['idstr'])->get()->first();
        //user has never binded
        if (empty($user)) {
            //add a new user
            $newID = $this->addNewUser($userInfo['screen_name']);
            $this->addOpenLoginUser($newID, $token, $userInfo['idstr'], $userInfo['screen_name'], time() + $tokenInfo['expire_in']);
            $token = $this->newLoginToken($newID, $client);
            return $this->setReturnData('10201', 'succeed', array('token' => $token->toArray()));
        }

        //user has binded, update token
        $this->updateOpenUser($user['user_id'], $token, $userInfo['screen_name'], time() + $tokenInfo['expire_in']);
        $token = $this->newLoginToken($user['user_id'], $client);
        return $this->setReturnData('10201', 'succeed', array('token' => $token->toArray()));
    }

    private function getUserInfo($token, $uid){
        $apiUrl = \Config::get('open_auth.weibo.get_user_info_url');
        $appKey = \Config::get('open_auth.weibo.app_key');
        $url = $apiUrl . '?source=' . $appKey . '&access_token=' . $token . '&uid=' . $uid;
        $res = $this->getCurlResponse($url, false);
        if (empty($res['id'])) {
            return false;
        }
        return $res;
    }

    private function getTokenInfo($token) {
        $apiUrl = \Config::get('open_auth.weibo.get_token_info_url');
        $data = 'access_token=' . $token;
        $token_info = $this->getCurlResponse($apiUrl, true, $data);
        if (empty($token_info['uid'])) {
            return false;
        }
        return $token_info;
    }

}