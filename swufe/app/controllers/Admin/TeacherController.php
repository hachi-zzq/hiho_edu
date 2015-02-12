<?php namespace HihoEdu\Controller\Admin;

use HiHo\Other\Pinyin;
use \TeacherVideo;
class TeacherController extends AdminBaseController
{

    /**
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return mixed
     */
    public function getIndex()
    {
        $teachers = \Teacher::paginate(25);
        if($teachers){
            //统计其他信息
            foreach($teachers as $teacher){
                $teacher->count = TeacherVideo::where('teacher_id',$teacher->id)->count();
            }
        }
        return \View::make('admin.teacher.index', compact('teachers'));
    }

    public function getCreate()
    {
        $departments = $this->getDepartment();
        return \View::make('admin.teacher.create', compact('departments'));
    }


    /**
     * 添加讲师
     * @return mixed
     * @author zhuzhengqian
     */
    public function postCreate()
    {

        $subDir = date('Ymd');
        $targetDir = public_path() . '/upload/' . $subDir;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777);
        }
        $postData = \Input::all();
        ##validator
        $rule = array(
            'teacher_name' => 'required'
        );
        $validator = \Validator::make($postData, $rule);
        if ($validator->fails()) {
            return \Redirect::to('/admin/teachers/create')->with('error_tips', 'name is required')->withInput();
        }
        ##check exist  name
        $pinyin = new Pinyin();
        $pinyinName = $pinyin->output($postData['teacher_name']);
        if (\Teacher::where('name', '=', $postData['teacher_name'])->first()) {
            return \Redirect::to('/admin/teachers/create')->with('error_tips', 'name is already exist')->withInput();
        }
//            ##check permalink
        if (\Teacher::withTrashed()->where('permalink', '=', $pinyinName)->first()) {
            ## add permalink random str
            $pinyinName = $pinyinName . str_random(5);
        }
        ##check email
        if (!empty($postData['email']) && !filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
            return \Redirect::to('/admin/teachers/create')->with('error_tips', 'email format is error')->withInput();
        }

        $fileData = \Input::file('portrait');
        if ($fileData) {
            $allowExt = array('png', 'jpeg', 'bmp', 'gif');
            //check extension
            $ext = $fileData->guessExtension();
            if (!in_array($ext, $allowExt)) {
                return \Redirect::to('/admin/teachers/create')->with('error_tips', 'file type is illegal');
            }
            $realName = $fileData->getClientOriginalName();
            $randomName = date('His') . rand(1, 100) . '.' . $ext;
            //move
            $fileData->move($targetDir, $randomName);
        }

        ##save in mysql
        $objTeacher = new \Teacher();
        $objTeacher->name = addslashes($postData['teacher_name']);
        if ($fileData) {
            $objTeacher->portrait_src = '/upload/' . $subDir . '/' . $randomName;
        }

        $objTeacher->permalink = $pinyinName;
        $objTeacher->description = addslashes($postData['description']);
        $objTeacher->title = addslashes($postData['title']);
        $objTeacher->email = addslashes($postData['email']);
        $objTeacher->save();
        ##save in depart teacher
        $objDepartmentTeacher = new \DepartmentTeacher();
        $objDepartmentTeacher->teacher_id = $objTeacher->id;
        $objDepartmentTeacher->department_id = $postData['department'];
        $objDepartmentTeacher->save();
        return \Redirect::to('/admin/teachers')->with('success_tips', '操作成功');

    }

    /**
     *获取二级院系
     * @return string
     * @author zhuzhengqian
     */
    public function getSubDepartment()
    {
        $id = \Input::get('id');
        ##get sub
        $objSub = \Department::where('parent', '=', $id)->get();
        return json_encode($objSub);
    }

    /**
     *删除讲师
     * @param null $id
     * @return mixed
     * @author zhuzhengqian
     */
    public function getDestroy($id = NULL)
    {
        empty($id) and \App::abort('404', 'teacher_id is required');
        $objTeacher = \Teacher::find($id);
        empty($objTeacher) and \App::abort('404', 'Teacher is not found');
        $objTeacher->delete();

        return \Redirect::to('/admin/teachers')->with('success_tips', '删除成功');

    }


    /**
     *修改讲师
     * @param int $id
     * @return mixed
     * @author zhuzhengqian
     */
    public function modify($id = NULL)
    {
        empty($id) and \App::abort(403, 'id is required');
        $objTeacher = \Teacher::find($id);
        empty($objTeacher) and \App::abort(404, 'not found');
        $departments = $this->getDepartment();
        ##get belongs departemtn
        $departmentId = \DepartmentTeacher::where('teacher_id', '=', $id)->first()->department_id;
        $objDepartment = \Department::find($departmentId);
        return \View::make('admin.teacher.modify', compact('objTeacher', 'departments', 'objDepartment'));
    }

    /**
     *修改讲师post
     * @return mixed
     * @author zhuzhengqian
     */
    public function modifyPost()
    {
        $subDir = date('Ymd');
        $targetDir = public_path() . '/upload/' . $subDir;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777);
        }
        $postData = \Input::all();
        ##validator
        $rule = array(
            'teacher_name' => 'required'
        );
        $validator = \Validator::make($postData, $rule);
        if ($validator->fails()) {
            return \Redirect::to('/admin/teachers/modify' . $postData['id'])->with('error_tips', 'name is required')->withInput();
        }
        ##check email
        if (!empty($postData['email']) && !filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
            return \Redirect::to('/admin/teachers/modify/' . $postData['id'])->with('error_tips', 'email format is error')->withInput();
        }
        $fileData = \Input::file('portrait');
        if ($fileData) {
            $allowExt = array('png', 'jpeg', 'bmp', 'gif');
            //check extension
            $ext = $fileData->guessExtension();
            if (!in_array($ext, $allowExt)) {
                return \Redirect::to('/admin/teachers/modify/' . $postData['id'])->with('error_tips', 'file type is illegal');
            }
            $realName = $fileData->getClientOriginalName();
            $randomName = date('His') . rand(1, 100) . '.' . $ext;
            //move
            $fileData->move($targetDir, $randomName);
        }

        ##save in mysql
        $objTeacher = \Teacher::find($postData['id']);
        $objTeacher->name = addslashes($postData['teacher_name']);
        if ($fileData) {
            $objTeacher->portrait_src = '/upload/' . $subDir . '/' . $randomName;
        }
        $objTeacher->permalink = $postData['permalink'];
        $objTeacher->description = addslashes($postData['description']);
        $objTeacher->title = addslashes($postData['title']);
        $objTeacher->email = addslashes($postData['email']);
        $objTeacher->save();
        ##save in depart teacher
        $objDepartmentTeacher = \DepartmentTeacher::where('teacher_id', '=', $postData['id'])->first();
        $objDepartmentTeacher->department_id = $postData['department'];
        $objDepartmentTeacher->save();
        return \Redirect::to('/admin/teachers')->with('tips', '操作成功');
    }

    /**
     *取得院系下讲师
     * @param $departmentId
     * @author zhuzhengqian
     */
    function getDepartmentTeacher(){
        $departmentId = \Input::get('department_id');
        if($departmentId){
            $obj = \DepartmentTeacher::where('department_id',$departmentId)->get();
        }else{
            $obj = \DepartmentTeacher::all();
        }

        if($obj){
            $arrTeacher = array();
            foreach($obj as $departmentTeacher){
                array_push($arrTeacher,$departmentTeacher->teacher_id);
            }
            if($arrTeacher){
                $ids = implode(',',$arrTeacher);
            }else{
                $ids = -1;
            }
        }

        $objTeacher = \Teacher::whereRaw("id in ($ids)")->get();
        echo json_encode(array('messageCode'=>0,'message'=>'success','data'=>$objTeacher));
    }

    /**
     *首页讲师推荐
     * @author zhuzhengqian
     */
    public function indexRecommend(){
        $input = \Input::all();
        if($input['recommend_position']){
            $objRec = \Recommend::where('place','index')->where('type','teachers')->first();
            if($objRec)
                $ids = $objRec->content_ids;
            if(isset($ids) && $ids!=''){
                $arrIds = unserialize($ids);
                if( ! $arrIds){
                    $arrIds = array();
                }
            }

            $objRec->content_ids = serialize(array_unique(array_merge($input['check_id'],$arrIds)));
            $objRec->save();
        }
        return \Redirect::to('/admin/teachers')->with('success_tips', '操作成功');
    }

}