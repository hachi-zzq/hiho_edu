<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Role
 * @package HiHo\Model
 * @author Haiming<haiming.wang@autotiming.com>
 */
class Role extends \Eloquent
{
    use SoftDeletingTrait;
    protected $dates = ['deleted_at'];
    protected $table = 'roles';

}