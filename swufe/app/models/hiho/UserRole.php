<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Database\Eloquent\Collection;


/**
 * Class UserRole
 * @package HiHo\Model
 * @author Haiming<haiming.wang@autotiming.com>
 */
class UserRole extends \Eloquent
{
    use SoftDeletingTrait;
    protected $dates = ['deleted_at'];

    protected $table = 'users_roles';

    public function user()
    {
        return $this->hasOne('User', 'user_id');
    }

    public function role()
    {
        return $this->hasOne('Role', 'role_id');
    }

}