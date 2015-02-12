<?php


/**
 * 视频-专业关系实体
 * Class VideoSpeciality
 * @package HiHo\Model
 * @author Haiming<haiming.wang@autotiming.com>
 */
class VideoSpeciality extends \Eloquent
{
    protected $table = 'videos_specialities';

    public function info()
    {
        return $this->hasOne('VideoInfo', 'video_id');
    }

    public function pic()
    {
        return $this->hasOne('videos_pictures', 'video_id');
    }


}