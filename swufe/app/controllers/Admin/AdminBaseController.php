<?php namespace HihoEdu\Controller\Admin;
use \Topic;

class AdminBaseController extends \BaseController
{

    /**
     *默认分页
     * pagesize
     */
    const PAGE_SIZE = 10;

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    /**
     *遍历所有院系
     * @param int $parentId
     * @return mixed
     * @author zhuzhengqian <zhengqian@autotiming.com>
     */
    public function getDepartment($parentId = 0)
    {
        $department = \Department::where('parent', '=', $parentId)->get();
        if ($department) {
            foreach ($department as $p) {
                $p->child = $p->getSubTree();
            }
        }
        return $department;
    }

    /**
     *遍历所有分类
     * @param int $parentId
     * @return mixed
     * @author zhuzhengqian <zhengqian@autotiming.com>
     */
    public function getCategories($parentId = 0)
    {
        $categories = \Category::where('parent', '=', $parentId)->orderBy('sort', 'asc')->get();
        if ($categories) {
            foreach ($categories as $p) {
                $p->child = $p->getSubTree();
            }
        }
        return $categories;
    }

    /**
     *遍历搜有主题
     * @param null
     * @author zhuzhengqian
     */
    public function getAllTopics(){
        return Topic::all();
    }

    /**
     * 遍历所有专业
     * @param int $parentId
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function getSpecialities($parentId = 0)
    {
        $specialities = \Speciality::where('parent', '=', $parentId)->get();
        if ($specialities) {
            foreach ($specialities as $p) {
                $p->child = $p->getSubTree();
            }
        }
        return $specialities;
    }

    /**
     *返回广告具体类型
     * @param $type
     * @return string
     * @author zhuzhengqian
     */
    public function advertisementType($type){
        !$type and \App::abort(403);
        switch($type){
            case 'picture':
                return '图片广告';
                break;
            case 'text':
                return '文字广告';
                break;
            case 'rotation':
                return '图片轮播';
                break;
            case 'code':
                return '代码广告';
                break;
            default:
                return '未知广告类型';
        }
    }
}
