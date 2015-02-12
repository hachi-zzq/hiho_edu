<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 视频图片 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class VideoPicture extends \Eloquent
{
    protected $table = 'videos_pictures';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

}