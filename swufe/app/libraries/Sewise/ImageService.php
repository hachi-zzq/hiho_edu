<?php

namespace HiHo\Sewise;

/**
 * 远程图片服务
 * Class ImageService
 * @package HiHo\Sewise
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class ImageService
{

    /**
     * 根据图片资源地址获得图片服务 URL
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $resource_url
     * @return string
     */
    public static function getImageApiDomain($resource_url)
    {
        $http_info = parse_url($resource_url);

        // TODO: 已根据本台服务器修改... 未加入 CONFIG...
        //临时方案，解决暂时服务器ping不同本机问题
        if($_SERVER["SERVER_ADDR"] == '10.200.255.200'){
            $service_api_domain = 'http://127.0.0.1';
        }else{
            $service_api_domain = 'http://171.221.3.200';
        }

        return $service_api_domain;
    }

    /**
     * 使用 TaskID、Time 拼接视频碎片图片 URL
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $service_api_domain
     * @param $task_id
     * @param $time
     * @return string
     */
    public static function getImageUrlWithTaskIdAndTime($service_api_domain, $task_id, $time)
    {
        $sewise_api_url = sprintf("%s/service/api/?do=index&op=getscreenshot&taskid=%s&time=%s",
            $service_api_domain, $task_id, $time);

        // TODO: 加载失败
        $api_result = json_decode(file_get_contents($sewise_api_url));
        $sewise_image_url = $api_result->url;
        return $sewise_image_url;
    }
} 