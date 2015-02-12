<?php namespace HiHo\Edu\Controller\Rest;

use Illuminate\Support\Facades\Cache;
use \DateTime;
use \App;
use \Input;
use \Validator;
use \DB;
use \URL;
use \Paginator;
use \Video;
use HiHo\Model\VideoInfo;
use HiHo\Sewise\Player;
use HiHo\Search\Client;

/**
 * RestAPI 视频
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class VideoController extends BaseController
{

    /**
     * 视频索引
     * @author zhuzhengqian
     * @return string
     */
    public function getIndex()
    {
        $input = Input::only("tag_id", "category_id", 'page', 'limit', 'since_id', 'teacher_id', "show_keyframes", 'language', 'year', 'topic_id','speciality_id');

        // 分页
        $since_id = $input['since_id'] ? $input['since_id'] : 1;
        $limit = $input['limit'] ? $input['limit'] : 15;
        $show_keyframes = $input['show_keyframes'] == 'false' ? 0 : 1;
        $page = $input['page'] > 0 ? intval($input['page']) : 1;

        //获取过滤之前的id数组
        $arrAllId = array();
        $obj = \Video::where('video_id', '>', $since_id)->get();
        if ($obj) {
            foreach ($obj as $video) {
                array_push($arrAllId, $video->video_id);
                unset($video);
            }
        }

        // filter teacher_id
        if (isset($input['teacher_id'])) {
            $arrTeacherIds = array();
            $objVideo = \TeacherVideo::where('teacher_id', $input['teacher_id'])->get();
            if ($objVideo) {
                foreach ($objVideo as $video) {
                    array_push($arrTeacherIds, $video->video_id);
                }
            }
            $arrAllId = array_intersect($arrAllId, $arrTeacherIds);
            unset($objVideo);
        }

        // filter category_id
        if (isset($input['category_id'])) {
            $arrCatIds = array();
            $objVideo = \VideoCategory::where('category_id', $input['category_id'])->get();
            if ($objVideo) {
                foreach ($objVideo as $video) {
                    array_push($arrCatIds, $video->video_id);
                }
            }
            $arrAllId = array_intersect($arrAllId, $arrCatIds);
            unset($objVideo);
        }

        // filter topic_id
        if (isset($input['topic_id'])) {
            $arrTopicIds = array();
            $objVideo = \TopicVideos::where('topic_id', $input['topic_id'])->get();
            if ($objVideo) {
                foreach ($objVideo as $video) {
                    array_push($arrTopicIds, $video->video_id);
                }
            }
            $arrAllId = array_intersect($arrAllId, $arrTopicIds);
            unset($objVideo);
        }

        // filter language
        if (isset($input['language'])) {
            $arrLanguageIds = array();
            $objVideo = \Video::where('language', $input['language'])->get();
            if ($objVideo) {
                foreach ($objVideo as $video) {
                    array_push($arrLanguageIds, $video->video_id);
                }
            }
            $arrAllId = array_intersect($arrAllId, $arrLanguageIds);
            unset($objVideo);
        }


        // filter speciality
        if (isset($input['speciality_id'])) {
            $arrSpeciality = array();
            $objVideo = \VideoSpeciality::where('speciality_id', $input['speciality_id'])->get();
            if ($objVideo) {
                foreach ($objVideo as $video) {
                    array_push($arrSpeciality, $video->video_id);
                }
            }
            $arrAllId = array_intersect($arrAllId, $arrSpeciality);
            unset($objVideo);
        }

        // filter year (TODO: 可以沟通后去掉)
        if (isset($input['year'])) {
            $arrYearIds = array();
            $objVideo = \Video::whereBetween('created_at', array($input['year'] . '-01-01 00:00:00', $input['year'] . '-12-30 23:59:59'))->get();
            if ($objVideo) {
                foreach ($objVideo as $video) {
                    array_push($arrYearIds, $video->video_id);
                }
            }
            $arrAllId = array_intersect($arrAllId, $arrYearIds);
            unset($objVideo);
        }

        if ($arrAllId) {
            $strId = implode(',', $arrAllId);
        } else {
            $strId = -1;
        }

        $videos = \Video::whereRaw("video_id in ($strId) and deleted_at IS NULL")->paginate($limit);
        if ($videos) {
            foreach ($videos as &$video) {
                $video->playid = $video->getPlayIdStr();
                $video_info = VideoInfo::where("video_id", $video->video_id)->get();
                $arrInfo = array();
                foreach ($video_info as $info) {
                    $stdClass = new \stdClass();
                    $stdClass->title = $info->title;
                    $stdClass->description = $info->description;
                    $stdClass->is_original = $info->is_original;
                    $stdClass->language = $info->language;
                    //分类
                    $objVideoCategory = \VideoCategory::where('video_id', $info->video_id)->first();
                    $stdClass->category = '';
                    if ($objVideoCategory) {
                        $objCategory = \Category::find($objVideoCategory->category_id);
                        if ($objCategory) {
                            $stdClass->category = $objCategory->name;
                        }
                    }

                    $teacher = \TeacherVideo::where('video_id', $info->video_id)->first();
                    $stdClass->teacher = '';
                    $stdClass->department = '';
                    $stdClass->parent_department = new \stdClass();
                    if ($teacher) {
                        $stdClass->teacher = \Teacher::find($teacher->teacher_id)->name;
                        $department = \DepartmentTeacher::where("teacher_id", $teacher->teacher_id)->first();
                        if ($department) {
                            $stdClass->department = \Department::find($department->department_id);
                        }
                    }
                    array_push($arrInfo, $stdClass);
                }
                $video->info = $arrInfo;

                ##pictures
                $video->pictures = \VideoPicture::where('video_id', $video->video_id)->get();

                if (!$show_keyframes) {
                    unset($video->keyframes);
                }
            }
        }

        $response = $videos->toArray();
        return $this->encodeResult('10701', 'success', $response);
    }

    /**
     * 搜索
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string
     */
    public function getSearch()
    {
        // 表单验证规则
        $input = Input::only('q', 'page', 'limit', 'since_id');
        $rules = array(
            'q' => array('required', 'max:64', 'min:2'),
            'page' => array('numeric'),
            'limit' => array('numeric'),
            'since_id' => array(),
        );
        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 分页准备
        $paginator = App::make('paginator');

        $page = $input['page'] ? $input['page'] : 1;
        $since_id = $input['since_id'] ? $input['since_id'] : 1;
        $perPage = $input['limit'] ? $input['limit'] : 15;

        // TODO: $perPage > $lastPage

        // 引入新搜索客户端
        $q = $input['q'];
        $client = new Client();
        $response = $client->search($q, (($page - 1) * $perPage), $perPage);

        // 查询时间
        $queryTime = $client->getResponseHeader()->QTime / 1000;

        // 高亮标题等
        $highlighting = $client->getResponseHighlighting();


        // 预处理数据
        $searchResultArr = array();
        if (isset($response->docs)) {
            foreach ($response->docs as &$row) {
                $rowArr = array();
                $rowArr['video_id'] = $video_id = $row->video_id;
                $rowArr['created_at'] = new DateTime(str_replace('Z', 'UTC', $row->created_at));
                $rowArr['updated_at'] = new DateTime(str_replace('Z', 'UTC', $row->updated_at));

                $rowArr['guid'] = $row->guid;
                $rowArr['playid'] = $row->playid;
                $rowArr['length'] = $row->length;
                $rowArr['language'] = $row->language;
                $rowArr['origin_id'] = $row->origin_id;
                $rowArr['liked'] = $row->liked;
                $rowArr['viewd'] = $row->viewd;

                $rowArr['title'] = $row->title;

                // 接口需求方不需要高亮, 该方法和主搜索控制器方法有不同之处, 请注意.

                // 输出字幕结果高亮
                // 1. 遍历 TXT 字幕全文, 替换分词后的[关键词]为高亮, 即带 EM 标签的词
                // 2. 增加按行号配对的时间戳, 即显示的时间和 URL 的 STARTTIME

                // TODO: 多语言、多条 Info 的情况
                $rowArr['title'] = is_string($rowArr['title']) ? $rowArr['title'] : array_values($rowArr['title'])[0];
                $rowArr['thumbnails'] = is_string($row->thumbnails) ? $row->thumbnails : array_values($row->thumbnails)[0];

                $timezone = $row->subtitle_timeline;
                if (isset($row->subtitle_content_en)) {
                    $fulltext = $row->subtitle_content_en;
                } else if (isset($row->subtitle_content_zh)) {
                    $fulltext = $row->subtitle_content_zh;
                }

                // 缓存碎片处理结果
                $fkey = sprintf('search_fragment_%s_%s', $row->video_id, hash('sha1', $q));

                if (Cache::has($fkey)) {
                    $fragments = Cache::get($fkey);
                } else {
                    $fragments = $client->formatFragments(
                        $fulltext, $timezone, $q
                    );
                    Cache::put($fkey, $fragments, 1);
                }

                $rowArr['fragments'] = $fragments;
                $searchResultArr[] = $rowArr;
            }
        }

        $searchResult = $paginator->make($searchResultArr, $response->numFound, $perPage)->toArray();


        return $this->encodeResult('10703', 'succeed',
            array(
                'searchResult' => $searchResult,
                'queryTime' => $queryTime
            )
        );
    }

    /**
     * 异步获得字幕时间戳
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getSearchSubtitleResultWithKeywordsAndGuid()
    {
        // 表单验证规则
        $input = \Input::only('video_guid', 'keywords');
        $rules = array(
            'video_guid' => array('min:36'),
            'keywords' => array('required', 'min:2', 'max:64')
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        $video = Video::where('guid', '=', $input['video_guid'])->first();

        /**
         * 判断视频是否存在
         */
        if (!$video) {
            return $this->encodeResult('20701', 'Video does not exist.');
        }

        // 启动搜索器
        $searcher = new Searcher();
        $searchResult = $searcher->querySingleVideoSubtitles($video->video_id, $input['keywords']);

        return $this->encodeResult('10706', 'Succeed', $searchResult);
    }


    /**
     * 显示视频信息
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $guid
     */
    public function getShow()
    {
        // 表单验证规则
        $input = \Input::only('token', 'guid', 'video_id', 'show_keyframes');
        $rules = array(
            'token' => array(),
            'guid' => array('min:36'),
            'video_id' => array(),
            'show_keyframes' => array(''),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 判断使用 GUID 还是 VIDEO_ID
        if ($input['guid']) {
            $video = \Video::where('guid', '=', $input['guid'])->get()->first() ? \Video::where('guid', '=', $input['guid'])->get()->first() : NULL;
        } else {
            $video = \Video::find($input['video_id']);
        }

        /**
         * 判断视频是否存在
         */
        if (!$video) {
            return $this->encodeResult('20701', 'Video does not exist.');
        }

        // 修正切割播放地址
        $player = new Player();
        $player->loadVideo($video);
        $player->clip();

        $resource = $player->getResource();

        /**
         * 解序列化
         */
        if (empty($input['show_keyframes']) or strtolower($input['show_keyframes']) != 'false') {
            $video->keyFrames = unserialize($video->keyframes);
        }
        $video->playid = $video->getPlayIdStr();
        $video->info = $video->info()->get()->toArray();
        $video->pictures = $video->pictures()->get()->toArray();
        $video->resource = $resource;
        $video->content_rating = $video->content_rating()->get()->toArray();
        //取得更全面的category信息，而不是关系
//        $video->category = $video->category()->get()->toArray();
        //category
        $objTeacherCategory = \VideoCategory::where('video_id', $video->video_id)->get();
        $arrCat = array();
        if ($objTeacherCategory) {
            foreach ($objTeacherCategory as $v) {
                array_push($arrCat, \Category::find($v->category_id));
            }
        }
        $video->category = $arrCat;
        //teacher
        $objTeacherVideo = \TeacherVideo::where('video_id', $video->video_id)->first();
        if ($objTeacherVideo) {
            $video->teacher = \Teacher::find($objTeacherVideo->teacher_id);
        } else {
            $std = new \stdClass();
            $video->teacher = $std;
        }
        unset($video->keyframes);

        $subtitles = $video->subtitles()->where('type', '=', 'JSON')->get();
        $subtitles2 = array();
        foreach ($subtitles as $s) {
            $subtitles2[] = array(
                'is_original' => $s->is_original,
                'language' => $s->language,
                'type' => $s->type,
                // TODO: 临时方案
                'url' => strtolower('http://swufe.autotiming.com/subtitle/' . $video->guid . '/' . $s->language . '.' . $s->type),
            );
        }

        $video->subtitles = $subtitles2;

        return $this->encodeResult('10702', 'succeed', $video->toArray());
    }

    /**
     * 通过 Source 和 Origin_id 获得 Video
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $origin_id
     * @return mixed
     */
    public function getVideoByOriginId($source_id = NULL, $origin_id = NULL)
    {
        // 表单验证规则
        $input = \Input::only('source_id', 'origin_id');
        $rules = array(
            'source_id' => array(),
            'origin_id' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // HTTP GET 参数转入变量
        $input['source_id'] ? $source_id = $input['source_id'] : $source_id = 1;
        $input['origin_id'] ? $origin_id = $input['origin_id'] : NULL;

        $video = \Video::where('source_id', '=', $source_id)
            ->where('origin_id', '=', $origin_id)
            ->get()
            ->first();

        /**
         * 判断视频是否存在
         */
        if (!$video) {
            return $this->encodeResult('20701', 'Video does not exist.');
        }

        /**
         * 返回 ID
         */
        return $this->encodeResult('10704', 'succeed', $video->toArray());
    }

    /**
     * 通过 TaskID 获得 Video
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $origin_id
     * @return mixed
     */
    public function getVideoBySewiseTaskId($origin_id = NULL)
    {
        $source_id = 1;
        return $this->getVideoByOriginId($source_id, $origin_id);
    }

    /**
     * 获得总查询时间
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    private function getAllQueryTime()
    {
        $time = 0;
        $queries = DB::getQueryLog();
        foreach ($queries as $q) {
            $time += $q['time'];
        }
        return $time;
    }
}