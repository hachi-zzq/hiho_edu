<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 收录请求 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class GatherRequest extends \Eloquent
{
    protected $table = 'gather_requests';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
}