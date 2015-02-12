<?php namespace HihoEdu\Controller\Admin;

use HiHo\Other\Pinyin;

/**
 * Class SpecialityController
 * @package HihoEdu\Controller\Admin
 * @author Haiming<haiming.wang@autotiming.com>
 */
class SpecialityController extends AdminBaseController
{

    /**
     * 专业列表
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function index()
    {
        $specialities = $this->getSpecialities();
        return \View::make('admin.speciality.index', compact('specialities'));
    }

    /**
     * 添加专业
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function addShow()
    {
        $specialities = $this->getSpecialities();
        return \View::make('admin.speciality.bs3_create', compact('specialities'));
    }

    /**
     * post添加注释
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function addPost()
    {
        $postData = \Input::only('speciality', 'name');
        $parentId = 0;//$postData['speciality'];
        $rule = array(
            'name' => array('required'),
        );
        $validator = \Validator::make($postData, $rule);
        if ($validator->fails()) {
            $message = $validator->messages();
            return \Redirect::to('/admin/speciality/add')->with('error_tips', $message->first());
        }
        //check exist
        if (\Speciality::where('name', $postData['name'])->first()) {
            return \Redirect::to('/admin/speciality/add')->with('error_tips', '该专业已经存在');
        }
        $pinyin = new Pinyin();
        $permalink = $pinyin->output($postData['name']);
        if ($objDeleted = \Speciality::withTrashed()->where('permalink', $permalink)->first()) {
            $objDeleted->deleted_at = NULL;
            $objDeleted->name = addslashes($postData['name']);
            $objDeleted->parent = $parentId;
            $objDeleted->save();
            return \Redirect::to('/admin/specialities')->with('success_tips', "专业创建成功！");
        }


        $path = '';
        if ($parentId == 0) {
            $path = '/';
        } else {
            $parent = \Speciality::find($parentId);
            if (!$parent) {
                return \Redirect::to('/admin/speciality/add')->with('error_tips', '上级专业不存在');
            }
            $path = rtrim($parent->path, '/') . '/' . $parentId;
        }
        ## save in mysql
        $department = new \Speciality();

        $department->permalink = $permalink;
        $department->parent = $parentId;
        $department->name = addslashes($postData['name']);
        $department->path = $path;
        $department->save();
        return \Redirect::to('/admin/specialities')->with('success_tips', '专业创建成功！');

    }


    /**
     *删除分类
     * @param int $id
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function delete($id = NULL)
    {
        empty($id) and \App::abort(403, 'speciality is required');
        $objSpeciality = \Speciality::find($id);
        empty($objSpeciality) and \App::abort(404, 'speciality is not found');
        $childCount = \Speciality::where('parent', '=', $objSpeciality->id)->count();
        if ($childCount > 0) {
            return \Redirect::to('/admin/specialities')->with('error_tips', '专业删除失败，专业下含有子专业');
        }
        $objSpeciality->delete();
        return \Redirect::to('/admin/specialities')->with('success_tips', '专业删除成功');
    }

    /**
     *修改分类
     * @param int $id
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function modify($id = NULL)
    {
        $id = $id ? $id : \Input::get('id');
        empty($id) and \App::abort(403, 'speciality is required');
        $objSpeciality = \Speciality::find($id);
        ##do post modify
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = \Input::only('speciality', 'name');
            $pSpeciality = 0;//$postData['speciality'];
            $rule = array(
                'name' => array('required'),
            );
            $validator = \Validator::make($postData, $rule);
            if ($validator->fails()) {
                $message = $validator->messages();
                return \Redirect::to('/admin/speciality/modify/' . $postData['id'])->with('error_tips', $message->first());
            }
            $parentId = $postData['speciality'];
            $path = '';
            if ($parentId == 0) {
                $path = '/';
            } else {
                if ($pSpeciality == $id) {
                    return \Redirect::to('/admin/speciality/modify/')->with('error_tips', '上级专业不能是本身');
                }
                $parent = \Speciality::find($postData['speciality']);
                if (!$parent) {
                    return \Redirect::to('/admin/speciality/modify/')->with('error_tips', '上级专业不存在');
                }
                $path = rtrim($parent->path, '/') . '/' . $parentId;
            }

            $objSpeciality->name = addslashes($postData['name']);
            $objSpeciality->parent = $pSpeciality;
            $objSpeciality->path = $path;
            $objSpeciality->save();
            return \Redirect::to('/admin/specialities')->with('success_tips', '专业修改成功');
        }
        empty($objSpeciality) and \App::abort(404, 'speciality is not found');
        $specialities = $this->getSpecialities();
        return \View::make('admin.speciality.modify', compact('objSpeciality', 'specialities'));
    }

    /**
     * 获取子专业列表
     * @param $specialityId
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function slaveSpecialityIndex($specialityId)
    {
        empty($specialityId) and \App::abort(403);
        $specialities = \Speciality::where('parent', $specialityId)->get();
        return \View::make('admin.speciality.slave_index', compact('specialities'));
    }

}