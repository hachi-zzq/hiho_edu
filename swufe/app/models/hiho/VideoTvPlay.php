<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 视频电视台播放记录 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class VideoTvPlay extends \Eloquent
{
    protected $table = 'videos_tv_plays';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

}