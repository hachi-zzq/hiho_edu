<?php
namespace HiHo\Other;

use \Advertisement;
use \AdPosition;
/**
 * #广告前台调用类库
 * DateTime: 14-8-25 上午11:44
 * author zhengqian.zhu <zhengqian.zhu@autotiming.com>
 */

class AdvertisementCall {

    /**
     *前台渲染
     * @param $ad_id
     */
    public static  function render($adPosition_id,$only_url=0){
        ! $adPosition_id and \App::abort(403);
        $objAdPosition = AdPosition::where('status',1)->find($adPosition_id);
        if( ! $objAdPosition){
            return '';
        }
        return self::handerType($objAdPosition->type,$adPosition_id,$only_url);
    }

    /**
     *更加不同的广告类型，不同的渲染
     * @param $type
     */
    public static function handerType($type,$adPosition_id,$only_url){
        switch ($type) {
            case 'picture':
                $dom = self::picture($adPosition_id,$only_url);
                break;
            case 'rotation':
                $dom = self::rotation($adPosition_id);
                break;
            case 'text':
                $dom = self::text($adPosition_id);
                break;
            case 'code':
                $dom = self::code($adPosition_id);
                break;
            default:
                $dom = '';
        };
        return $dom;
    }

    public static function picture($adPosition_id,$only_url){
        $objAd = Advertisement::where('position_id',$adPosition_id)->where('status',1)->first();
        if($only_url && $objAd){
            return $objAd->img_src;
        }elseif(!$objAd){
            return '';
        }
        $dom = sprintf('<a href="%s" target="_blank"><img src="%s" alt="%s"/></a>',$objAd->href,$objAd->img_src,$objAd->name);
        return $dom;
    }

    public static function rotation($adPosition_id){
        $objAd = Advertisement::where('position_id',$adPosition_id)->where('status',1)->get();
        $dom = '';
        if($objAd){
            foreach($objAd as $ad){
                $dom .= sprintf('<li><a href="%s" target="_blank"><img src="%s" alt="%s" /></a></li>',$ad->href,$ad->img_src,$ad->name);
            }
            return '<div class="focus" id="focus001"><ul>'.$dom.'</ul></div>';
        }else{
            return '';
        }


    }

    public static function text($adPosition_id){
        $objAd = Advertisement::where('position_id',$adPosition_id)->where('status',1)->get();
        $dom = '';
        if($objAd){
            foreach($objAd as $ad){
                $dom .= sprintf('<a href="%s" target="_blank">%s</a>',$ad->href,$ad->text_name);
            }
            return $dom;
        }else{
            return '';
        }

    }

    public static function code($adPosition_id){
        $objAd = Advertisement::where('position_id',$adPosition_id)->where('status',1)->first();
        if(!$objAd){
            return '';
        }
        return $objAd->code;
    }

}