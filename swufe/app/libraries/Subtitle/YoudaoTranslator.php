<?php namespace HiHo\Subtitle;

/**
 * User: luyu
 * Date: 13-11-14
 * Time: 上午9:48
 */

class YoudaoTranslator implements TranslatorInterface
{
    public		$err_code		= 0;
    public		$err_message	= '';

    public function translate()
    {

    }

    public function mergeResult($result)
    {

    }

    public function clipOriginal($original)
    {

    }

    /**
     * 联合有道和百度翻译
     * @author hualong
     * @param $str
     * @param string $from
     * @param string $to
     * @return string
     */
    public function mergeTranslate($str,$from='',$to='') {
        if(!$from or !$to) {
            $result = $this->youdaoLanguage($str);
            if($this->err_code==0) {
                return $result;
            }else {
                return $this->err_message;
            }
        }
        if(($from == 'zh' and $to == 'en') or ($from == 'en' and $to == 'zh')) {
            $result = $this->youdaoLanguage($str);
            if($this->err_code==0) {
                return $result;
            }else {
                $result = $this->baiduLanguage($str,$from,$to);
            }
        }else {
            $result = $this->baiduLanguage($str,$from,$to);
        }

        if($this->err_code == 0) {
            return $result;
        }else {
            return $this->err_message;
        }
    }

    /**
     * 有道翻译 中<->英 max 200字符
     * @author hualong
     * @param $value
     * @return string
     */
    public function youdaoLanguage($value){
        if(mb_strlen($value)>200){
            return $this->error( 20 , 'query string is too long , please be less than 200)' );
        }
        else{
            $query = $value;
        }
        $keyfrom = "AutoTiming"; //申请APIKEY时，填表的网站名称的内容  ；注意： $keyFrom 需要是【连续的英文、数字的组合】
        $apikey = "1629116153";  //从有道申请的APIKEY
        $qurl = 'http://fanyi.youdao.com/fanyiapi.do?keyfrom='.$keyfrom.'&key='.$apikey.'&type=data&doctype=json&version=1.1&q='.$query;
        $content = @file_get_contents($qurl);
        $sina = json_decode($content,true);
        $errorcode = $sina['errorCode'];
        $trans = '';
        if(isset($errorcode)){
            switch ($errorcode){
                case 0:
                    $trans = $sina['translation']['0'];
                    break;
                case 20:
                    $trans = $this->error( 20 , '要翻译的文本过长');
                    break;
                case 30:
                    $trans = $this->error( 30 , '无法进行有效的翻译');
                    break;
                case 40:
                    $trans = $this->error( 40 , '不支持的语言类型');
                    break;
                case 50:
                    $trans = $this->error( 50 , '无效的key');
                    break;
                default:
                    $trans = $this->error( 500 , '出现异常');
                    break;
            }
        }
        return $trans;
    }

    /**
     * 百度翻译 支持多国语言翻译 get max 2k,post max 5k
     * @author hualong
     * @param $value
     * @param string $from
     * @param string $to
     * @return mixed
     */
    public function baiduLanguage($value,$from="zh",$to="en")
    {
        $value_code=urlencode($value);
        #首先对要翻译的文字进行 urlencode 处理
        $appid="MAYQC31TjNrgux2QWuNvG4IK";
        #您注册的API Key
        $languageurl = "http://openapi.baidu.com/public/2.0/bmt/translate?client_id=" . $appid ."&q=" .$value_code. "&from=".$from."&to=".$to;
        #生成翻译API的URL GET地址
        $text=json_decode($this->language_text($languageurl));
        $text = $text->trans_result;
        return $text[0]->dst;
    }

    /**
     * 获取目标URL所打印的内容
     * @author hualong
     * @param $url
     * @return mixed|string
     */
    public function language_text($url)
    {
        if(!function_exists('file_get_contents')) {
            $file_contents = file_get_contents($url);
        } else {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }
        return $file_contents;
    }

    protected function error( $code , $message ){
        $this->err_code = $code;
        $this->err_message = $message;
        return false;
    }

}