<?php

use HiHo\Model\Fragment;

class VideoImageController extends BaseController{

	/**
     * 通过虚拟 URL 获得视频图片
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $video_guid
     * @param string $norms
     */
    public function getVideoImage($video_guid, $norms = 'THUMBNAIL')
    {
        // 获得视频对象
        $video = Video::with('pictures')->where('guid', '=', $video_guid)->first();
        if (!$video) {
            // 若无视频跳转到默认图
            return Redirect::action('VideoImageController@getNotFound', NULL, 301);
        }

        // 查找 DB 中的图片路径
        $vp_src = '';
        if ($video->pictures and $video->pictures->count() >= 1) {
            foreach ($video->pictures as $vp) {
                if ($vp instanceof VideoPicture) {
                    $vp_src = $vp->src;
                    break;
                }
            }
        }

        if (!$vp_src) {
            // 若无图片跳转到默认图
            return Redirect::action('VideoImageController@getNotFound', NULL, 301);
        }

        //临时方案
        if(strpos($vp_src,'171.221.3.200') != false && $_SERVER['REMOTE_ADDR'] != '127.0.0.1'){
            header('Content-Type: image/jpeg');
            header('HiHo-Image-Original-Url: ' . $vp_src);
            $new = substr($vp_src,24);
            readfile('/data/www/'.$new);
            exit;
        }

        // TODO: 检查路径扩展名
        // TODO: 图片加载失败

        // 如果是远程图片
        // 如果是 HiHo 主站图片
        // (尝试保存到本地)
        // 如果是本地图片

        // ReadFile
        header('Content-Type: image/jpeg');
        header('HiHo-Image-Original-Url: ' . $vp_src);
        readfile($vp_src);
        return;
    }

    public function getFragmentImageWithVideoGuid($video_guid, $st, $et, $norms = 'THUMBNAIL') {

        if(!is_numeric($st) or !is_numeric($et)){
            return Redirect::action('VideoImageController@getNotFound', NULL, 301);
        }
        // 获得视频对象
        $video = Video::with('pictures')->where('guid', '=', $video_guid)->first();
        if (!$video or $et < $st or $st < 0 or $et > $video->length) {
            // 若无视频跳转到默认图
            return Redirect::action('VideoImageController@getNotFound', NULL, 301);
        }


        // 获得碎片对象
        $fragment = Fragment::getFragmentByStAndEt($video, $st, $et);

        // DB 内没有图片路径
        $vi_src = $fragment->cover;

        if (!$vi_src) {
            // 若无图片使用完整视频的碎片
            return Redirect::action('VideoImageController@getVideoImage', array($video_guid, $norms), 301);
        }

        // 如果是远程图片
        // 如果是 HiHo 主站图片
        // (尝试保存到本地)


        // 如果是本地图片
        $config_vi_host = parse_url(Config::get('hiho.videoimage_root_url'))['host'];
        $vi_src_parse = parse_url($vi_src);
        if ($vi_src_parse['host'] == $config_vi_host) {
            $vi_localpath = public_path($vi_src_parse['path']);
        }

        // ReadFile
        header('Content-Type: image/jpeg');
        header('HiHo-Image-Original-Url: ' . $vi_src);

        if (isset($vi_localpath)) {
            readfile($vi_localpath);
        } else {
            readfile($vi_src);
        }
        return;
    }

    /**
     * 没有图片
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param string $norms
     */
    public function getNotFound($norms = 'THUMBNAIL')
    {
        $vp_src = asset('static/img/video_default.png');

        header('Content-Type: image/jpeg');
        header('HiHo-Image-Original-Url: ' . $vp_src);
        readfile($vp_src);
        return;
    }



}