<?php namespace HiHo\Other;


/*
 |----------------------------
 |tool class
 |---------------------------
 |this is a tool class
 |
 */

class Tool
{

    /**
     *字节格式化
     * @param $bytes
     * @return string
     * @author zhuzhengqian
     */
    public static function  formatBytes($bytes)
    {
        if ($bytes < 1024) return $bytes . ' B';
        elseif ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
        elseif ($bytes < 1073741824) return round($bytes / 1048576, 2) . ' MB';
        elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2) . ' GB';
        else return round($bytes / 1099511627776, 2) . ' TB';
    }


    /**
     * --------------------------
     * @param $url
     * @param bool $isPost
     * @param array $data
     * @return array
     * --------------------------
     * curl#post
     */
    public static function  getCurl($url, $isPost = FALSE, $data = array())
    {

        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //定义超时60秒钟
        if ($isPost) {
            // POST数据
            curl_setopt($ch, CURLOPT_POST, 1);
            // 把post的变量加上
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        //执行并获取url地址的内容
        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //释放curl句柄
        curl_close($ch);
        return array(
            'httpCode' => $httpCode,
            'content' => $output
        );
    }

    /**
     *媒资视频状态匹配
     * @var array
     * -2：不可用；2：准备就绪；0：等待分离；3：分离中；-3：分离成功，字幕音频不匹配；4：分离成功；-4：分离失败；5：转码中；-5：转码失败；6：匹配中；7：匹配完成；-7匹配失败；8：mp4转码中；-8：mp4转码失败
     * @author zhuzhengqian
     */
    public static function returnStatus($statusCode)
    {

        $status = array(
            'NORMAL' => '等待上传字幕',
            '-2' => '不可用',
            '2' => '准备就绪',
            '0' => '等待分离',
            '3' => '分离中',
            '-3' => '分离成功,字幕音频不匹配',
            '4' => '分离成功',
            '-4' => '分离失败',
            '5' => '转码中',
            '-5' => '转码失败',
            '6' => '匹配中',
            '7' => '匹配完成',
            '-7' => '匹配失败',
            '8' => 'mp4转码中',
            '-8' => 'mp4转码失败',

        );
        return $status[$statusCode];
    }


    /**
     *时间格式化
     * @param $timeStamp
     * @return string
     * @author zhuzhengqian
     */
    public static function timeFormat($timeStamp)
    {
        switch (TRUE) {
            case(time() - $timeStamp < 60):
                $return = time() - $timeStamp . "秒前";
                break;
            case($timeStamp < time() - 3600 * 24 * 7 && $timeStamp > time() - 3600 * 24 * 30):
                $return = '一周以前';
                break;
            case($timeStamp < time() - 3600 * 24 * 30 && $timeStamp > time() - 3600 * 24 * 30 * 6):
                $return = '一个月以前';
                break;
            case($timeStamp < time() - 3600 * 24 * 30 * 6 && $timeStamp > time() - 3600 * 24 * 30 * 12):
                $return = '半年以前';
                break;
            case($timeStamp < time() - 3600 * 24 * 30 * 12):
                $return = '一年以前';
                break;
            default:
                $return = date('Y-m-d H:i:s', $timeStamp);
        }

        return $return;
    }

    /**
     * 支持utf8中文字符截取
     * @param    string $text 待处理字符串
     * @param    int $start 从第几位截断
     * @param    int $sublen 截断几个字符
     * @param    string $code 字符串编码
     * @param    string $ellipsis 附加省略字符
     * @return    string
     * @author zhuzhengqian
     */
    static function csubstr($string, $start = 0, $sublen = 12, $ellipsis = '', $code = 'UTF-8')
    {
        if ($code == 'UTF-8') {
            $tmpstr = '';
            $i = $start;
            $n = 0;
            $str_length = strlen($string); //字符串的字节数
            while (($n + 0.5 < $sublen) and ($i < $str_length)) {
                $temp_str = substr($string, $i, 1);
                $ascnum = Ord($temp_str); //得到字符串中第$i位字符的ascii码
                if ($ascnum >= 224) { //如果ASCII位高与224，
                    $tmpstr .= substr($string, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
                    $i = $i + 3; //实际Byte计为3
                    $n++; //字串长度计1
                } elseif ($ascnum >= 192) { //如果ASCII位高与192，
                    $tmpstr .= substr($string, $i, 3); //根据UTF-8编码规范，将2个连续的字符计为单个字符
                    $i = $i + 3; //实际Byte计为2
                    $n++; //字串长度计1
                } else { //其他情况下，包括小写字母和半角标点符号，
                    $tmpstr .= substr($string, $i, 1);
                    $i = $i + 1; //实际的Byte数计1个
                    $n = $n + 0.5; //小写字母和半角标点等与半个高位字符宽...
                }
            }
            if (strlen($tmpstr) < $str_length) {
                $tmpstr .= $ellipsis; //超过长度时在尾处加上省略号
            }
            return $tmpstr;
        } else {
            $strlen = strlen($string);
            if ($sublen != 0) $sublen = $sublen * 2;
            else $sublen = $strlen;
            $tmpstr = '';
            for ($i = 0; $i < $strlen; $i++) {
                if ($i >= $start && $i < ($start + $sublen)) {
                    if (ord(substr($string, $i, 1)) > 129) $tmpstr .= substr($string, $i, 2);
                    else $tmpstr .= substr($string, $i, 1);
                }
                if (ord(substr($string, $i, 1)) > 129) $i++;
            }
            if (strlen($tmpstr) < $strlen) $tmpstr .= $ellipsis;
            return $tmpstr;
        }
    }

    /**
     * 获取IP
     * @param null
     * @return    string
     * @author zhuzhengqian
     */
    static function funGetIP()
    {

        $ip = '';

        switch (true) {
            case isset($_SERVER["HTTP_X_FORWARDED_FOR"]):
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                break;
            case isset($_SERVER["HTTP_CLIENT_IP"]):
                $ip = $_SERVER["HTTP_CLIENT_IP"];
                break;
            default:
                $ip = $_SERVER["REMOTE_ADDR"] ? $_SERVER["REMOTE_ADDR"] : '127.0.0.1';
        }
        if (strpos($ip, ', ') > 0) {
            $ips = explode(', ', $ip);
            $ip = $ips[0];
        }

        return $ip;
    }

    /**
     * video date format: 2014-06-20 12:00:00 ==> 2014/06/20
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public static function dateFormat($dateStr)
    {
        return substr(str_replace('-', '/', $dateStr), 0, 10);
    }

    /**
     * 日期与当前时间的间隔
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public static function timeTransfer($the_time)
    {
        $now_time = time();
        $show_time = strtotime($the_time);
        $dur = $now_time - $show_time;
        if ($dur < 0) {
            return $the_time;
        }

        if ($dur < 60) {
            return $dur . '秒前';
        }

        if ($dur < 3600) {
            return floor($dur / 60) . '分钟前';
        }

        if ($dur < 86400) {
            return floor($dur / 3600) . '小时前';
        }

        if ($dur < 259200) { //3天内
            return floor($dur / 86400) . '天前';
        } else {
            return $the_time;
        }
    }

    /**
     * 获取字幕语言中文显示
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public static function getLanguageName($lang) {
        $ret = '';
        switch($lang) {
            case 'en':
                $ret = '英文';
                break;
            case 'zh_cn':
                $ret = '中文';
                break;
            default:
                $ret = '未知';
                break;
        }
        return $ret;
    }
}