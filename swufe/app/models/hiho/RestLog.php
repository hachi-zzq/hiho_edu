<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * Rest 接口日志
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class RestLog extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
}