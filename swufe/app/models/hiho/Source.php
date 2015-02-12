<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 来源 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Source extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
}