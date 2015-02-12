<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use HiHo\Search\IncrementUpdate;

/**
 * 视频多语言信息 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class VideoInfo extends \Eloquent
{

    protected $table = 'videos_info';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * 绑定加载事件
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public static function boot() {
        parent::boot();

        // 新增视频
        self::created(function ($videoInfo) {
            IncrementUpdate::videoCreated($videoInfo->video_id);
        });

        // 删除视频
        self::deleted(function ($videoInfo) {
            IncrementUpdate::videoDeleted($videoInfo->video_id);
        });
    }

    public function video()
    {
        return $this->belongsTo('Video');
    }

}