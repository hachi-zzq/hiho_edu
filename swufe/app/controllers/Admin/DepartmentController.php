<?php namespace HihoEdu\Controller\Admin;

use HiHo\Other\Pinyin;

class DepartmentController extends AdminBaseController
{

    /**
     * 院系机构列表
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return mixed
     */
    public function getIndex()
    {

        $departments = $this->getDepartment();
        if($departments){
            foreach($departments as $depart){
                $depart->teacherCount = \DepartmentTeacher::where('department_id',$depart->id)->count();
            }
        }
        return \View::make('admin.department.index', compact('departments'));
    }

    /**
     *子集院系
     * @param $parentId
     * @return mixed
     * @author zhuzhengqian
     */
    public function slaveDepartmentIndex($parentId){
        $salve = \Department::where('parent',$parentId)->get();
        if($salve){
            foreach($salve as $depart){
                $depart->teacherCount = \DepartmentTeacher::where('department_id',$depart->id)->count();
            }
        }
        return \View::make('admin.department.salve_index',compact('salve'));
    }

    /**
     *添加院系(get)
     * @return mixed
     * @author zhuzhengqian
     */
    public function getCreate()
    {
        ##get departemtn
        $departments = $this->getDepartment();
        return \View::make('admin.department.create', compact('departments'));
    }

    /**
     *添加院系post
     * @return mixed
     * @author zhuzhengqian
     */
    public function postCreate()
    {
        $postData = \Input::all();
        $rule = array(
            'name' => array('required'),
        );
        $validator = \Validator::make($postData, $rule);
        if ($validator->fails()) {
            ##redirect
            return \Redirect::to('/admin/departments/create')->withErrors($validator)->withInput();
        }
        ## save in mysql
        $department = new \Department();
        $permalink = trim($postData['permalink']);
        if (empty($permalink)) {
            $pinyin = new Pinyin();
            $permalink = $pinyin->output($postData['name']);
        }else{
            $permalinkPattern = "/^(?![0-9])[A-Za-z_0-9]+$/i";
            if (!preg_match($permalinkPattern, $permalink)) {
                return \Redirect::to('/admin/departments/create')->with('error_tips','固定标识只允许数字、英文字母和下划线，且不能以数字开头');
            }
        }
        $parentId = $postData['departments'];
        $path = '';
        if($parentId==0){
            $path = '/';
        }else{
            $parent = \Department::find($postData['departments']);
            if(!$parent){
                return \Redirect::to('/admin/departments/create')->with('error_tips','上级机构不存在');
            }
            $path = rtrim($parent->path, '/') . '/' . $parentId;
        }
        $department->permalink = $permalink;
        $department->parent = $postData['departments'];
        $department->name = addslashes($postData['name']);
        $department->description = addslashes($postData['description']);
        $department->path = $path;
        $department->save();

        ##success redirect
        return \Redirect::to('/admin/departments')->with('success_tips', 'add department success');

    }

    /**
     *删除院系
     * @param int $id
     * @return mixed
     * @author zhuzhengqian
     */
    public function getDestroy($id = NULL)
    {
        empty($id) and \App::abort(403, 'department is required');
        $objDepartment = \Department::find($id);
        empty($objDepartment) and \App::abort(404, 'department is not found');
        ##判断是否是一级还子集
        $objSlave = \Department::where('parent',$objDepartment->id)->get();
        if (count($objSlave)) {
            ##parent
            return \Redirect::to('/admin/departments')->with('error_tips', '此部门下包含二级部分，请删除后在操作');
        }

        $objDepartment->delete();
        return \Redirect::to('/admin/departments')->with('success_tips', '部门删除成功');
    }

    /**
     *修改院系信息
     * @param int $id
     * @return mixed
     * @author zhuzhengqian
     */
    public function modify($id = NULL)
    {
        $id = $id ? $id : \Input::get('id');
        empty($id) and \App::abort(403, 'department is required');
        $objDepartment = \Department::find($id);
        ##do post modify
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = \Input::all();
            $rule = array(
                'name' => array('required'),
            );
            $validator = \Validator::make($postData, $rule);
            if ($validator->fails()) {
                ##redirect
                return \Redirect::to('/admin/departments/modify/' . $postData['id'])->withErrors($validator)->withInput();
            }
            $parentId = $postData['departments'];
            $path = '';
            if($parentId==0){
                $path = '/';
            }else{
                if ($postData['departments'] == $id) {
                    return \Redirect::to('/admin/departments/modify/'. $postData['id'])->with('error_tips','上级机构不能是本身');
                }
                $parent = \Department::find($postData['departments']);
                if(!$parent){
                    return \Redirect::to('/admin/departments/modify/'. $postData['id'])->with('error_tips','上级机构不存在');
                }
                $path = rtrim($parent->path, '/') . '/' . $parentId;
            }
            $permalink = trim($postData['permalink']);
            if (empty($permalink)) {
                $pinyin = new Pinyin();
                $permalink = $pinyin->output($postData['name']);
            }else{
                $permalinkPattern = "/^(?![0-9])[A-Za-z_0-9]+$/i";
                if (!preg_match($permalinkPattern, $permalink)) {
                    return \Redirect::to('/admin/departments/modify/'. $postData['id'])->with('error_tips','固定标识只允许数字、英文字母和下划线，且不能以数字开头');
                }
            }
            $objDepartment->name = addslashes($postData['name']);
            $objDepartment->permalink = $permalink;
            $objDepartment->description = addslashes($postData['description']);
            $objDepartment->parent = $postData['departments'];
            $objDepartment->save();
            return \Redirect::to('/admin/departments')->with('success_tips', 'department modify success');
        }

        ##get modify

        empty($objDepartment) and \App::abort(404, 'department is not found');
        $departments = $this->getDepartment();
        return \View::make('admin.department.modify', compact('objDepartment', 'departments'));
    }


}