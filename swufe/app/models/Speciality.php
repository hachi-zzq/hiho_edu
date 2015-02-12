<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Database\Eloquent\Collection;

/**
 * 专业实体类
 * Class Speciality
 * @package HiHo\Model
 * @author Haiming<haiming.wang@autotiming.com>
 */
class Speciality extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    public function parent()
    {
        return self::find($this->parent) ? self::find($this->parent) : NULL;
    }

    /**
     * 获得子树
     * @param null $subSpeciality
     * @return array
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function getSubTree($subSpeciality = null)
    {
        $tree = array();
        if ($subSpeciality == null) {
            $subSpeciality = Speciality::where('path', 'like', $this->path . $this->id . '%')->orderBy('path', 'asc')->get();
        }
        foreach ($subSpeciality as $index => $sub) {
            if ($sub->parent == $this->id) {
                $current = $sub;
                array_push($tree, $current);
                unset($subSpeciality[$index]);
                $current->child = $current->getSubTree($subSpeciality);
            }
        }
        return $tree;
    }

    /**
     * 获得专业路径树
     * @author Haiming<haiming.wang@autotiming.com>
     * @return array
     **/
    public function getPathTree()
    {
        $parentIds = explode('/', trim($this->path, '/'));
        $parentIds[] = $this->id;
        $tree = Speciality::wherein('id', $parentIds)->orderBy('path', 'asc')->get();
        return $tree;
    }

    /**
     * 删除
     * @author Haiming<haiming.wang@autotiming.com>
     * @return array
     **/
    public function delete()
    {
        VideoSpeciality::where('speciality_id', $this->id)->delete();
        return parent::delete();
    }

    /**
     * 获得专业路径树字符串
     * @author Haiming<haiming.wang@autotiming.com>
     * @return string
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

}