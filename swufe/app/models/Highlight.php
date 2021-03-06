<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 分类 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Highlight extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

}