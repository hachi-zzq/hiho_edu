<?php namespace HiHo\Model;

/**
 * 标签 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Tag extends \Eloquent
{
    /**
     * @return mixed
     * @author zhuzhengqian
     */
    public function delete(){
        //fragment tag
        \FragmentTag::where('tag_id',$this->id)->delete();
        //video tag
        \VideoTag::where('tag_id',$this->id)->delete();
        return parent::delete();
    }
}