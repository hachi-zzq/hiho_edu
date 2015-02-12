<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;


class RecommendPosition extends \Eloquent
{

    protected $table = 'recommend_positions';


    /**
     *
     * @return mixed
     */
    public function delete(){
        //删除该推荐位下的所有推荐
        \Recommend::where('position_id',$this->id)->delete();
        return parent::delete();

    }
}