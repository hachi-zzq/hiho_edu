<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * Class DepartmentTeacher
 */
class  DepartmentTeacher extends \Eloquent
{

    protected $table = 'departments_teachers';

    /**
     * #自动关系
     */
    public function Department(){
        $this->belongsTo('Department','department_id');
    }

    /**
     * #自动关系
     */
    public function  Teacher(){
        $this->belongsTo('Department','teacher_id');
    }
}