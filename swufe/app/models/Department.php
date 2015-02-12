<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 机构 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Department extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * 自动一对多
     */
    public function DepartmentTeacher()
    {
        return $this->hasMany('DepartmentTeacher', 'department_id');
    }


    public function parent()
    {
        return self::find($this->parent) ? self::find($this->parent) : NULL;
    }

    /**
     * 获得子树
     * @param null $subDepartments
     * @return array
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function getSubTree($subDepartments =null)
    {
        $tree = array();
        if ($subDepartments == null) {
            $subDepartments = Department::where('path', 'like', $this->path . $this->id . '%')->orderBy('path', 'asc')->get();
        }
        foreach($subDepartments as $index =>$sub){
            if($sub->parent == $this->id){
                $current = $sub;
                array_push($tree ,$current);
                unset($subDepartments[$index]);
                $current->child = $current->getSubTree($subDepartments);
            }
        }
        return $tree;
    }
    /**
     * 获得路径树
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function getPathTree()
    {
        $parentIds = explode('/', trim($this->path, '/'));
        $parentIds[] = $this->id;
        $tree = Category::wherein('id' , $parentIds)->orderBy('path', 'asc')->get();
        return $tree;
    }

    /**
     * 获得路径树字符串
     * @return string
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function getPathTreeStr()
    {
        $tree = $this->getPathTree();
        $str = '';
        foreach ($tree as $c) {
            if ($str) {
                $str .= ' > ';
            }
            $str .= $c->name;
        }
        return $str;
    }


    /**
     * @return mixed
     * @author zhuzhenqian
     */
    public function delete(){
        //department teacher
        \DepartmentTeacher::where('department_id',$this->id)->delete();
        return parent::delete();
    }
}