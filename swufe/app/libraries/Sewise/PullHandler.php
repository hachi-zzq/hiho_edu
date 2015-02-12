<?php namespace HiHo\Sewise;

use HiHo\Model\Video;
use HiHo\Model\VideoCategory;
use HiHo\Model\VideoInfo;
use HiHo\Model\VideoTvPlay;
use HiHo\Model\VideoResource;
use HiHo\Model\VideoPicture;
use HiHo\Model\VideoContentRating;
use HiHo\Model\VideoTag;
use HiHo\Model\Subtitle;
use HiHo\Model\Tag;

/**
 * User: luyu
 * Date: 13-11-14
 * Time: 上午9:37
 */
class PullHandler
{

    public function handle()
    {

    }

    /**
     * 保存缩略图到本地data/thumbnail
     * @author hualong
     * @param $thum_url
     * @param $guid
     * @param $pinter
     * @return bool
     */
    public function saveThum($thum_url, $guid, $pinter)
    {
        if ($thum_url == "")
            return false;
        $ext = strrchr($thum_url, ".");
        if ($ext != ".gif" && $ext != ".jpg" && $ext != ".png" && $ext != ".jpeg")
            return false;
        $path = 'data/thumbnail/';
        $filename = $path . $guid . '_' . $pinter . $ext;

        if (file_exists($filename)) {
            return false;
        }
        ob_start();
        readfile($thum_url);
        $img = ob_get_contents();
        ob_end_clean();
        $size = strlen($img);
        $fp = fopen($filename, "a");
        if (!$fp)
            return false;
        fwrite($fp, $img);
        fclose($fp);
        return true;
    }

    /**
     * 拉取新字幕
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $video_id
     * @param $type
     * @param $language
     * @param $url
     */

    public function loadSubtitle($video_id, $type, $language, $url, $i, $crud = 'add')
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

        //如果没拉取到数据，返回，等待下次拉取更新
        if ($crud == 'update') {
            if ($type == 'JSON') {
                $subtitle = Subtitle::whereRaw("type = ? AND video_id = ? AND language = ?", array('JSON', $video_id, $language))->first();
                $subtitle->content = $json;
                $subtitle->url = $url;
                $subtitle->is_original = $is_original;
                $subtitle->save();
                $subtitle = Subtitle::whereRaw("type = ? AND video_id = ? AND language = ?", array('SRT', $video_id, $language))->first();
                $subtitle->content = Subtitle::generateSrt($json);
                $subtitle->is_original = $is_original;
                $subtitle->save();
                $subtitle = Subtitle::whereRaw("type = ? AND video_id = ? AND language = ?", array('TXT', $video_id, $language))->first();
                $subtitle->content = Subtitle::generateTxt($json);
                $subtitle->is_original = $is_original;
                $subtitle->save();
            } else {
                $subtitle = Subtitle::whereRaw("type = ? AND video_id = ? AND language = ?", array('XML', $video_id, $language))->first();
                $subtitle->content = $json;
                $subtitle->url = $url;
                $subtitle->is_original = $is_original;
                $subtitle->save();
            }
        } else {
            $subtitle = new Subtitle();
            $subtitle->video_id = $video_id;
            $subtitle->is_original = $is_original;
            $subtitle->type = $type;
            $subtitle->language = $language;
            $subtitle->content = $json;
            $subtitle->url = $url;
            $subtitle->save();

            /**
             * 遇到 JSON 自动生成 SRT 和 TXT 字幕入库
             */
            if ($type == 'JSON') {
                $json = $subtitle->content;
                $subtitle = new Subtitle();

                $subtitle->video_id = $video_id;
                $subtitle->is_original = $is_original;
                $subtitle->type = 'SRT';
                $subtitle->language = $language;
                $subtitle->content = Subtitle::generateSrt($json);
                $subtitle->save();

                $subtitle = new Subtitle();

                $subtitle->video_id = $video_id;
                $subtitle->is_original = $is_original;
                $subtitle->type = 'TXT';
                $subtitle->language = $language;
                $subtitle->content = Subtitle::generateTxt($json);
                $subtitle->save();

                $this->getKeyframes($video_id, $json);
            }
        }

        return;
    }

    /**
     * 抓取关键帧
     * @author ZhuJun<jun.zhu@autotiming.com>
     * @param $video_id
     * @param $json
     */
    public function getKeyframes($video_id, $json)
    {
        $json = json_decode($json, TRUE);
        $video = Video::find($video_id);
        $video->keyframes = serialize($json['keyframes']);
        $video->save();
    }

}
