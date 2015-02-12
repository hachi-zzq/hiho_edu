<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 分类 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class VideosReference extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    protected $table = 'videos_references';
}