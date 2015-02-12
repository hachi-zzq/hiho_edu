<?php

/**
 * 用户 MODEL
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class SewiseVideosSubtitle extends \Eloquent
{
    protected $softDelete = true;



    /*
     * |自动一对多
     */
    public function SewiseVideosInfo(){
        return $this->belongsTo('SewiseVideosInfo','video_id');
    }
}