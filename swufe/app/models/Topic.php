<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 分类 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Topic extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];


    /**
     * @return mixed
     * @author zhuzhengqian
     */
    public function delete(){
        //video topic
        \TopicVideo::where('topic_id',$this->id)->delete();
        return parent::delete();
    }
}