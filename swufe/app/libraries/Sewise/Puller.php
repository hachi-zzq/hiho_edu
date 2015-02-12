<?php namespace HiHo\Sewise;

use Guzzle\Http\Client;

/**
 * Class Puller
 * @package HiHo\Sewise
 * @author ZhuJun<jun.zhu@autotiming.com>
 */
class Puller {

    protected $url;
    protected $country;
    protected $language;
    protected $sewise_language;
    protected $is_complete;

    // 构造函数
    public function __construct() {

        $this->url = \Config::get('hiho.sewise')['pull_api_url'];
        $this->country = 'US';
        $this->language = 'en';
        $this->sewise_language = 'zh_CN';
        $this->is_complete = TRUE;
    }

    /**
     * setLanguages
     * @param type $languages
     */
    public function setLanguage($languages) {
        $this->language = $languages;
    }

    /**
     *
     * @param type $country
     */
    public function setCountry($country) {
        $this->country = $country;
    }

    /**
     *
     * @param type $language
     */
    public function setSewiseLanguage($language) {
        $this->sewise_language = $language;
    }

    /**
     *
     * @param type $is_complete
     */
    public function setIsComplete($is_complete) {
        $this->is_complete = $is_complete;
    }

    /**
     * 拉取数据主程序
     * @return int
     */
    public function run() {

        // 设置拉取环境
        set_time_limit(0);
        \DB::statement('set global max_allowed_packet = 2*1024*1024*10');

        $result = $this->getSwMediaList($this->sewise_language);
        $total_page = $result['total_page']; // 视频总页数
        $total_count = 0; // 视频拉取数
        $create_count = 0;
        $update_count = 0;

        for ($i = 1; $i <= $total_page; $i++) {

            // 拉取失败
            if (isset($result['errors'])) {
                return;
            }

            // 拉取成功
            if (isset($result['success']) && !empty($result['record'])) {

                $result = $this->getSwMediaList($this->sewise_language, $i);
                $media = $result['record'];

                foreach ($media as $item) {

                    $origin_id = $item['taskid'];
                    $media_detail = $this->getSwMedia($origin_id, $this->sewise_language)['record'];

                    // 去除卫星数据
                    if (!$this->is_complete) {
                        if ($media_detail['source'] == 5) {
                            continue;
                        }
                    }

                    // 去除没封面视频
                    if ($media_detail['cover']['src'] == '') {
                        continue;
                    }

                    $total_count++;

                    // 设置国家和语言
                    $subtitle_lang = $media_detail['subtitle_lang'];

                    foreach ($media_detail['subtitle_url'] as $k => $v) {
                        // 寻找源视频语言
                        if ($v['translate'] == '0') {
                            if ($k == 'en') {
                                $this->country = 'US';
                                $this->language = 'en';
                            } elseif ($k == 'zh_cn') {
                                $this->country = 'CN';
                                $this->language = 'zh-CN';
                            }
                        }
                    }

                    // 数据转换
                    $data = $this->ChangeDataFromSwMedia($media_detail, $subtitle_lang);

                    $is_original = ($media_detail['subtitle_url'][$subtitle_lang]['translate'] == '0') ? 1 : 0;

                    // 判断origin_id是否存在
                    if ($this->isVideoIdExist($origin_id)) {
                        $exist_media = $this->getExistVideo($origin_id);
                        $this->saveInfo($data['info'], $exist_media->video_id, $is_original);
                        $this->saveResource($data['resources'], $exist_media->video_id, $is_original);
                        $this->savePicture($data['pictures'], $exist_media->video_id, $exist_media->guid, $is_original); //guid是否一致
                        $this->saveSubtitle($data['subtitles'], $exist_media->video_id, $is_original);
                        // 跳出并回到循环开始
                        $update_count++;
                        continue;
                    }

                    // 正常保存Pull数据


                    $video_result = $this->saveVideo($data['video']);
                    $video_id = $video_result->video_id; //存入的视频Id
                    $video_guid = $video_result->video_guid; //存入的视频guid
                    $this->saveInfo($data['info'], $video_id, $is_original);
                    $this->saveResource($data['resources'], $video_id, $is_original);
                    $this->savePicture($data['pictures'], $video_id, $video_guid, $is_original);
                    $this->saveSubtitle($data['subtitles'], $video_id, $is_original);
//                if (!empty($data['tags'])) {
//                    $this->saveTag($data['tags'], $video_id);
//                }
                    $create_count++;
                }
            }
        }
        $result = array(
            'total_count' => $total_count,
            'create_count' => $create_count,
            'update_count' => $update_count
        );
        return $result;
    }

    /**
     * 数据组装
     * @param type $media
     */
    private function ChangeDataFromSwMedia($media, $lang) {

        $data = array();

        $data['video'] = array(
            'country' => $this->country,
            'language' => $this->language,
            'district' => $media['duration'],
            'source_id' => $media['source'],
            'category_id' => $media['type'],
            'origin_id' => $media['taskid'],
            'copyright' => '',
            'aspect_ratio' => ''
        );

        $data['info'] = array(
            'language' => $this->getRightLanguage($lang),
            'title' => $media['title'],
            'author' => empty($media['author']) ? '' : $media['author'],
            'description' => empty($media['description']) ? '' : $media['description']
        );


        foreach ($media['video_url'] as $szie => $media_detail) {
            foreach ($media_detail as $media_format => $media_url) {
                $media_size = explode('x', $szie);
                $data['resources'][] = array(
                    'type' => $media_format,
                    'width' => $media_size[0],
                    'height' => $media_size[1],
                    'src' => $media_url
                );
            }
        }

        $data['pictures'][] = array(
            'key' => 'cover',
            'type' => 'JPEG', //default
            'width' => '200', //default
            'height' => '200', //default
            'url' => empty($media['cover']['src']) ? '' : $media['cover']['src'],
            'occurrence' => empty($media['cover']['time']) ? '' : $media['cover']['time']
        );

        if (!empty($media['frames'])) {
            foreach ($media['frames'] as $item) {
                $data['pictures'][] = array(
                    'key' => 'frames',
                    'type' => 'JPEG', //default
                    'width' => '200', //default
                    'height' => '200', //default
                    'url' => empty($item['src']) ? '' : $item['src'],
                    'occurrence' => empty($item['time']) ? '' : $item['time']
                );
            }
        }

        if (!empty($media['subtitle_url'])) {
            $data['subtitles'][] = array(
                'at_id' => '1', //default
                'language' => $lang,
                'type' => 'XML',
                'url' => $media['subtitle_url'][$lang]['xmlurl']
            );
            $data['subtitles'][] = array(
                'at_id' => '1', //default
                'language' => $lang,
                'type' => 'JSON',
                'url' => $media['subtitle_url'][$lang]['url']
            );
        } else {
            $data['subtitles'][] = array(
                'at_id' => '1', //default
                'language' => 'error',
                'type' => 'XML',
                'url' => 'error'
            ); //ToDo error
        }

        if (!empty($media['tags'])) {
            $tags = explode(';', $media['tags']);
            foreach ($tags as $item) {
                if ($item != '') {
                    $data['tags'][] = $item;
                }
            }
        }
        return $data;
    }

    /**
     * 拉取 SW 的视频列表
     * @param type $lang
     * @param type $page
     * @return array
     */
    private function getSwMediaList($lang = 'en', $page = 1) {
        $client = new Client();

        $request = $client->get($this->url . '?do=index&lang=' . $lang . '&page=' . $page, ['timeout' => 5]);

        try {
            $response = $request->send();
            $data = $response->json();
        } catch (Exception $e) {
            $data = array();
        }

        return $data;
    }

    /**
     * 拉取SW详细信息
     * @param type $taskid
     * @param type $lang
     * @return array
     */
    private function getSwMedia($taskid, $lang = 'en') {
        $client = new Client();

        $request = $client->get($this->url . '?do=index&op=detail&taskid=' . $taskid . '&lang=' . $lang, ['timeout' => 5]);
        try {
            $response = $request->send();
            $data = $response->json();
        } catch (Exception $e) {
            $data = array();
        }

        return $data;
    }

    /**
     * 保存视频主信息
     * @param type $media
     */
    private function saveVideo($media) {
        $video = new \Video();
        $video->guid = \Uuid::v4();
        $video->country = $media['country'];
        $video->language = $media['language'];
        $video->district = $media['district'];
        $video->source_id = $media['source_id'];
        $video->origin_id = $media['origin_id'];
        $video->copyright = $media['copyright'];
        $video->aspect_ratio = $media['aspect_ratio'];
        $video->save();

        return $video;
        //Todo 保存分类信息
    }

    /**
     * 保存视频信息(多语言)
     * @param type $media
     * @param type $video_id
     */
    private function saveInfo($media, $video_id, $is_original) {


        // 查询是否需要更新
        if ($this->isInfoExist($video_id, $media['language'])) {
            return;
        }

        $info = new \VideoInfo();
        $info->video_id = $video_id;
        $info->is_original = $is_original;
        $info->language = $media['language'];
        $info->title = $media['title'];
        $info->author = $media['author'];
        $info->description = $media['description'];
        $info->save();
    }

    /**
     * 保存视频资源
     * @param type $media
     * @param type $video_id
     */
    private function saveResource($media, $video_id, $is_original) {
        foreach ($media as $item) {
            // 查询是否需要更新
            if ($this->isResourceExist($video_id, $item['src'])) {
                continue;
            }
            $resources = new \VideoResource();
            $resources->video_id = $video_id;
            $resources->is_original = $is_original;
            $resources->type = $item['type'];
            $resources->width = $item['width'];
            $resources->height = $item['height'];
            $resources->src = $item['src'];
            $resources->save();
        }
    }

    /**
     * 保存封面和帧图片
     * @param type $media
     * @param type $video_id
     * @param type $video_ggid
     */
    private function savePicture($media, $video_id, $video_guid, $is_original) {
        foreach ($media as $item) {
            // 查询是否需要更新
            if ($this->isPictureExist($video_id, $item['url'])) {
                continue;
            }
            $pictures = new \VideoPicture();
            $pictures->video_id = $video_id;
            $pictures->is_original = $is_original;
            $pictures->key = $item['key'];
            $pictures->type = $item['type'];
            $pictures->width = $item['width'];
            $pictures->height = $item['height'];
            $pictures->src = $item['url'];
            $pictures->occurrence = $item['occurrence'];
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
    private function saveSubtitle($media, $video_id, $is_original) {

        // 储存所有字幕到 DB
        foreach ($media as $item) {
            // 查询是否需要更新
            if ($this->isSubtitleExist($video_id, $item['language'], $item['type'])) {
                continue;
            }
            $pull_handler = new PullHandler();
            $pull_handler->loadSubtitle($video_id, $item['type'], $item['language'], $item['url'], $is_original);
        }
    }

    /**
     * 保存视频Tag
     * @param type $media
     * @param type $video_id
     */
//    private function saveTag($media, $video_id) {
//        foreach ($media as $item) {
//            $tags = new \VideoTag();
//            $tags->video_id = $video_id;
//            $tags->tag_id = \DB::table('tags')->where('name', $v)->pluck('id');
//            $tags->save();
//        }
//    }

    /**
     * 获取已存在视频主信息
     * @param type $taskid
     * @return type
     */
    private function getExistVideo($taskid) {
        $media = \Video::where('origin_id', $taskid)->get()->first();
        return $media;
    }

    /**
     * 判断某 TaskId 的视频是否存在
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $taskid
     * @return bool
     */
    private function isVideoIdExist($taskid) {
        $v = \Video::where('origin_id', $taskid)->get()->first();
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
    private function isInfoExist($video_id, $language) {
        $v = \VideoInfo::where('video_id', $video_id)->where('language', $language)->get()->first();
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
    private function isResourceExist($video_id, $src) {
        $v = \VideoResource::where('video_id', $video_id)->where('src', $src)->get()->first();
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
    private function isPictureExist($video_id, $src) {
        $v = \VideoPicture::where('video_id', $video_id)->where('src', $src)->get()->first();
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
    private function isSubtitleExist($video_id, $language, $type) {
        $v = \Subtitle::where('video_id', $video_id)->where('language', $language)->where('type', $type)->get()->first();
        if ($v) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取正确的语言格式
     * @param type $lang
     * @return string
     */
    private function getRightLanguage($lang) {

        if ($lang == 'zh_cn') {
            return 'zh-CN';
        } elseif ($lang == 'en') {
            return 'en';
        }
    }

}
