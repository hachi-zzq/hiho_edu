<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Database\Eloquent\Collection;

/**
 * 分类 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Category extends \Eloquent
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    public function parent()
    {
        return self::find($this->parent) ? self::find($this->parent) : NULL;
    }

    /**
     * 获得子树
     * @param null $subCategory
     * @return array
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function getSubTree($subCategory =null)
    {
        $tree = array();
        if ($subCategory == null) {
            $subCategory = Category::where('path', 'like', $this->path . $this->id . '%')->orderBy('path', 'asc')->orderBy('sort', 'asc')->get();
        }
        foreach($subCategory as $index =>$sub){
            if($sub->parent == $this->id){
                $current = $sub;
                array_push($tree ,$current);
                unset($subCategory[$index]);
                $current->child = $current->getSubTree($subCategory);
            }
        }
        return $tree;
    }

    /**
     * 获得分类路径树
     * @author Luyu<luyu.zhang@autotiming.com>
     * @modify by Haiming
     * @return array
     ***/

    public function getPathTree()
    {
        $parentIds = explode('/', trim($this->path, '/'));
        $parentIds[] = $this->id;
        $tree = Category::wherein('id' , $parentIds)->orderBy('path', 'asc')->get();
        return $tree;
    }

    /**
     * 获得分类路径树字符串
     * @author Luyu<luyu.zhang@autotiming.com>
     * @modify by Haiming
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

    /**
     *
     * @return mixed
     * @author zhuzhengqian
     */
    public function delete(){
        //delete video category
        \VideoCategory::where('category_id',$this->id)->delete();
        return parent::delete();
    }

}