<?php
use \Advertisement;

class AdPosition extends \Eloquent {


    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    protected $table = 'ad_positions';

    /**
     * #重写的delete
     * @return mixed
     */
    public function delete(){
        //advertise
       Advertisement::where('position_id',$this->id)->delete();
       return parent::delete();
    }


}