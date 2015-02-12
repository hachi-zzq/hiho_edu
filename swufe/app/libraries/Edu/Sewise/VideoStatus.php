<?php namespace HiHo\Edu\Sewise;

class VideoStatus
{
    // TODO: 写入 API 地址等到 Config

    /**
     * @var
     */
    private $api = '';

    /**
     * @var array
     */
    private $arrSourceid = array();

    /**
     * @var string
     */
    private $strSourceid = '';

    /**
     * @param array $arrSourceId
     */
    public function __construct($arrSourceId = array())
    {
        //临时方案，解决暂时服务器ping不同本机问题
        if($_SERVER["SERVER_ADDR"] == '10.200.255.200'){
            $this->api = 'http://127.0.0.1/service/api/?do=index&op=getstatus';
        }else{
            $this->api = 'http://171.221.3.200/service/api/?do=index&op=getstatus';
        }
        //$this->api = \Config::get('hiho.sewise.video_status_api');
        if ($arrSourceId) {
            $this->arrSourceid = $arrSourceId;
            $this->sourceIdFormat();
        }
    }

    /**
     * #arrsource格式化
     */
    private function sourceIdFormat()
    {
        if (count($this->arrSourceid) == 1) {
            $arrSourceid = $this->arrSourceid;
            $this->strSourceid = array_pop($arrSourceid);
        } elseif (count($this->arrSourceid) > 1) {
            $this->strSourceid = implode(',', $this->arrSourceid);
        }
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        $api = $this->api . '&sourceid=' . $this->strSourceid;
        $ret = \Tool::getCurl($api, TRUE);
//        var_dump($ret);
//        exit;
        if ($ret['httpCode'] == 200) {
            return $ret['content'];
        } else {
            return \App::abort(403, 'error');
        }
    }


}