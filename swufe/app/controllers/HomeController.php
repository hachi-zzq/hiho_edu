<?php

/**
 * @author Hanxiang<hanxiang.qiu@autotiming.com>
 */
class HomeController extends BaseController
{

    public function showWelcome()
    {
        return View::make('hello');
    }

    public function blank()
    {
        echo  '';
        exit;
    }

    /**
     * 首页
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function index() {
        $topCategories = $this->getTopCategories();

        // 西南财大视频资源
        $topID1 = $topCategories[0]->id;
        $swufeVideos = $this->_getVideosOfTop($topID1);

        // 新闻
        $video_ids = array();
        $videos = Video::where('video_id', '>', 0);
        $category = Category::where('permalink', 'like', '%xinwen%')->first();
        $videoCategories = VideoCategory::where('category_id', $category->id)->get();
        foreach ($videoCategories as $kc => $vc) {
            array_push($video_ids, $vc->video_id);
        }
        $video_ids = array_unique($video_ids);
        if ($video_ids) {
            $videos = $videos->whereIn('video_id', $video_ids);
        }
        else {
            $videos = $videos->where('video_id', '0');
        }
        $videos = $videos->take(4)->orderby('created_at', 'desc')->get();
        $newsVideos = $this->getVideoAdditionalInfo($videos);

        // TED
        $topID2 = $topCategories[1]->id;
        $tedVideos = $this->_getVideosOfTop($topID2);

        // 其他院校视频资源
        $topID3 = $topCategories[2]->id;
        $otherVideos = $this->_getVideosOfTop($topID3);

        //get star teachers
        $stars = array();
        $recommendTeachersPos = RecommendPosition::where('type', 'teacher')
                                ->where('class', 'index-teacher')->first();
        if ($recommendTeachersPos) {
            $recommends = Recommend::where('position_id', $recommendTeachersPos->id)->take(4)->get();
            if ($recommends) {
                foreach ($recommends as $key => $recommend) {
                    $teacher = Teacher::find($recommend->content_id);
                    if ($teacher) {
                        $departmentOfTeacher = DepartmentTeacher::where("teacher_id", "=", $teacher->id)->get();
                        $departments = array();
                        foreach ($departmentOfTeacher as $kk => $dt) {
                            $departmentSingle = Department::find($dt->department_id);
                            if ($departmentSingle) {
                                $departments[$kk] = $departmentSingle;
                            } else {
                                $departments[$kk] = array();
                            }
                        }
                        $teacher->departments = $departments;
                        $stars[$key] = $teacher;
                    } else {
                        continue;
                    }
                }
            }
        }

        return View::make('index_category')
            ->with('swufeVideos', $swufeVideos)
            ->with('newsVideos', $newsVideos)
            ->with('tedVideos', $tedVideos)
            ->with('otherVideos', $otherVideos)
            ->with('stars', $stars);
    }


    /**
     * 按照顶级分类查找视频
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    private function _getVideosOfTop($topID) {
        $video_ids = array();
        $videos = Video::where('video_id', '>', 0);
        $videosTopCategory = VideoCategory::where('category_id', $topID)->get();
        if ($videosTopCategory) {
            foreach ($videosTopCategory as $key => $vt) {
                array_push($video_ids, $vt->video_id);
            }
        }

        // 课程资源中除去校园新闻的分类内容
        $xinwenCategory = Category::where('permalink', 'like', '%xinwen%')->first();
        if ($xinwenCategory) {
            $xinwenCID = $xinwenCategory->id;
        }
        else {
            $xinwenCID = 0;
        }

        $categories = Category::where('parent', $topID)->where('id', '<>', $xinwenCID)->get();
        foreach ($categories as $k => $c) {
            $videoCategories = VideoCategory::where('category_id', $c->id)->get();
            foreach ($videoCategories as $kc => $vc) {
                array_push($video_ids, $vc->video_id);
            }
        }
        $video_ids = array_unique($video_ids);
        if ($video_ids) {
            $videos = $videos->whereIn('video_id', $video_ids);
        }
        else {
            $videos = $videos->where('video_id', '0');
        }

        $videos = $videos->take(8)->orderby('created_at', 'desc')->get();
        return $this->getVideoAdditionalInfo($videos);
    }

    /**
     * APP下载页面
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function app() {
        return View::make('app');
    }

    /**
     * test function
     * @author zhuzhengqian
     *
     */
    public function test(){
        echo $_SERVER['SCRIPT_FILENAME'];
    }
}
