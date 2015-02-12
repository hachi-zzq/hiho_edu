<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 分类 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class  Teacher extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * #自动一对多
     */
    public function DepartmentTeacher(){
        return $this->hasMany('DepartmentTeacher','teacher_id');
    }

    public function delete(){
        ## delete teacher department
        \DepartmentTeacher::where('teacher_id', $this->id)->delete();
        return parent::delete();
    }
}