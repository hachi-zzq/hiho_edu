<?php

class DepartmentController extends BaseController
{

    /**
     * 院系列表
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function index()
    {

        $departments = Department::whereRaw("parent = 0 AND deleted_at IS NULL")->paginate(8);
        if ($departments) {
            foreach ($departments as $d) {
                $d->subDepartments = Department::whereRaw("parent = ? AND deleted_at IS NULL", array($d->id))->get();
                $departmentTeachers = DepartmentTeacher::whereRaw("department_id = ?", array($d->id))->get();
                $teachers = array();
                foreach ($departmentTeachers as $k => $dt) {
                    $teachers[$k] = array();
                    $teachers[$k] = Teacher::find($dt->teacher_id);
                }
                $d->teachers = $teachers;
            }
        }
        return View::make('departments')->with('departments', $departments);
    }

    /**
     * 院系详情
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function detail($department_id)
    {
        $department = Department::where('id', '=', $department_id)->first();

        if (empty($department)) {
            return View::make('no_exist')->with('data', '院系');
        }


        $parentDepartmentId = Department::find($department_id)->parent;
        $department->departments = Department::where('parent', $parentDepartmentId)->get();

        $teachersOfDepartment = DepartmentTeacher::where('department_id', $department_id)->get();

        $teachers = array();
        foreach ($teachersOfDepartment as $k => $td) {
            $teacher = Teacher::where('id', '=', $td->teacher_id)->first();

            // $userInfo = User::where('user_id', '=', $teacher->user_id)->first();
            // if ($userInfo) {
            //     $teacher->avatar = $userInfo->avatar;
            // }
            // else {
            //     $teacher->avatar = '/source/dist/img/avatar_default.png';
            // }
            
            $teacher->departments = $departmentsOfTeacher = DepartmentTeacher::where('teacher_id', '=', $td->teacher_id)->get();
            foreach ($teacher->departments as $dt) {
                $departmentOfTeacher = Department::where('id', '=', $dt->department_id)->first();
                $dt->name = $departmentOfTeacher->name;
            }

            $teacher->videoCount = TeacherVideo::where('teacher_id', '=', $td->teacher_id)->count();
            if ($teacher->videoCount > 0) {
                $teacher->videos = TeacherVideo::where('teacher_id', '=', $td->teacher_id)->get();
                foreach ($teacher->videos as $tv) {
                    $tv->info = VideoInfo::where('video_id', '=', $tv->video_id)->first();
                    if (!$tv->info) {
                        continue;
                    }
                    $video = Video::where('video_id', '=', $tv->video_id)->first();
                    $tv->playid = $video->getPlayIdStr();
                    $tv->guid = $video->guid;
                    $video_info = VideoInfo::where('video_id', '=', $tv->video_id)->first();
                    $tv->title = $video_info->title;
                }
            }
            $teachers[$k] = $teacher;
        }
        return View::make('department_detail')->with('department', $department)->with('teachers', $teachers)->with('department_id', $department_id);
    }

}