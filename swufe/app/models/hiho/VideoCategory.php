<?php namespace HiHo\Model;

/**
 * 视频分类关系 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class VideoCategory extends \Eloquent
{
    protected $table = 'videos_categories';

    public function info()
    {
        return $this->hasOne('VideoInfo', 'video_id');
    }

    public function pic()
    {
        return $this->hasOne('videos_pictures', 'video_id');
    }


}