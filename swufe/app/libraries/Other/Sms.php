<?php namespace HiHo\Other;

/**
 * 短信功能类
 * @author Hanxiang<hanxiang.qiu@autotiming.com>
 */
class Sms{

    public static function sendSMS($mobile, $content) {

        //calculate post data
        $uid = \Config::get('sms.uid');
        $pwd = \Config::get('sms.pwd');
        $data = array(
            'uid' => $uid,
            'pwd' => md5($pwd . $uid),
            'mobile' => $mobile,
            'content'=>$content,
        );
        $post = "";
        while (list($k, $v) = each($data)){
            $post .= rawurlencode($k) . "=" . rawurlencode($v) . "&";	//转URL标准码
        }
        $post = substr( $post , 0 , -1 );
        $len = strlen($post);

        //parse api url
        $url = \Config::get('sms.url');
        $row = parse_url($url);
        $host = $row['host'];
        $port = 80;
        $file = $row['path'];

        //send post request
        $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
        if (!$fp) {
//            return "$errstr ($errno)\n";
            return false;
        } else {
            $receive = '';
            $out = "POST $file HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n\r\n";
            $out .= $post;
            fwrite($fp, $out);
            while (!feof($fp)) {
                $receive .= fgets($fp, 128);
            }
            fclose($fp);
            $receive = explode("\r\n\r\n", $receive);
            unset($receive[0]);
            return implode("",$receive);
        }
    }
}