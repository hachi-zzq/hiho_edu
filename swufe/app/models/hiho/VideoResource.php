<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 视频资源 Model
 * @author ZhuJun<jun.zhu@autotiming.com>
 */
class VideoResource extends \Eloquent
{
    protected $table = 'videos_resources';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
}