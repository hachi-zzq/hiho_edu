<?php

/**
 * @author Hanxiang
 */
class CategoryController extends BaseController {

    /**
     * 西南财大视频资源
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function index() {
        $input = Input::only('sort', 'cid', 'sid', 'tid', 'lang', 'perpage', 'current', 'top');
        $rules = array(
            'perpage' => 'numeric',
            'current' => 'numeric'
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return json_encode(array('status' => '-1', 'message' => '必填参数没有传递。'));
        }

        $sort = Input::get('sort');
        $cid = Input::get('cid'); // category id
        $sid = Input::get('sid'); // speciality id
        $tid = Input::get('tid'); // teacher id
        $language = Input::get('lang');
        $perpage = Input::get('perpage'); // per page item number
        $current = Input::get('current'); // current page, start from 0
        $currentTopCategoryID = Input::get('top');
        if (!isset($currentTopCategoryID)) {
            // $firstTop = Category::where('parent', '0')->first();
            // $currentTopCategoryID = $firstTop->id;
            $topCategories = $this->getTopCategories();
            $currentTopCategoryID = $topCategories[0]->id;
        }

        $videos = Video::where('video_id', '>', '0');
        // select by category
        $video_ids = array();
        if (isset($cid)) {
            $categories = VideoCategory::where('category_id', $cid)->get();
            foreach ($categories as $key => $c) {
                array_push($video_ids, $c->video_id);
            }
        }
        else {
            $videosTopCategory = VideoCategory::where('category_id', $currentTopCategoryID)->get();
            if ($videosTopCategory) {
                foreach ($videosTopCategory as $key => $vt) {
                    array_push($video_ids, $vt->video_id);
                }
            }

            $categories = Category::where('parent', $currentTopCategoryID)->orderBy('sort', 'asc')->get();
            foreach ($categories as $k => $c) {
                $videoCategories = VideoCategory::where('category_id', $c->id)->get();
                foreach ($videoCategories as $kc => $vc) {
                    array_push($video_ids, $vc->video_id);
                }
            }
        }

        $video_ids = array_unique($video_ids);
        if ($video_ids) {
            // select videos belong to a category
            $videos = $videos->whereIn('video_id', $video_ids);
        }
        else {
            $videos = $videos->where('video_id', '0');
        }

        // select by speciality
        $video_ids = array();
        if (isset($sid)) {
            $specialities = VideoSpeciality::where('speciality_id', $sid)->get();
            foreach ($specialities as $key => $s) {
                array_push($video_ids, $s->video_id);
            }

            $video_ids = array_unique($video_ids);
            if ($video_ids) {
                // select videos belong to a speciality
                $videos = $videos->whereIn('video_id', $video_ids);
            }
            else {
                $videos = $videos->where('video_id', '0');
            }
        }

        //select by teacher
        $video_ids = array();
        if (isset($tid)) {
            $teachers = TeacherVideo::where('teacher_id', $tid)->get();
            foreach ($teachers as $key => $t) {
                array_push($video_ids, $t->video_id);
            }

            $video_ids = array_unique($video_ids);
            if ($video_ids) {
                // select videos belong to a teacher
                $videos = $videos->whereIn('video_id', $video_ids);
            }
            else {
                $videos = $videos->where('video_id', '0');
            }
        }

        // select by language
        if (isset($language)) {
            $videos = $videos->where('language', $language);
        }

        // sort
        if (isset($sort) && $sort = 'hot') {
            // hot: sort by viewed
            $videos = $videos->orderby('viewed', 'desc');
        }
        else {
            // default: sort by created_at
            $videos = $videos->orderby('created_at', 'desc');
        }

        // set page
        if (!isset($perpage)) {
            // default per page items: 10
            $perpage = Config::get('app.videoItemsPerpage');
        }
        elseif($perpage <= 0) {
            $perpage = Config::get('app.videoItemsPerpage');
        }

        if (!isset($current)) {
            $current = 0;
        }
        $offset = $current * $perpage;

        $objVideos = $videos->select('video_id', 'guid', 'language', 'length', 'liked', 'viewed', 'created_at', 'access_level')
            ->take($perpage)->skip($offset)->get();


        // add video's additional info
        $objVideos = $this->getVideoAdditionalInfo($objVideos);
        $videos = $objVideos->toArray();

        // selections
        $selections = $this->getAllSelections($currentTopCategoryID);

        if (Request::ajax()) {
            return json_encode(array(
                'status'        => 0,
                'message'       => 'success',
                'videos'        => $videos
                // 'selections'    => $selections
            ));
        }
        else {
            return View::make('videos_categories')
                ->with('videos', $videos)
                ->with('selections', $selections);
        }
    }

    /**
     * 获取所有筛选条件
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    private function getAllSelections($currentTopCategoryID) {
        $topCategories = $this->getTopCategories()->toArray();
        $categories = Category::where('parent', $currentTopCategoryID)->orderBy('sort', 'asc')->get()->toArray();
        $specialities = Speciality::where('parent', '0')->select('id', 'permalink', 'name')->get()->toArray();
        $teachers = Teacher::select('id', 'permalink', 'name')->get()->toArray();
        $selections = array(
            'currentTopCategoryID' => $currentTopCategoryID,
            'topCategories' => $topCategories,
            'categories' => $categories,
            'specialities' => $specialities,
            'teachers' => $teachers
        );
        return $selections;
    }
}
