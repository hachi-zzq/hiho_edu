<?php namespace HiHo\Tools;

/**
 * 短 Url 加解密类
 * @package HiHo
 * @author Zhengqian<zhengqian.zhu@autotiming.com>
 */
class ShortUri
{

    /**
     * 加密
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $dec
     * @return string
     */
    public static function dec2short($dec)
    {
        return base_convert($dec, 10, 36) . substr((string)$dec, 0, 1);
    }

    /**
     * 解密
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $string
     * @return int
     */
    public static function short2dec($string)
    {
        $string = substr($string, 0, strlen($string) - 1);
        return intval(base_convert($string, 36, 10));
    }

}
