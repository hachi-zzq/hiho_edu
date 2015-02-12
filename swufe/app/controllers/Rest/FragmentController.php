<?php namespace HiHo\Edu\Controller\Rest;

use \Input;
use \URL;
use \Validator;
use \DB;
use \Subtitle;
use \Fragment;
use \Video;
use HiHo\Sewise\Player;
use HiHo\Subtitle\Shear;
use HiHo\Sewise\LanguageCode;

/**
 * RestAPI 碎片
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class FragmentController extends BaseController
{
    /**
     * 碎片列表
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string
     */
    public function getIndexV2()
    {

        // 表单验证规则
        $input = Input::only('order_by', 'token', 'page', 'limit', 'since_id');
        $rules = array(
            'order_by' => array(),
            'page' => array(),
            'limit' => array('integer'),
            'since_id' => array('integer'),
        );
        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 分页
        $since_id = $input['since_id'] ? $input['since_id'] : 1;
        $limit = $input['limit'] ? $input['limit'] : 15;
        $fragments = Fragment::where('id', '>=', $since_id)
            ->where('cover', '!=', '');

        // 过滤 user_id
        if ($input['token']) {
            // 验证 Token 身份
            $userId = $this->verifyToken($input['token']);
            if (!$userId) {
                return $this->encodeResult('20100', 'Token validation fails.');
            }

            $fragments = $fragments->where('user_id', '=', $userId);
        }

        // 按时间、浏览数或收藏数排序
        if ($input['order_by'] == 'time') {
            $fragments = $fragments->orderBy('created_at', 'DESC');
        } else if ($input['order_by'] == 'view') {
            $fragments = $fragments->orderBy('views', 'DESC');
        } else if ($input['order_by'] == 'like') {
            $fragments = $fragments->orderBy('likes', 'DESC');
        } else {
            $fragments = $fragments->orderBy('created_at', 'DESC');
        }

        $fragments = $fragments->paginate($limit);

        // TODO: 字幕内容切割
//        foreach ($fragments as &$f) {
//            $v = Video::find($f->video_id);
//            $f->info = $v->info->toArray();
//
//            $json = $this->clipSubtitle($v->language, $v,
//                $f->start_time, $f->end_time, 'json', $f->info);
//            if (is_array($json)) {
//                $f->subtitle = $json['value'];
//            } else {
//                $f->subtitle = Subtitle::generateTxt($json);
//            }
//        }

        foreach ($fragments as $key => &$f) {
            $v = Video::find($f->video_id);
            if (!$v) {
                // 进行 Unset 后数据量不对了,并且unset后，返回类型发生变换，fragments将不是以数组的形式返回 by zhuzhengqian
                // unset($fragments[$key]);
                $f->info = array();
            } else {
                $f->info = \VideoInfo::with('video')->where('video_id', $f->video_id)->get();
                foreach ($f->info as $info) {
                    unset($info->video->keyframes);
                }
                $f->playid = $f->getPlayIdStr();
                $f->subtitle = ''; // TODO: subtitle content
            }
            unset($f);
        }

        $arrResponse = $fragments->toArray();
        $responseData = $arrResponse['data'];
        $arrTmp = array();
        if ($responseData) {
            foreach ($responseData as $f) {
                if ($f['info']) {
                    array_push($arrTmp, $f);
                }
            }
        }

        $arrResponse['data'] = $arrTmp;

        return $this->encodeResult('10802', 'succeed', $arrResponse);
    }

    /**
     * 截取视频碎片实例
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string
     */
    public function getShowWithVideoGuid()
    {
        $input = Input::only('video_guid', 'st', 'et', 'user_id');
        $rules = array(
            'video_guid' => array('required'),
            'st' => array('required'),
            'et' => array('required'),
            'user_id' => array(''),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 检查 UserID 合法性
        if ($input['user_id']) {
            try {
                $user = Video::find($input['user_id']);
            } catch (ModelNotFoundException $e) {
                return $this->encodeResult('20804', '分享人的 UserID 不存在，如没有请勿传递该参数');
            }
        } else {
            $user = NULL;
        }

        $video = Video::where('guid', '=', $input['video_guid'])->first();

        // 检查，如果没有，储存新碎片入库
        if (!$video) {
            return $this->encodeResult('20801', 'Video does not exist.');
        }

        $fragment = Fragment::getFragmentByStAndEt($video, $input['st'], $input['et'], $user);

        if ($input['st'] and $input['et']) {
            $st = $input['st'];
            $et = $input['et'];
        } else {
            $st = $fragment->start_time;
            $et = $fragment->end_time;
        }

        // 修正切割播放地址
        $player = new Player();
        $player->loadVideo($video);
        $player->clip($st, $et);

        $resource = $player->getResource();

        /**
         * 解序列化
         */
        $video->keyFrames = unserialize($video->keyframes);
        unset($video->keyframes);
        $video->info = $video->info()->get()->toArray();
        $video->pictures = $video->pictures()->get()->toArray();
        $video->content_rating = $video->content_rating()->get()->toArray();
        $video->category = $video->category()->get()->toArray();

        unset($video->resource);

        $fragment->playid = $fragment->getPlayIdStr();
        $fragment->video = $video->toArray();
        $fragment->resource = $resource;
        $fragment->parameters = array(
            'adjustKeyFramesTime' => $player->getAdjustKeyFramesTime(),
            'fSt' => $player->getFSt(),
            'fEt' => $player->getFEt(),
            'vSt' => $player->getVSt(),
            'vEt' => $player->getVEt()
        );

        $subtitles = $video->subtitles()->where('type', '=', 'JSON')->get();
        $subtitles2 = array();
        foreach ($subtitles as $s) {
            $subtitles2[] = array(
                'is_original' => $s->is_original,
                'language' => $s->language,
                'type' => $s->type,
                // TODO: URL 前缀是临时方案
                'url' => strtolower(sprintf("http://swufe.autotiming.com/subtitle/%s/%s-%s/%s.%s",
                    $video->guid, $st, $et, $s->language, $s->type)),
            );
        }

        $fragment->subtitles = $subtitles2;

        // TODO: 临时方案
        $fragment->share_url = 'http://swufe.autotiming.com/fragment/' . $fragment->guid;

        // TODO: 封面地址
        return $this->encodeResult('10801', 'succeed', $fragment->toArray());
    }

    /**
     * TODO: 通过 PlayID 查询
     */
    public function getShowWithPlayId()
    {

    }

    /**
     * 获得视频碎片实例
     * @author Luyu<luyu.zhang@autotiming.com> zhuzhengqian<zhuzhengqian@autotimong.com>
     * @return string
     */
    public function getShowWithGuid()
    {
        $input = Input::only('guid');
        $rules = array(
            'guid' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        $fragment = Fragment::where('guid', '=', $input['guid'])->first();
        if (!$fragment) {
            return $this->encodeResult('20802', 'Fragment does not exist.');
        }

        $video = $fragment->video()->first();

        // 检查，如果没有，储存新碎片入库
        if (!$video) {
            return $this->encodeResult('20801', 'Video does not exist.');
        }

        $st = $fragment->start_time;
        $et = $fragment->end_time;

        // 修正切割播放地址
        $player = new Player();
        $player->loadVideo($video);
        $player->clip($st, $et);

        $resource = $player->getResource();

        /**
         * 解序列化
         */
        $video->keyFrames = unserialize($video->keyframes);
        unset($video->keyframes);
        $video->info = $video->info()->get()->toArray();
        $video->pictures = $video->pictures()->get()->toArray();
        $video->content_rating = $video->content_rating()->get()->toArray();
//        $video->category = $video->category()->get()->toArray();
        $objTeacherCategory = \VideoCategory::where('video_id', $video->video_id)->get();
        $arrCat = array();
        if ($objTeacherCategory) {
            foreach ($objTeacherCategory as $v) {
                array_push($arrCat, \Category::find($v->category_id));
            }
        }
        $video->category = $arrCat;
        unset($video->resource);

        $fragment->playid = $fragment->getPlayIdStr();
        $fragment->video = $video->toArray();
        $fragment->resource = $resource;
        $fragment->parameters = array(
            'adjustKeyFramesTime' => $player->getAdjustKeyFramesTime(),
            'fSt' => $player->getFSt(),
            'fEt' => $player->getFEt(),
            'vSt' => $player->getVSt(),
            'vEt' => $player->getVEt()
        );

        $subtitles = $video->subtitles()->where('type', '=', 'JSON')->get();
        $subtitles2 = array();
        foreach ($subtitles as $s) {
            $subtitles2[] = array(
                'is_original' => $s->is_original,
                'language' => $s->language,
                'type' => $s->type,
                // TODO: URL 前缀是临时方案
                'url' => strtolower(sprintf("http://swufe.autotiming.com/subtitle/%s/%s-%s/%s.%s",
                    $video->guid, $st, $et, $s->language, $s->type)),
            );
        }

        $fragment->subtitles = $subtitles2;

        // TODO: 临时方案
        $fragment->share_url = 'http://swufe.autotiming.com/fragment/' . $fragment->guid;

        //user info
//        $fragment->user = new \stdClass();
//        if($fragment->user_id){
//            $fragment->user = \User::find($fragment->user_id);
//        }

        // TODO: 封面地址
        return $this->encodeResult('10801', 'succeed', $fragment->toArray());
    }

    /**
     * 删除碎片实例
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string
     */
    public function postDestroy()
    {
        $input = Input::only('token', 'fragment_guid');
        $rules = array(
            'token' => array('required'),
            'fragment_guid' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $v = $this->verifyToken($input['token']);
        if (!$v) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }

        $fragment = \Fragment::where('guid', '=', $input['fragment_guid'])->get()->first();

        /**
         * 判断是否存在
         */
        if (!$fragment) {
            return $this->encodeResult('20802', 'Fragment does not exist.');
        }

        /**
         * 判断有权限删除
         */
        if ($fragment->user_id != \Auth::user()->user_id) {
            return $this->encodeResult('20103', 'Access denied');
        }

        $fragment->delete();
        return $this->encodeResult('10805', 'Succeed');
    }

    /**
     * 删除碎片实例
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getDestroy()
    {
        return $this->postDestroy();
    }

    /**
     * 获得碎片实例的截图
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string
     */
    public function getPictureWithGuid()
    {
        $input = Input::only('guid');

        $rules = array(
            "guid" => "required",
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }


        $fragment = Fragment::where('guid', '=', $input['guid'])->first();
        if (!$fragment) {
            return $this->encodeResult('20802', 'Fragment does not exist.');
        }

        $video = $fragment->video()->first();

        // 检查，如果没有，储存新碎片入库
        if (!$video) {
            return $this->encodeResult('20801', 'Video does not exist.');
        }

        $st = $fragment->start_time;
        $et = $fragment->end_time;

        // TODO: Sewise 服务器选择
        $result = file_get_contents("http://127.0.0.1/service/api/?do=index&op=getscreenshot&taskid=" . $video->origin_id . "&time=" . $st);
        $result = json_decode($result);

        if (isset($result->errors)) {
            return $this->encodeResult('20805', $result->errors);
        } else {
            return $this->encodeResult('10803', '获取碎片截图成功', array('keyframePic' => $result->url));
        }

    }

    /**
     * 字幕切割 by shear
     * @author guanjun <guanjun.li@autotiming.com>
     * @param $language
     * @param $video_guid
     * @param $st
     * @param $et
     * @param string $returnType
     * @param string $videoinfo
     * @return string
     */
    private function clipSubtitle($language, $video, $st, $et, $returnType = 'json', $videoInfo)
    {
        // TODO: $videoInfo

        // 转换语言代码查询
        $langCode = new LanguageCode($language);
        $langCode = $langCode->getTargetCode();

        // 查找 JSON 字幕
        $subtitle = Subtitle::where('video_id', '=', $video->video_id)
            ->where('type', '=', 'JSON')
            ->where('language', '=', $langCode)
            ->first();
        if (!$subtitle) {
            // TODO: INFO 的语言筛选
            $arr['state'] = 'error';
            $arr['value'] = $videoInfo[0]['title'];
            return $arr;
        }

        // 电锯杀人狂来了
        $shear = new Shear();
        $shear->loadSubtitle($subtitle);
        $shear->clip($st, $et);
        $result = $shear->output($returnType);
        unset($shear);

        return $result;
    }


    /**
     * (废弃方法!)
     * 获得碎片视频
     * 可以使用碎片 guid 或 video_guid + st + et + user_id
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getShow()
    {
        $input = Input::only('guid', 'video_guid', 'st', 'et', 'user_id');
        $rules = array(
            'guid' => array(''),
            'video_guid' => array(),
            'st' => array(''),
            'et' => array(''),
            'user_id' => array(''),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 检查 UserID 合法性
        if ($input['user_id']) {
            try {
                $user = Video::find($input['user_id']);
            } catch (ModelNotFoundException $e) {
                return $this->encodeResult('20804', '分享人的 UserID 不存在，如没有请勿传递该参数');
            }
        } else {
            $user = NULL;
        }

        if (!$input['guid']) {
            if ($input['st'] === '0') {
                NULL;
            }
            if (empty($input['video_guid']) or (empty($input['st']) and !$input['st'] === '0') or empty($input['et'])) {
                return $this->encodeResult('20803', 'The input parameter is not correct.');
            }
            $video = Video::where('guid', '=', $input['video_guid'])->first();
            $fragment = Fragment::getFragmentByStAndEt($video, $input['st'], $input['et'], $user);
        } else {
            $fragment = Fragment::where('guid', '=', $input['guid'])->first();
            if (!$fragment) {
                return $this->encodeResult('20802', 'Fragment does not exist.');
            }

            $video = $fragment->video()->first();
        }

        // 检查，如果没有，储存新碎片入库
        if (!$video) {
            return $this->encodeResult('20801', 'Video does not exist.');
        }

        if ($input['st'] and $input['et']) {
            $st = $input['st'];
            $et = $input['et'];
        } else {
            $st = $fragment->start_time;
            $et = $fragment->end_time;
        }

        // 修正切割播放地址
        $player = new Player();
        $player->loadVideo($video);
        $player->clip($st, $et);

        $resource = $player->getResource();

        /**
         * 解序列化
         */
        $video->keyFrames = unserialize($video->keyframes);
        unset($video->keyframes);
        $video->info = $video->info()->get()->toArray();
        $video->pictures = $video->pictures()->get()->toArray();
        $video->content_rating = $video->content_rating()->get()->toArray();
        $video->category = $video->category()->get()->toArray();

        unset($video->resource);
        $fragment->video = $video->toArray();
        $fragment->resource = $resource;
        $fragment->parameters = array(
            'adjustKeyFramesTime' => $player->getAdjustKeyFramesTime(),
            'fSt' => $player->getFSt(),
            'fEt' => $player->getFEt(),
            'vSt' => $player->getVSt(),
            'vEt' => $player->getVEt()
        );

        $subtitles = $video->subtitles()->where('type', '=', 'JSON')->get();
        $subtitles2 = array();
        foreach ($subtitles as $s) {
            $subtitles2[] = array(
                'is_original' => $s->is_original,
                'language' => $s->language,
                'type' => $s->type,
                'url' => strtolower(URL::to('subtitle', array($video->guid)) .
                    '/' . "$st-$et" .
                    '/' . $s->language .
                    '.' . $s->type),
            );
        }

        $fragment->subtitles = $subtitles2;

        // TODO: URL!
        $fragment->share_url = action('FragmentController@getShow', array('guid' => $fragment->guid));

        // TODO: 封面地址

        return $this->encodeResult('10801', 'succeed', $fragment->toArray());
    }


}