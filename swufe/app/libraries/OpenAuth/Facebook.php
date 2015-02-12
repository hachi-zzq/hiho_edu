<?php namespace HiHo\OpenAuth;

use HiHo\Model\User;

class Facebook extends OBase {

    public function handler($token, $client) {
        //get user info
        $userInfo = $this->getUserInfo($token);
        if (!$userInfo) {
            return $this->setReturnData('20205', 'Cannot get user by access_token and uid');
        }
        $uid = $userInfo['id'];

        //get token info
        $tokenInfo = $this->getTokenInfo($token);
        if (!$tokenInfo) {
            return $this->setReturnData('20206', 'Cannot get token info by access_token');
        }
        $expire_at = $tokenInfo['data']['expires_at'];

        $user_open = new \HiHo\Model\OpenLogin();
        $user = $user_open::where('open_id', '=', $uid)->get()->first();
        if (empty($user)) {
            //add a new user
            $newID = $this->addNewUser($userInfo['name']);
            $this->addOpenLoginUser($newID, $token, $uid, $userInfo['name'], $expire_at);
            $token = $this->newLoginToken($newID, $client);
            return $this->setReturnData('10201', 'succeed', array('token' => $token->toArray()));
        }

        //user has binded, update token
        $this->updateOpenUser($user['user_id'], $token, $userInfo['name'], $expire_at);
        $token = $this->newLoginToken($user['user_id'], $client);
        return $this->setReturnData('10201', 'succeed', array('token' => $token->toArray()));
    }

    private function getUserInfo($token) {
        $apiUrl = \Config::get('open_auth.Facebook.get_user_info_url');
        $url = $apiUrl . '?access_token=' . $token;
        $res = $this->getCurlResponse($url, false);
        if (empty($res['id'])) {
            return false;
        }
        return $res;
    }

    private function getTokenInfo($token) {
        $apiUrl = \Config::get('open_auth.Facebook.get_token_info_url');
        $appAccessToken = \Config::get('open_auth.Facebook.app_access_token');

        //the first param input_token is the token you want to inspect
        //the second param access_token is Facebook App Access Token
        //the Facebook App Access Token need to be generated in other place or by calling API
        $url = $apiUrl . '?input_token=' . $token . '&access_token=' . $appAccessToken;
        $token_info = $this->getCurlResponse($url, false);
        if(empty($token_info['data']['expires_at'])){
            return false;
        }
        return $token_info;
    }
}