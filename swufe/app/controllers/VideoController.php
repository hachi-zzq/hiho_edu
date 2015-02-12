<?php

use \Mobile_Detect;
use HiHo\Sewise\Player;
use HiHo\Model\Favorite;

class VideoController extends BaseController
{

    /**
     * 视频通用索引
     * @return mixed
     */
    public function index()
    {
        $data = array();

        $select['cid'] = $cid = Input::get('cid'); //category_id
        $select['year'] = $year = Input::get('year');
        $select['lang'] = $lang = Input::get('lang'); //language:en, zh_cn
        $select['order'] = $order = Input::get('order');
        $select['topic'] = $topic = Input::get('topic');

        // calculate query
        $video_ids = array();
        if ($cid > 0) {
            $categories = VideoCategory::where('category_id', '=', $cid)->get();
            foreach ($categories as $c) {
                array_push($video_ids, $c->video_id);
            }

            $video_ids = array_unique($video_ids);
            if ($video_ids) {
                $videoIDs = '';
                foreach ($video_ids as $video_id) {
                    $videoIDs .= $video_id . ',';
                }
                $videoIDs .= '0';
                $whereRaw = "video_id in ($videoIDs) AND deleted_at IS NULL";
            } else {
                $whereRaw = "video_id = 0 AND deleted_at IS NULL";
            }
        } else {
            $whereRaw = 'video_id <> 0 AND deleted_at IS NULL';
        }

        if ($topic > 0) {
            $topics = TopicVideos::where('topic_id', '=', $topic)->get();
            foreach ($topics as $t) {
                array_push($video_ids, $t->video_id);
            }
        }

        if ($year > 0) {
            $whereRaw .= " AND created_at > '$year-01-01 00:00:00' AND created_at < '$year-12-31 23:59:59'";
        }
        if ($lang) {
            $whereRaw .= " AND language = '$lang'";
        }

        // calculate order by
        if ($order) {
            $orderBy = 'viewed';
        } else {
            $orderBy = 'created_at';
        }

        $videos = Video::whereRaw($whereRaw)->orderBy($orderBy, 'desc')->paginate(12);
        if ($videos) {
            foreach ($videos as $k => $v) {
                $v->favoriteCount = Favorite::where("play_id", $v->getPlayIdStr())->count();
                // $v->playid = PlayID::isExistWithEntity($v)->play_id;
                $v->playid = $v->getPlayIdStr();

                $v->videoInfo = VideoInfo::where("video_id", "=", $v->video_id)->first();

                $teachersOfCourses = TeacherVideo::where("video_id", "=", $v->video_id)->first();
                if (!empty($teachersOfCourses)) {
                    $v->teacher = Teacher::where("id", "=", $teachersOfCourses->teacher_id)->first();
                    $teachersOfDepartments = DepartmentTeacher::where("teacher_id", "=", $teachersOfCourses->teacher_id)->first();
                    if($teachersOfDepartments){
                        $v->department = Department::where("id", "=", $teachersOfDepartments->department_id)->first();
                    }
                } else {
                    $v->userInfo = array();
                    $v->teacher = array();
                    $v->department = array();
                }

                if ($v->video_id != 0) {
                    $data[$k]['info'] = VideoInfo::where("video_id", $v->video_id)->first();
                    if (!$v->info) {
                        $info = new \stdClass();
                        $info->title = "title not found";
                        $data[$k]['info'] = $info;
                    }

                    $data[$k]['pic'] = VideoPicture::where("video_id", $v->video_id)->first();
                    if (!$v->pic) {
                        $pic = new \stdClass();
                        $pic->src = "/static/img/video_default.png";
                        $data[$k]['pic'] = $pic;
                    }
                }
            }
        }

        $topics = Topic::get();

        $categories = Category::whereRaw('parent = 0 AND deleted_at IS NULL')->get();
        return View::make('videos')->with('videos', $videos)->with('select', $select)->with('categories', $categories)->with('topics', $topics);
    }



    //视频预览页面
    public function review()
    {

        $input = Input::Only("st", "et", "guid");
        // 检查必填参数

        $rules = array(
            'st' => 'required',
            'et' => 'required',
            'guid' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return json_encode(array('status' => '-1', 'message' => '必填参数没有传递。'));
        }

        // 检查 视频 GUID 合法性
        try {
            $video = Video::where('guid', '=', $input['guid'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return json_encode(array('status' => '-2', 'message' => '视频 GUID 不存在。'));
        }

        $player = new Player();
        $player->loadVideo($video);
        $player->clip($input['st'], $input['et']);
        $resource = $player->getResource();
        $array['subtitle'] = '/subtitle/' . $input['guid'] . '/' . $input['st'] . '-' . $input['et'] . '}/' . $video->language . '.srt';
        $data = VideoInfo::where('video_id', $video->video_id)->first();
        if (isset($resource['SD'])) {
            $resource['src'] = $resource['SD']['FLV']['src'];
        } elseif (isset($resource['HD'])) {
            $resource['src'] = $resource['HD']['FLV']['src'];
        } else {
            $resource['src'] = '';
        }

        return View::make("review")->with("data", $data)->with("video", $resource);
    }

    /**
     * 视频一级分类页面，获取更多视频Ajax方法
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function loadMore()
    {
        $input = Input::only('current', 'perpage', 'language', 'teacher');
        $rules = array(
            'current' => 'required|integer'
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return json_encode(array('status' => '-1', 'message' => '必填参数没有传递', 'data' => array()));
        }

        if (!isset($input['perpage'])) {
            $input['perpage'] = 10;
        }

        if (!isset($input['current'])) {
            $input['current'] = 0;
        }

        //get videos
        $offset = $input['perpage'] * $input['current'];
        $videos = Video::where('video_id', '>', 0);
        if (isset($input['language'])) {
            $videos = Video::where('language', $input['language']);
        }

        $videos = $videos->take($input['perpage'])->skip($offset)->orderBy("video_id", "desc")->get();
        if (count($videos) == 0) {
            return json_encode(array('status' => '1', 'message' => 'no more videos', 'data' => array()));
        }

        $videosData = array();
        foreach ($videos as $key => $video) {
            $videoInfo = VideoInfo::where('video_id', $video->video_id)->first();
            $videosData[$key]['videoID'] = $video->video_id;
            $videosData[$key]['guid'] = $video->guid;
            $videosData[$key]['title'] = $videoInfo->title;
            $videosData[$key]['description'] = $videoInfo->description;
            $videosData[$key]['shortID'] = ShortUri::dec2short($video->video_id);
            $videoPicture = VideoPicture::where('video_id', $video->video_id)->first();
            $videosData[$key]['videoImage'] = $videoPicture->src;

            //teacher info
            $teacherVideo = TeacherVideo::where('video_id', $video->video_id)->first();
            if ($teacherVideo) {
                $teacher = Teacher::find($teacherVideo->teacher_id);
                $videosData[$key]['teacherID'] = $teacher->id;
                $videosData[$key]['teacherName'] = $teacher->name;
                $videosData[$key]['teacherTitle'] = $teacher->title;

                //department info
                $departmentTeacher = DepartmentTeacher::where('teacher_id', $teacherVideo->teacher_id)->first();
                if ($departmentTeacher) {
                    $department = Department::find($departmentTeacher->department_id);
                    $videosData[$key]['departmentID'] = $department->id;
                    $videosData[$key]['departmentName'] = $department->name;
                } else {
                    $videosData[$key]['departmentID'] = '0';
                    $videosData[$key]['departmentName'] = '';
                }
            } else {
                $videosData[$key]['teacherID'] = '0';
                $videosData[$key]['teacherName'] = '';
                $videosData[$key]['teacherTitle'] = '';
                $videosData[$key]['departmentID'] = '0';
                $videosData[$key]['departmentName'] = '';
            }

            //category info
            $videoCategory = VideoCategory::where('video_id', $video->video_id)->first();
            if ($videoCategory) {
                $category = Category::find($videoCategory->category_id);
                $videosData[$key]['categoryID'] = $category->id;
                $videosData[$key]['categoryName'] = $category->name;
            } else {
                $videosData[$key]['categoryID'] = '0';
                $videosData[$key]['categoryName'] = '';
            }
        }

        return json_encode(array('status' => '0', 'message' => 'success', 'data' => $videosData));
    }

}