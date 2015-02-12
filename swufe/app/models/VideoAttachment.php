<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 视频附件 Model
 * @author Zhengqian<zhu.zhengqian@autotiming.com>
 */
class VideoAttachment extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    protected $table = 'videos_attachments';
}