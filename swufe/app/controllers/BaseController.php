<?php

class BaseController extends Controller {

    /**
     * 顶部分级数据，可用于所有页面，方便模板内调用
     * @author Hanxiang <hanxiang.qiu@autotiming.com>
     */
    public function __construct() {
        $topCategories = $this->_getTopCategories();
        View::share('topCategories', $topCategories);
    }

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    /**
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function getCaptcha() {
        echo $this->getCaptchaString();
        return;
    }

    /**
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function getCaptchaString() {
        $validateCode = new \HiHo\Other\ValidateCode();
        $validateCode->doimg();
        Session::put('validateCode', $validateCode->getCode());
        $content = file_get_contents(\Config::get('validate_code.validatePng'));
        return 'data:image/png;base64,'.base64_encode($content);
    }

    /**
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function getTopCategories() {
        return $this->_getTopCategories();
    }

    private function _getTopCategories() {
        $topCategories = Category::where('parent', 0)->select('id', 'permalink', 'name')->orderBy('sort', 'asc')->get();
        return $topCategories;
    }

    /**
     * 获取视频附加信息：videoInfo, teacher etc.
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     * @param object Video
     * @return object
     */
    public function getVideoAdditionalInfo($objVideos) {
        //
        foreach ($objVideos as $k => $v) {
            // playID
            $v->playID = $v->getPlayIdStr();

            // video info
            $videoInfo = VideoInfo::where('video_id', $v->video_id)->first();
            if ($videoInfo) {
                $v->video_title = $videoInfo->title;
                $v->video_description = $videoInfo->description;
            }
            else {
                $v->video_title = '';
                $v->video_description = '';
            }

            // favorite count
            $v->favoriteCount = Favorite::where("play_id", $v->playID)->count();

            // video pictures
            $videoPicture = VideoPicture::where('video_id', $v->video_id)->first();
            if ($videoPicture) {
                $v->video_picture = $videoPicture->src;
            }
            else {
                $v->video_picture = '/static/img/video_default.png';
            }

            // teacher info
            $teacherVideo = TeacherVideo::where('video_id', $v->video_id)->first();
            if ($teacherVideo) {
                $teacher = Teacher::find($teacherVideo->teacher_id);
                $v->teacher_id = $teacher->id;
                $v->teacher_name = $teacher->name;
                $v->teacher_title = $teacher->title;
                if ($teacher->portrait_src) {
                    $v->teacher_avatar = $teacher->portrait_src;
                }
                else {
                    $v->teacher_avatar = \Config::get('app.pathToSource') . '/img/avatar_no_name.png';
                }
                $departmentTeacher = DepartmentTeacher::where('teacher_id', $teacher->id)->first();
                if ($departmentTeacher) {
                    $department = Department::find($departmentTeacher->department_id);
                    if ($department) {
                        $v->teacher_department_id = $department->id;
                        $v->teacher_department_name = $department->name;
                    }
                    else {
                        $v->teacher_department_id = '';
                        $v->teacher_department_name = '';
                    }
                }
                else {
                    $v->teacher_department_name = '';
                }
            }
            else {
                $v->teacher_id = 0;
                $v->teacher_name = '';
                $v->teacher_title = '';
                $v->teacher_avatar = \Config::get('app.pathToSource') . '/img/avatar_no_name.png';
                $v->teacher_department_name = '';
            }

            // speciality info
            $specialityVideo = VideoSpeciality::where('video_id', $v->video_id)->first();
            if ($specialityVideo) {
                $speciality = Speciality::find($specialityVideo->speciality_id);
                $v->speciality_id = $speciality->id;
                $v->speciality_name = $speciality->name;
            }
            else {
                $v->speciality_id = 0;
                $v->speciality_name = '';
            }
        }

        return $objVideos;
    }

}
