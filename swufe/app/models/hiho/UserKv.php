<?php namespace HiHo\Model;

/**
 * 用户 Key-Value 模型
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class UserKv extends \Eloquent
{

    protected $table = 'users_kv';

    const LOGIN_FAIL_TIMES_KEY = 'login.fail.times';
    const LOGIN_FAIL_LOCKED_KEY = 'login.fail.locked.deadline';
    const PASSWORD_FORGOT_POST_TIMES_KEY = 'password.forgot.post.times';

    const USER_LAST_FORGOT_IP = 'user.last.forgot.ip';
    const USER_EMAIL_ACTIVATE_TOKEN = 'user.email.activate.token';

    public static function getByUserAndKey($userId, $key)
    {
        return UserKv::where('user_id', $userId)->where('key', $key)->first();
    }
}