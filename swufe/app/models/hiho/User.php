<?php namespace HiHo\Model;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 用户 MODEL
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class User extends \Eloquent implements UserInterface, RemindableInterface
{

    const STATUS_NORMAL = 'NORMAL'; //正常状态
    const STATUS_LOCKED = 'LOCKED'; //锁定状态-此时用户无法登陆系统
    const STATUS_UNBOUND = 'UNBOUND'; //未绑定账号状态-使用第三方登录过后但是没有绑定账号（邮箱或者手机号）

    const EMAIL_VALIDATED_YES = 1; //邮箱验证过
    const EMAIL_VALIDATED_NO = 0; //邮箱未验证

    const USER_ROLE_COMMON = 1;//普通用户角色ID

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $hidden = array('password');

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public function getStatusName()
    {
        /**
         * 0 无效用户
         * 100 正常用户
         * 101 仅订阅，未正式注册用户
         * 200 冻结
         * 201 新注册未验证 Email
         */
        return $this->status;
    }

    /**
     * 获得用户头像并考虑默认情况
     * @return string
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getAvatar()
    {
        return $this->avatar ? $this->avatar : '/static/hiho-edu/img/avatar_default.png';
    }

    /**
     * 自动多对多
     */
    public
    function favorites()
    {
        return $this->belongsToMany('Video', 'favorites', 'user_id', 'video_id')
            ->whereNull('favorites.deleted_at')
            ->withTimestamps();
    }

    /**
     * 多对多
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function roles()
    {
        return $this->belongsToMany('HiHo\Model\Role', 'users_roles', 'user_id', 'role_id');
    }

    /**
     * @return mixed
     * @author zhuzhengqian
     */
    public function delete(){
        //favorite
        \Favorite::where('user_id',$this->user_id)->delete();
        //fragement share
        \FragmentShare::where('user_id',$this->user_id)->delete();
        //playlist
        \Playlist::where('user_id',$this->user_id)->delete();
        //user token
        \UserToken::where('user_id',$this->user_id)->delete();
        return parent::delete();
    }

    /**
     *重写save方法，用来设置默认
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function save(array $options = Array())
    {
        if(!$this->avatar){
            $this->avatar = "/static/hiho-edu/img/avatar_default.png";
        }
        parent::save($options);
    }

}