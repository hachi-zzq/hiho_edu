<?php

/**
 * @author Hanxiang<hanxiang.qiu@autotiming.com>
 */
class TeacherController extends BaseController
{

    /**
     * 教师详情页面
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function detail($teacher_id)
    {
        $teacher = Teacher::where('id', '=', $teacher_id)->first();

        if (empty($teacher)) {
            return View::make('no_exist')->with('data', '讲师');
        }

        $teacher->departments = $departmentsOfTeacher = DepartmentTeacher::where('teacher_id', '=', $teacher_id)->get();
        foreach ($teacher->departments as $dt) {
            $departmentOfTeacher = Department::where('id', '=', $dt->department_id)->first();
            if ($departmentOfTeacher) {
                $dt->name = $departmentOfTeacher->name;
            } else {
                $dt->name = '';
            }
        }

        $input = Input::get('order');
        if ($input == 'A-Z') {
            $teacher->videos = TeacherVideo::where('teacher_id', '=', $teacher_id)->paginate(12);
        } else {
            $teacher->videos = TeacherVideo::where('teacher_id', '=', $teacher_id)->orderBy('created_at', 'desc')->paginate(12);
        }

        $teacher->videosCount = TeacherVideo::where('teacher_id', '=', $teacher_id)->count();
        if ($teacher->videosCount > 0) {
            foreach ($teacher->videos as $tv) {
                $tv->video = Video::where('video_id', '=', $tv->video_id)->first();
                $tv->info = VideoInfo::where("video_id", "=", $tv->video_id)->first();
                $tv->favoriteCount = Favorite::where("play_id", "=", $tv->video->getPlayIdStr())->count();
                $tv->playid = $tv->video->getPlayIdStr();
            }
        }

        return View::make('teacher_detail')->with('teacher', $teacher)->with('input', $input);
    }

}