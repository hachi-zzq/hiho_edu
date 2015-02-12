<?php

/**
 * 用户 MODEL
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class SewiseVideosPicture extends \Eloquent
{
    protected $table = 'sewise_videos_pictures';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];


    /*
     * |自动一对多
     */
    public function SewiseVideosInfo(){
        return $this->belongsTo('SewiseVideosInfo','video_id');
    }

}