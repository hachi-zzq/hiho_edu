<?php

/**
 * 用户 MODEL
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class SewiseVideosInfo extends \Eloquent
{
    protected $table = 'sewise_videos_infos';

    protected $softDelete = true;

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];


    /*
     * ,自动一对多关系
     */
    public function sewise_videos_picture(){
        return $this->hasMany('SewiseVideosPicture','video_id');
    }

    /*
 * ,自动一对多关系
 */
    public function sewise_videos_subtitle(){
        return $this->hasMany('SewiseVideosSubtitle','video_id');
    }
}