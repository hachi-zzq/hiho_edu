<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 收藏 MODEL
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Favorite extends \Eloquent
{
    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
    
    public function video()
    {
        return $this->hasOne('Video', 'video_id', 'video_id');
    }

    public function fragment()
    {
        return $this->hasOne('Fragment', 'id', 'fragment_id');

    }

    /**
     * @return mixed
     * @author zhzhengqian
     */
    public function delete(){
        return parent::delete();
    }


}