<?php namespace HiHo\Sewise;

use Guzzle\Http\Client;
use HiHo\Model\FragmentResource;
use HiHo\Model\Video;
use HiHo\Model\VideoResource;
use HiHo\Model\Fragment;

/**
 * Class Player
 * @package HiHo\Sewise
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Mp4
{
    /**
     * 获得 MP4 碎片的链接
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public static function getMp4Resource(Video $video, $st, $et)
    {

        // 先判断库内有没有 MP4
        $fr = self::isFragResourceExist($video, $st, $et);

        // 如没有则去 Sewise 拉取
        if (!$fr) {
            $fr = self::getMp4UrlFromSewise(NULL, $video, $st, $et, 'zh_cn');
        } elseif ($fr->status == 'WAITING') {
            $fr = self::getMp4UrlFromSewise($fr, $video, $st, $et, 'zh_cn');
        }

        // TODO: 可能需要定时清理 status 为 UNDEFINITION 的记录

        return $fr;

    }

    /**
     * 从 Sewise 抓 MP4 文件的 URL
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $task_id
     * @param $st
     * @param $et
     * @param $lang
     */
    public static function getMp4UrlFromSewise($fr, Video $video, $st, $et, $lang)
    {
        // TODO: source_id 判断
        $task_id = $video->origin_id;
        $vdn_server = VideoResource::where('video_id', $video->video_id)->where('type', 'flv')->first();
        $http_info = parse_url($vdn_server->src);
        if ($http_info['host'] == "219.232.161.208") {
            $api_url = 'http://cn01.vdn.autotiming.com/service/api/';
        } else if ($http_info['host'] == "54.199.247.59") {
            $api_url = 'http://jp01.vdn.autotiming.com/service/api';
        } else {
            $api_url = 'http://cn01.vdn.autotiming.com/service/api';
        }


        /* $client = new Client();
         $request = $client->get($api_url . '?do=index&op=downloadmpf&taskid=' . $task_id
             . '&starttime=' . $st
             . '&endtime=' . $et
             , ['timeout' => 4]);*/

        $request = Fragment::getImage($api_url . '?do=index&op=downloadmpf&taskid=' . $task_id
            . '&starttime=' . $st
            . '&endtime=' . $et);

        //获取Mp4分辨率
        $video_resource = VideoResource::where('video_id', $video->video_id)->where('type', 'mp4')->first();
        if ($video_resource) {
            $mp4_width = $video_resource->width;
            $mp4_height = $video_resource->height;
        } else {
            $mp4_width = 999;
            $mp4_height = 999;
        }


        if (!$fr) {
            $fr = new FragmentResource();
        }

        if (!$request) {

            /*
            $response = $request->send();
            $data = $response->json();*/


            $fr->video_id = $video->video_id;
            $fr->is_original = 0;
            $fr->start_time = $st;
            $fr->end_time = $et;
            $fr->type = 'mp4';
            $fr->width = $mp4_width;
            $fr->height = $mp4_height;
            $fr->src = '';
            $fr->status = 'WAITING';
            $fr->save();
            return $fr;
        }


        $data = json_decode($request);


        if (isset($data->errors)) {
            // 错误，如不存在 MP4 文件
            $fr->video_id = $video->video_id;
            $fr->is_original = 0;
            $fr->start_time = $st;
            $fr->end_time = $et;
            $fr->type = 'mp4';
            $fr->width = $mp4_width;
            $fr->height = $mp4_height;
            $fr->src = '';
            $fr->status = 'UNDEFINITION';
            $fr->save();
        } elseif (isset($data->url) and $data->url) {
            // 成功获得 MP4 URL
            $fr->video_id = $video->video_id;
            $fr->is_original = 0;
            $fr->start_time = $st;
            $fr->end_time = $et;
            $fr->type = 'mp4';
            $fr->width = $mp4_width;
            $fr->height = $mp4_height;
            $fr->src = $data->url;
            $fr->status = 'NORMAL';
            $fr->save();

        } else {
            // 无错误，如正在截取中的情况
            $fr->video_id = $video->video_id;
            $fr->is_original = 0;
            $fr->start_time = $st;
            $fr->end_time = $et;
            $fr->type = 'mp4';
            $fr->width = $mp4_width;
            $fr->height = $mp4_height;
            $fr->src = '';
            $fr->status = 'WAITING';
            $fr->save();
        }

        return $fr;
    }

    private static function isFragResourceExist(Video $video, $st, $et)
    {
        $fr = FragmentResource::whereRaw('video_id = ? AND ROUND(start_time,3) = ? AND ROUND(end_time,3) = ?', array($video->video_id, $st, $et))->first();
        return $fr;
    }

}