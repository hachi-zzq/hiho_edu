<?php namespace HihoEdu\Controller\Admin;

use HiHo\Other\Pinyin;
use HiHo\Model\Video;

/**
 * Created with JetBrains PhpStorm.
 * User: zhu
 * DateTime: 14-6-23 上午11:04
 * Email:www321www@126.com
 */
class CategoryController extends AdminBaseController
{


    /**
     *分类列表
     * @return mixed
     * @author zhuzhengqian
     */
    public function index()
    {
        $categories = $this->getCategories();
        return \View::make('admin.category.index', compact('categories'));
    }

    /**
     *添加分类
     * @return mixed
     * @author zhuzhengqian
     */
    public function addShow()
    {
        ##get departemtn
        $categories = $this->getCategories();
        return \View::make('admin.category.bs3_create', compact('categories'));
    }

    /**
     *post添加注释
     * @return mixed
     * @author zhuzhengqian
     */
    public function addPost()
    {
        $postData = \Input::all();
        $rule = array(
            'name' => array('required'),
            'access_level' => array('integer','min:0'),
            'sort' => array('integer','min:1')
        );
        $validator = \Validator::make($postData, $rule);
        if ($validator->fails()) {
            $message = $validator->messages();
            return \Redirect::to('/admin/category/add')->with('error_tips',$message->first());
        }
        //check exist
        if(\Category::where('name',$postData['name'])->first()){
            return \Redirect::to('/admin/category/add')->with('error_tips','该分类已经存在');
        }
        $sort = $postData['sort'] ? $postData['sort'] : 0;

        $pinyin = new Pinyin();
        $permalink = $pinyin->output($postData['name']);
        if ($objDeleted = \Category::withTrashed()->where('permalink', $permalink)->first()) {
            $objDeleted->deleted_at = NULL;
            $objDeleted->name = addslashes($postData['name']);
            $objDeleted->parent = $postData['category'];
            $objDeleted->save();
            return \Redirect::to('/admin/categories')->with('success_tips', "分类创建成功！");
        }

        $parentId = $postData['category'];
        $path = '';
        if($parentId==0){
            $path = '/';
        }else{
            $parent = \Category::find($postData['category']);
            if(!$parent){
                return \Redirect::to('/admin/category/add')->with('error_tips','上级分类不存在');
            }
            $path = rtrim($parent->path, '/') . '/' . $parentId;
        }
        ## save in mysql
        $department = new \Category();

        $department->permalink = $permalink;
        $department->parent = $postData['category'];
        $department->name = addslashes($postData['name']);
        $department->path = $path;
        $department->sort = $sort;
        $department->access_level = $postData['access_level']==null ?0:$postData['access_level'];
        $department->save();
        ##success redirect
        return \Redirect::to('/admin/categories')->with('success_tips', '分类创建成功！');

    }


    /**
     *删除分类
     * @param int $id
     * @return mixed
     *@author zhuzhengqian
     */
    public function delete($id = NULL)
    {
        empty($id) and \App::abort(403, 'category is required');
        $objCat = \Category::find($id);
        empty($objCat) and \App::abort(404, 'category is not found');
        ##判断是否还有子分类
        $objSlave = \Category::where('parent',$objCat->id)->get();
        if(count($objSlave)){
            return \Redirect::to('/admin/categories')->with('error_tips', '改分类下包含分类，请先删除子分类再继续操作');
        }
        $objCat->delete();
        return \Redirect::to('/admin/categories')->with('success_tips', '分类删除成功');
    }

    /**
     *修改分类
     * @param int $id
     * @return mixed
     * @author zhuzhengqian
     */
    public function modify($id = NULL)
    {
        $id = $id ? $id : \Input::get('id');
        empty($id) and \App::abort(403, 'category is required');
        $objCategory = \Category::find($id);
        ##do post modify
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = \Input::only('category', 'name', 'reset_video','access_level','sort');
            $rule = array(
                'name' => array('required'),
                'access_level' => array('integer','min:0'),
                'order' => array('integer','min:1')
            );
            $validator = \Validator::make($postData, $rule);
            if ($validator->fails()) {
                ##redirect
                $message = $validator->messages();
                return \Redirect::to('/admin/category/modify/' . $postData['id'])->with('error_tips',$message->first());
            }
            $sort = $postData['sort'] ? $postData['sort'] : 0;
            $parentId = $postData['category'];
            $path = '';
            if($parentId==0){
                $path = '/';
            }else{
                if ($postData['category'] == $id) {
                    return \Redirect::to('/admin/category/modify/')->with('error_tips','上级分类不能是本身');
                }
                $parent = \Category::find($postData['category']);
                if(!$parent){
                    return \Redirect::to('/admin/category/modify/')->with('error_tips','上级分类不存在');
                }
                $path = rtrim($parent->path, '/') . '/' . $parentId;
            }

            $access_level =  $postData['access_level']==null ?0:$postData['access_level'];
            $objCategory->name = addslashes($postData['name']);
            $objCategory->parent = $postData['category'];
            $objCategory->path = $path;
            $objCategory->sort = $sort;
            $objCategory->access_level = $access_level;
            $objCategory->save();
            if($postData['reset_video']=='on'){//影响该分类下视频的访问等级
                Video::resetAccessLevelByCategory($access_level, $id);
            }
            return \Redirect::to('/admin/categories')->with('success_tips', '分类修改成功');
        }

        ##get modify

        empty($objCategory) and \App::abort(404, 'category is not found');
        $categories = $this->getCategories();
        return \View::make('admin.category.modify', compact('objCategory', 'categories'));
    }

    /**
     *获取子分类列表
     * @param $categoryId
     * @return mixed
     * @author zhuzhengqian
     */
    public function slaveCategoryIndex($categoryId){
        empty($categoryId) and \App::abort(403);
        $categories = \Category::where('parent',$categoryId)->get();
        return \View::make('admin.category.slave_index', compact('categories'));
    }

}