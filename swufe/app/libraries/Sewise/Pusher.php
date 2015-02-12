<?php

namespace HiHo\Sewise;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use J20\Uuid\Uuid;
use HiHo\Model\Video;
use HiHo\Model\VideoInfo;
use HiHo\Model\VideoTvPlay;
use HiHo\Model\VideoResource;
use HiHo\Model\VideoPicture;
use HiHo\Model\VideoContentRating;
use HiHo\Model\VideoTag;
use HiHo\Model\TvChannel;
use HiHo\Model\Subtitle;
use HiHo\Model\Tag;
use HiHo\Model\RestLog;

/**
 * Class Pusher
 * @package HiHo\Sewise
 * @author Jun<jun.zhu@autotiming.com>
 */
class Pusher
{

    // 构造函数
    public function __construct()
    {
        set_time_limit(60); //设置超时时间

        //记录请求日志
        $this->encodeResult('1', 'Sewise Data');

        //ToDo:使用队列机制
    }

    public function create($post = "")
    {

        $media = json_decode($post);

        // 验证参数格式   
        if (empty($media) || !is_object($media)) {
            return $this->encodeResult('20000', 'Parameter format is incorrect');
        }

        // 验证必要参数是否存在
        if ($result = $this->verifyParam($media)) {
            return $result;
        };

        // 判断origin_id是否存在
        if ($this->isVideoIdExist($media->video->origin_id)) {

            $exist_media = $this->getExistVideo($media->video->origin_id);

            // 增加不同语言信息
            $video_is_original = $this->saveInfo($media->info, $exist_media->video_id);
            /*if (empty($video_info)) {
                return $this->encodeResult('20003', 'Video already exists');
            }*/

            // 增加不同语言封面帧图片
            $this->savePicture($media->pictures, $exist_media->video_id, $exist_media->guid, $video_is_original);

            // 增加不同语言字幕
            $this->saveSubtitle($media->subtitles, $exist_media->video_id, $video_is_original);

            // 增加不同语言标签
//            if (!empty($media->tags)) {
//                $this->saveTag($media->tags, $exist_media->video_id);
//            }
            return $this->encodeResult('10001', 'Video has been updated');
        }

        // 正常保存数据
        $video = $this->saveVideo($media->video);
        $video_id = $video->video_id; //存入的视频Id
        $video_guid = $video->video_guid; //存入的视频guid
        $video_is_original = $this->saveInfo($media->info, $video_id);
        $this->saveResource($media->resources, $video_id, $video_is_original);
        $this->savePicture($media->pictures, $video_id, $video_guid, $video_is_original);
        $this->saveSubtitle($media->subtitles, $video_id, $video_is_original, 'add');

        //保存评级，电视频道，标签数据
        if (!empty($media->ratings)) {
            $this->saveRating($media->ratings, $video_id);
        }

        if (!empty($media->tvs)) {
            $this->saveTv($media->tvs, $video_id);
        }

        if (!empty($media->tags)) {
            $this->saveTag($media->tags, $video_id);
        }

        return $this->encodeResult('1', 'succeed');
    }

    public function delete($post = "")
    {

        // 验证参数
        if (!empty($post['id']) && !empty($post['lang'])) {
            $validator = Validator::make($post, array(
                'id' => 'required',
                'lang' => 'required'
            ));

            if ($validator->fails()) {
                $messages = $validator->messages();
                return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first()));
            }
        } else {
            return $this->encodeResult('20002', 'id&lang is empty');
        }

        $origin_id = $post['id'];
        $language = $post['lang'];

        // 是否存在视频
        if ($this->isVideoIdExist($origin_id)) {
            // 获取要删除的视频id
            $media_id = $this->getExistVideo($origin_id)->video_id;

            $affectedRows = VideoInfo::where('video_id', $media_id)->where('language', $language)->delete();
            if ($affectedRows > 0) {
                Subtitle::where('video_id', $media_id)->where('language', $language)->delete();
            } else {
                return $this->encodeResult('20004', 'Video does not exist');
            }

            // 检测是否整个视频都需要删除
            $count = VideoInfo::where('video_id', $media_id)->count();
            if ($count == 0) {
                Video::where('video_id', $media_id)->delete();
                VideoResource::where('video_id', $media_id)->delete();
                VideoPicture::where('video_id', $media_id)->delete();
                VideoContentRating::where('video_id', $media_id)->delete();
                VideoTvPlay::where('video_id', $media_id)->delete();
                VideoTag::where('video_id', $media_id)->delete();
            }
            return $this->encodeResult('1', 'succeed');
        } else {
            return $this->encodeResult('20004', 'Video does not exist');
        }
    }

    public function modify($post = "")
    {

        // 验证参数
        if (!empty($post['method']) && !empty($post['data']) && !empty($post['id'])) {
            $validator = Validator::make($post, array(
                'method' => 'required',
                'data' => 'required',
                'id' => 'required'
            ));

            if ($validator->fails()) {
                $messages = $validator->messages();
                return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first()));
            }
        } else {
            return $this->encodeResult('20002', 'method&data&id is empty');
        }

        $method = $post['method'];
        $lang = $post['lang'];
        $origin_id = $post['id'];
        $data = json_decode($post['data']);

        switch ($method) {
            case 'video':
                $result = $this->modifyVideo($origin_id, $data);
                return $result;
                break;
            case 'info':
                $result = $this->modifyInfo($origin_id, $lang, $data);
                return $result;
                break;
            case 'resources':
                $result = $this->modifyResources($origin_id, $data);
                return $result;
                break;
            case 'pictures':
                $result = $this->modifyPictures($origin_id, $data);
                return $result;
                break;
            case 'subtitles':
                $result = $this->modifySubtitles($origin_id, $lang, $data);
                return $result;
                break;
            case 'tags':
                $result = $this->modifyTags($origin_id, $data);
                return $result;
                break;
            case 'ratings':
                $result = $this->modifyRatings($origin_id, $data);
                return $result;
                break;
            case 'tvs':
                $result = $this->modifyTvs($origin_id, $data);
                return $result;
                break;
            default:
                echo "error method";
        }
    }

    /**
     * 保存视频主信息
     * @param type $media
     */
    private function saveVideo($media)
    {

        $video = new Video();
        $video->guid = Uuid::v4();
        $video->country = $media->country;
        $video->language = $this->languageConvert($media->language);
//        $video->district = $media->district;
        ##rename by zhuzhengqian
        $video->length = $media->district;
        $video->source_id = 2;
        $video->origin_id = $media->origin_id;
        $video->copyright = isset($media->copyright) ? $media->copyright : '';
        $video->aspect_ratio = isset($media->aspect_ratio) ? $media->aspect_ratio : '';
        $video->save();

        //Todo 保存分类信息

        return $video;
    }

    /**
     * 保存视频信息(多语言)
     * @param type $media
     * @param type $video_id
     */
    private function saveInfo($media, $video_id)
    {

        // 查询是否需要更新
        if ($this->isInfoExist($video_id, $media->language)) {
            return $media->is_original;
        }

        $info = new VideoInfo();
        $info->video_id = $video_id;
        $info->is_original = $media->is_original;
        $info->language = $this->languageConvert($media->language);
        $info->title = $media->title;
        $info->author = isset($media->author) ? $media->author : '';
        $info->description = isset($media->description) ? $media->description : '';
        $info->save();

        return $media->is_original;
    }

    /**
     * 保存视频资源
     * @param type $media
     * @param type $video_id
     */
    private function saveResource($media, $video_id, $is_original)
    {
        foreach ($media as $item) {
            // 查询是否需要更新
            if ($this->isResourceExist($video_id, $item->src)) {
                continue;
            }
            $resources = new VideoResource();
            $resources->video_id = $video_id;
            $resources->is_original = $is_original;
            $resources->type = $item->type;
            $resources->width = $item->width;
            $resources->height = $item->height;
            $resources->src = $item->src;
            $resources->save();
        }
    }

    /**
     * 保存封面和帧图片
     * @param type $media
     * @param type $video_id
     * @param type $video_ggid
     */
    private function savePicture($media, $video_id, $video_guid, $is_original)
    {
        foreach ($media as $item) {
            // 查询是否需要更新
            if ($this->isPictureExist($video_id, $item->url)) {
                continue;
            }
            $pictures = new VideoPicture();
            $pictures->video_id = $video_id;
            $pictures->is_original = $is_original;
            $pictures->key = $item->key;
            $pictures->type = $item->type;
            $pictures->width = $item->width;
            $pictures->height = $item->height;
            $pictures->src = $item->url;
            $pictures->occurrence = $item->occurrence;
            $pictures->save();

            // 保存缩略图到本地
//            if ($item['key'] == 'cover') {
//                $pull_handler = new PullHandler();
//                $pull_handler->saveThum($item['url'], $video_guid, '0');
//            }
        }
    }

    /**
     * 保存视频字幕
     * @param type $media
     * @param type $video_id
     */
    private function saveSubtitle($media, $video_id, $is_original)
    {

        // 储存所有字幕到 DB
        foreach ($media as $item) {
            // 查询是否需要更新
//            if ($this->isSubtitleExist($video_id, $this->languageConvert($item->language), $item->type, $item->accuracy)) {
//                continue;
//            }

            $subtitle = Subtitle::where('video_id',$video_id)->where('accuracy',$item->accuracy)->where('type',strtoupper($item->type))->first();
            if($subtitle){
                $crud = 'update';
            }else{
                $crud = 'add';
            }

            $this->loadSubtitle($video_id, $item->at_id, strtoupper($item->type), $this->languageConvert($item->language), $item->url, $item->accuracy, $is_original, $crud);
        }
    }

    /**
     * 保存视频评级
     * @param type $media
     * @param type $video_id
     */
    private function saveRating($media, $video_id)
    {

        foreach ($media as $item) {
            $ratings = new VideoContentRating();
            $ratings->video_id = $video_id;
            $ratings->country = $item->country;
            $ratings->rating = $item->level;
            $ratings->save();
        }
    }

    /**
     * 保存电视台频道
     * @param type $media
     * @param type $video_id
     */
    private function saveTv($media, $video_id)
    {

        foreach ($media as $item) {

            // 是否存在channel
            $channel_row = TvChannel::where('sewise_id', $item->channel)->get()->first();

            if ($channel_row) {
                $tvplay = new VideoTvPlay();
                $tvplay->video_id = $video_id;
                $tvplay->channel_id = $channel_row->id;
                $tvplay->playtime = isset($item->playtime) ? $item->playtime : 'undefined';
                $tvplay->save();
            }

            //ToDo 不存在电视台自动添加
        }
    }

    /**
     * 保存视频标签
     * @param type $media
     * @param type $video_id
     */
    private function saveTag($media, $video_id)
    {

        foreach ($media as $item) {
            // 是否存在标签
            $tag_row = Tag::where('name', $item)->get()->first();

            if ($tag_row) {
                $video_tags = new VideoTag();
                $video_tags->video_id = $video_id;
                $video_tags->tag_id = $tag_row->id;
                $video_tags->save();
            } else {
                $tags = new Tag();
                $tags->name = $item;
                $tags->save();

                $video_tags = new VideoTag();
                $video_tags->video_id = $video_id;
                $video_tags->tag_id = $tags->id;
                $video_tags->save();
            }
        }
    }

    /**
     * 修改视频video
     * @param type $data
     * @return type
     */
    private function modifyVideo($origin_id, $data)
    {

        // 验证data.video params
        if (isset($data)) {
            $validator = Validator::make((array)$data, array(
                'country' => 'required',
                'language' => 'required',
                'district' => 'required',
                'source_id' => 'required',
                'category_id' => 'required'
            ));

            if ($validator->fails()) {
                $messages = $validator->messages();
                return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first() . '(video)'));
            }
        } else {
            return $this->encodeResult('20002', 'data.video is empty');
        }

        // 是否存在视频
        if ($this->isVideoIdExist($origin_id)) {
            $update_arr = array(
                'country' => $data->country,
                'language' => $data->language,
//                'district' => $data->district,
            ##by zhuzhengqian
                'length' => $data->district,
                'source_id' => 2,
                'copyright' => isset($data->copyright) ? $data->copyright : '',
                'aspect_ratio' => isset($data->aspect_ratio) ? $data->aspect_ratio : ''
            );
            $affectedRows = Video::where('origin_id', $origin_id)->update($update_arr);

            if ($affectedRows > 0) {
                return $this->encodeResult('1', 'succeed');
            } else {
                return $this->encodeResult('30001', 'Handling Exceptions');
            }
        } else {
            return $this->encodeResult('20004', 'Video does not exist');
        }

        //ToDo Category Id
    }

    /**
     * 修改视频info
     * @param type $data
     * @return type
     */
    private function modifyInfo($origin_id, $lang, $data)
    {

        // 验证data.info params
        if (isset($data)) {
            $validator = Validator::make((array)$data, array(
                'title' => 'required',
                'is_original' => 'required'
            ));

            if ($validator->fails()) {
                $messages = $validator->messages();
                return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first() . '(info)'));
            }
        } else {
            return $this->encodeResult('20002', 'data.info is empty');
        }

        // 是否存在视频
        if ($this->isVideoIdExist($origin_id)) {

            $video_id = Video::where('origin_id', $origin_id)->get()->first()->video_id;

            $video_info = VideoInfo::where('video_id', $video_id)->where('language', $lang)->get()->first();
            if ($video_info) {
                $update_arr = array(
                    'title' => $data->title,
                    'author' => isset($data->author) ? $data->author : '',
                    'description' => isset($data->description) ? $data->description : '',
                    'is_original' => $data->is_original
                );


                $affectedRows = VideoInfo::where('video_id', $video_id)->where('language', $lang)->update($update_arr);

                if ($affectedRows > 0) {
                    return $this->encodeResult('1', 'succeed');
                } else {
                    return $this->encodeResult('30001', 'Handling Exceptions');
                }
            } else {
                return $this->encodeResult('20004', 'Video does not exist');
            }
        } else {
            return $this->encodeResult('20004', 'Video does not exist');
        }
    }

    /**
     * 修改视频资源
     * @param type $origin_id
     * @param type $data
     */
    private function modifyResources($origin_id, $data)
    {

        // 验证data.resources params
        foreach ($data as $v) {
            $validator = Validator::make((array)$v, array(
                'type' => 'required',
                'width' => 'required',
                'height' => 'required',
                'src' => 'required'
            ));

            if ($validator->fails()) {
                $messages = $validator->messages();
                return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first() . '(resources)'));
            }
        }

        // 是否存在视频
        if ($this->isVideoIdExist($origin_id)) {

            // 视频信息
            $video_id = Video::where('origin_id', $origin_id)->get()->first()->video_id;

            // 删除
            VideoResource::where('video_id', $video_id)->delete();

            // 写入
            foreach ($data as $v) {
                $resource = new VideoResource();
                $resource->video_id = $video_id;
                $resource->is_original = 1; // default
                $resource->type = $v->type;
                $resource->width = $v->width;
                $resource->height = $v->height;
                $resource->src = $v->src;
                $resource->save();
            }

            return $this->encodeResult('1', 'succeed');
        } else {
            return $this->encodeResult('20004', 'Video does not exist');
        }
    }

    /**
     * 修改视频图片
     * @param type $origin_id
     * @param type $data
     */
    private function modifyPictures($origin_id, $data)
    {
        // 验证data.pictures params
        foreach ($data as $v) {
            $validator = Validator::make((array)$v, array(
                'key' => 'required',
                'type' => 'required',
                'width' => 'required',
                'height' => 'required',
                'url' => 'required'
            ));

            if ($validator->fails()) {
                $messages = $validator->messages();
                return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first() . '(pictures)'));
            }
        }

        // 是否存在视频
        if ($this->isVideoIdExist($origin_id)) {

            // 视频信息
            $video_id = Video::where('origin_id', $origin_id)->get()->first()->video_id;

            // 删除
            VideoPicture::where('video_id', $video_id)->delete();

            // 写入
            foreach ($data as $v) {
                $pictures = new VideoPicture();
                $pictures->video_id = $video_id;
                $pictures->is_original = 1; // default
                $pictures->key = $v->key;
                $pictures->type = $v->type;
                $pictures->width = $v->width;
                $pictures->height = $v->height;
                $pictures->src = $v->url;
                $pictures->occurrence = isset($v->occurrence) ? $v->occurrence : '';
                $pictures->save();
            }

            return $this->encodeResult('1', 'succeed');
        } else {
            return $this->encodeResult('20004', 'Video does not exist');
        }
    }

    /**
     * 修改视频字幕
     * @param type $origin_id
     * @param type $data
     */
    private function modifySubtitles($origin_id, $lang, $data)
    {

        // 验证data.subtitles params
        foreach ($data as $v) {
            $validator = Validator::make((array)$v, array(
                'at_id' => 'required',
                'language' => 'required',
                'type' => 'required',
                'url' => 'required'
            ));
            if ($validator->fails()) {
                $messages = $validator->messages();
                return $this->encodeResult('20001', 'The input parameter is not correct.', array('requiredFields' => $messages->first() . '(subtitles)'));
            }
        }


        // 是否存在视频
        if ($this->isVideoIdExist($origin_id)) {

            // 视频信息
            $video_id = Video::where('origin_id', $origin_id)->get()->first()->video_id;
            $is_original = VideoInfo::where('video_id', $video_id)->where('language', $lang)->get()->first()->is_original;

            // 删除
            Subtitle::where('video_id', $video_id)->where('language', $lang)->delete();

            // 写入subtitles数据
            $this->saveSubtitle($data, $video_id, $is_original);

            return $this->encodeResult('1', 'succeed');
        } else {
            return $this->encodeResult('20004', 'Video does not exist');
        }
    }

    /**
     * 修改视频评级
     * @param type $origin_id
     * @param type $data
     * @return type
     */
    private function modifyRatings($origin_id, $data)
    {
        // 验证data.rating params
        foreach ($data as $v) {
            $validator = Validator::make((array)$v, array(
                'country' => 'required',
                'level' => 'required'
            ));

            if ($validator->fails()) {
                $messages = $validator->messages();
                return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first() . '(rating)'));
            }
        }

        // 是否存在视频
        if ($this->isVideoIdExist($origin_id)) {

            // 视频信息
            $video_id = Video::where('origin_id', $origin_id)->get()->first()->video_id;

            // 删除
            VideoContentRating::where('video_id', $video_id)->delete();

            // 写入
            $this->saveRating($data, $video_id);

            return $this->encodeResult('1', 'succeed');
        } else {
            return $this->encodeResult('20004', 'Video does not exist');
        }
    }

    /**
     * 修改视频所在电视台
     * @param type $origin_id
     * @param type $data
     */
    private function modifyTvs($origin_id, $data)
    {
        // 验证data.tvs params
        foreach ($data as $v) {
            $validator = Validator::make((array)$v, array(
                'channel' => 'required',
                'playtime' => 'required'
            ));

            if ($validator->fails()) {
                $messages = $validator->messages();
                return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first() . '(tvs)'));
            }
        }

        // 是否存在视频
        if ($this->isVideoIdExist($origin_id)) {

            // 视频信息
            $video_id = Video::where('origin_id', $origin_id)->get()->first()->video_id;

            // 删除
            VideoTvPlay::where('video_id', $video_id)->delete();

            // 写入
            $this->saveTv($data, $video_id);

            return $this->encodeResult('1', 'succeed');
        } else {
            return $this->encodeResult('20004', 'Video does not exist');
        }
    }

    /**
     * 修改视频标签
     * @param type $origin_id
     * @param type $data
     */
    private function modifyTags($origin_id, $data)
    {

        // tags是否为空
        if (empty($data)) {
            return $this->encodeResult('20002', 'tags is empty');
        }

        // 是否存在视频
        if ($this->isVideoIdExist($origin_id)) {

            // 视频信息
            $video_id = Video::where('origin_id', $origin_id)->get()->first()->video_id;

            // 删除
            VideoTag::where('video_id', $video_id)->delete();

            // 写入
            $this->saveTag($data, $video_id);

            return $this->encodeResult('1', 'succeed');
        } else {
            return $this->encodeResult('20004', 'Video does not exist');
        }
    }

    /**
     * 获取已存在视频主信息
     * @param type $taskid
     * @return type
     */
    private function getExistVideo($taskid)
    {
        $media = Video::where('origin_id', $taskid)->get()->first();
        return $media;
    }

    /**
     * 判断某 TaskId 的视频是否存在
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $taskid
     * @return bool
     */
    private function isVideoIdExist($taskid)
    {
        $v = Video::where('origin_id', $taskid)->get()->first();
        if ($v) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断视频信息是否重复
     * @param type $video_id
     * @return boolean
     */
    private function isInfoExist($video_id, $language)
    {
        $v = VideoInfo::where('video_id', $video_id)->where('language', $language)->get()->first();
        if ($v) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断视频资源是否重复
     * @param type $video_id
     * @param type $src
     * @return boolean
     */
    private function isResourceExist($video_id, $src)
    {
        $v = VideoResource::where('video_id', $video_id)->where('src', $src)->get()->first();
        if ($v) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断视频图片是否重复
     * @param type $video_id
     * @param type $src
     * @return boolean
     */
    private function isPictureExist($video_id, $src)
    {
        $v = VideoPicture::where('video_id', $video_id)->where('src', $src)->get()->first();
        if ($v) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断字幕是否重复
     * @param type $video_id
     * @param type $type
     * @return boolean
     */
    private function isSubtitleExist($video_id, $language, $type, $accuracy)
    {
        $v = Subtitle::where('video_id', $video_id)->where('language', $language)->where('type', $type)->where('accuracy', $accuracy)->first();
        if ($v) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证Post参数
     * @param type $media
     * @return type
     */
    private function verifyParam($media)
    {

        // 验证data.video params
        if (isset($media->video)) {
            $validator = Validator::make((array)$media->video, array(
                'country' => 'required',
                'language' => 'required',
                'district' => 'required',
                'source_id' => 'required',
                'category_id' => 'required',
                'origin_id' => 'required'
            ));

            if ($validator->fails()) {
                $messages = $validator->messages();
                return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first() . '(video)'));
            }
        } else {
            return $this->encodeResult('20002', 'data.video is empty');
        }

        // 验证data.info params
        if (isset($media->info)) {
            $validator = Validator::make((array)$media->info, array(
                'language' => 'required',
                'title' => 'required',
                'is_original' => 'required'
            ));

            if ($validator->fails()) {
                $messages = $validator->messages();
                return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first() . '(info)'));
            }
        } else {
            return $this->encodeResult('20002', 'data.info is empty');
        }

        // 验证data.resources params
        if (isset($media->resources)) {
            // 验证data.resources params
            foreach ($media->resources as $v) {
                $validator = Validator::make((array)$v, array(
                    'type' => 'required',
                    'width' => 'required',
                    'height' => 'required',
                    'src' => 'required'
                ));

                if ($validator->fails()) {
                    $messages = $validator->messages();
                    return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first() . '(resources)'));
                }
            }
        } else {
            return $this->encodeResult('20002', 'data.resources is empty');
        }

        // 验证data.pictures params
        if (isset($media->pictures)) {
            foreach ($media->pictures as $v) {
                $validator = Validator::make((array)$v, array(
                    'key' => 'required',
                    'type' => 'required',
                    'width' => 'required',
                    'height' => 'required',
                    'url' => 'required'
                ));

                if ($validator->fails()) {
                    $messages = $validator->messages();
                    return $this->encodeResult('20001', 'The input parameter is not correct', array('requiredFields' => $messages->first() . '(pictures)'));
                }
            }
        } else {
            return $this->encodeResult('20002', 'data.pictures is empty.');
        }

        // 验证data.subtitles params
        if (isset($media->subtitles)) {
            foreach ($media->subtitles as $v) {
                $validator = Validator::make((array)$v, array(
                    'at_id' => 'required',
                    'language' => 'required',
                    'type' => 'required',
                    'url' => 'required',
                    'accuracy' => 'required'
                ));
                if ($validator->fails()) {
                    $messages = $validator->messages();
                    return $this->encodeResult('20001', 'The input parameter is not correct.', array('requiredFields' => $messages->first() . '(subtitles)'));
                }
            }
        } else {
            return $this->encodeResult('20002', 'data.subtitle is empty');
        }
    }

    /**
     * 返回结果encode并报错记录
     * @param type $msgcode
     * @param type $message
     * @param type $response
     * @return type
     */
    private function encodeResult($msgcode, $message = NULL, $response = NULL)
    {
        /**
         * 记录接口的 Requset 和返回值
         */
        $log = new RestLog();

//        $log->type = 20;
        $log->request = serialize(Input::all());
        $log->response = serialize($response);
        $log->request_route = Route::currentRouteName();
        $log->msgcode = $msgcode;
        $log->message = $message;
        $log->client_ip = Request::getClientIp();
        $log->client_useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;

        $log->save();

        /**
         * 返回 JSON 的统一返回值结构
         */
        $result = array(
            'request_id' => $log->id,
            'msgcode' => $msgcode,
            'message' => $message,
            'response' => $response,
            'version' => $option = Config::get('hiho.pushapi.version'),
            'servertime' => time()
        );

        return json_encode($result);
    }

    /**
     * 拉取新字幕
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $video_id
     * @param $type
     * @param $language
     * @param $url
     */
    public function loadSubtitle($video_id, $at_id, $type, $language, $url, $accuracy, $i, $crud = 'add')
    {
        if ($i == 1) {
            $is_original = 1;
        } else {
            $is_original = 0;
        }

        // 多次请求保证抓取
        $get_content_count = 0;
        while ($get_content_count < 3 && ($json = @file_get_contents($url)) === FALSE) {
            $get_content_count++;
        }

        //ToDo 如果没拉取到数据，返回，等待下次拉取更新
        if ($crud == 'update') {

            Subtitle::where('video_id', $video_id)->where('type', $type)->where('language', $language)->where('accuracy', $accuracy)->update(array(
                'content' => $json,
                'at_id' => $at_id,
                'url' => $url
            ));

            if ($type == 'JSON') {

                Subtitle::where('video_id', $video_id)->where('type', 'SRT')->where('language', $language)->where('accuracy', $accuracy)->update(array(
                    'content' => Subtitle::generateSrt($json)
                ));

                Subtitle::where('video_id', $video_id)->where('type', 'TXT')->where('language', $language)->where('accuracy', $accuracy)->update(array(
                    'content' => Subtitle::generateTxt($json)
                ));

                //更新keyframes
                $video = Video::find($video_id);
                $video->keyframes = serialize(json_decode($json)->keyframes);
                $video->save();
            } else {

            }
        } else {


            $subtitle = new Subtitle();
            $subtitle->video_id = $video_id;
            $subtitle->is_original = $is_original;
            $subtitle->type = $type;
            $subtitle->accuracy = $accuracy;
            $subtitle->language = $language;
            $subtitle->content = $json;
            $subtitle->at_id = $at_id;
            $subtitle->url = $url;

            $subtitle->save();
//            echo $video_id;
//exit();
            /**
             * 遇到 JSON 自动生成 SRT 和 TXT 字幕入库
             */

            if ($type == 'JSON') {
                $subtitle_content_json = $subtitle->content;
                $subtitle = new Subtitle();
                $subtitle->video_id = $video_id;
                $subtitle->is_original = $is_original;
                $subtitle->type = 'SRT';
                $subtitle->accuracy = $accuracy;
                $subtitle->language = $language;
                $subtitle->content = Subtitle::generateSrt($subtitle_content_json);
                $subtitle->at_id = $at_id;
                $subtitle->save();

                $subtitle = new Subtitle();
                $subtitle->video_id = $video_id;
                $subtitle->is_original = $is_original;
                $subtitle->type = 'TXT';
                $subtitle->accuracy = $accuracy;
                $subtitle->language = $language;
                $subtitle->content = Subtitle::generateTxt($subtitle_content_json);
                $subtitle->at_id = $at_id;
                $subtitle->save();

                //增加keyframes
                $video = Video::find($video_id);
                $video->keyframes = serialize(json_decode($json)->keyframes);
                $video->save();
            }
        }

        return;
    }

    /**
     * @param $json
     */
    private function subtitleJsonConvert($json)
    {
        $subtitle = json_decode($json);
        $srt = $subtitle->srt;
        return json_encode(array('srt' => $srt));
    }

    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    private function languageConvert($lang)
    {
        if ($lang == 'zh-CN') {
            return 'zh_cn';
        } elseif ($lang == 'en') {
            return 'en';
        } else {
            return $lang;
        }
    }

}
