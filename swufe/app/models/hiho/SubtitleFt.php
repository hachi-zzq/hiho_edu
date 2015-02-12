<?php namespace HiHo\Model;

/**
 * 字幕全文 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class SubtitleFt extends \Eloquent
{

    protected $table = 'subtitles_fulltext';

    public function video()
    {
        return $this->belongsTo('Video');
    }

    public function subtitle()
    {
        return $this->belongsTo('Subtitle');
    }
}