<?php namespace HiHo\Edu\Sewise;

// TODO: 写入 API 地址等到 Config

class VideoStorage
{

    /**
     * api地址
     */
    private $api = '';

    /**
     * @var array
     * #要push的数据
     */
    private $data = '';

    /**
     * @var string
     * 视频标题
     * */
    private $title = '';

    /**
     * @var string
     * #视频关键字，多个用”，“分割
     */
    private $keyword = '';

    /**
     * @var string
     * #视频时长
     */
    private $duration = '';

    /**
     * @var string
     * #视频的唯一标示（判重）
     */
    private $fid = '';

    /**
     * @var string
     * #视频字幕实际内容
     */
    private $caption = '';


    /**
     * @var string
     * #主演
     */
    private $recorder = '';

    /**
     * @var string
     * #字幕语言
     */
    private $captionlanguage = 'zh_cn';

    /**
     * @var string
     * #视频封面地址
     */
    private $pic = '';

    /**
     * @var string
     * #视频作者
     */
    private $author = '';

    /**
     * @var string
     * #视频描述
     */
    private $description = '';

    /**
     * @var int
     * #是否字段匹配
     */
    private $autoaudit = 0;

    /**
     * @var int
     * #视频来源 1：录制；2：ted采集；3：上传；4：华为云；5：自动录制；6：cntv
     */
    private $source = 3;

    /**
     * @var int
     * #视频；类型 1：标清；2：高清；3：超清
     */
    private $type = 1;

    /**
     * @var string
     * #视频地址
     * 如果是 UNIX绝对地址，格式为：local:// + UNIX绝对地址。例如：local:///data/test.m4v
     */
    private $url = '';

    /**
     * @var string
     * #视频语言
     */
    private $audiolanguage = '';
    /**
     * ------------
     * contruct
     * ------------
     */

    /**
     * @var string
     * #电视频道
     */
    private $channel = '';

    /**
     * @var string
     * #播放开始时间
     */
    private $playtime = '';

    /**
     * @param array $config
     *
     */
    public function __construct($config = array())
    {
        //临时方案，解决暂时服务器ping不同本机问题
        if($_SERVER["SERVER_ADDR"] == '10.200.255.200'){
            $this->api = 'http://127.0.0.1/service/api/?do=videostorage';
        }else{
            $this->api = 'http://171.221.3.200/service/api/?do=videostorage';
        }
        //$this->api = \Config::get('hiho.sewise.storage_api');
        if ($config) {
            foreach ($config as $k => $v) {
                $this->$k = $v;
            }
        }
    }


    /**
     * -----------------------
     * @param array $config
     * @return bool
     * ---------------------
     * set config
     */
    public function setConf($config = array())
    {
        if ($config) {
            foreach ($config as $k => $v) {
                $this->$k = $v;
            }
        }
        return true;
    }

    /**
     * -------------------
     * @param null $key
     * @return string
     * -------------------
     * get config
     */
    public function getConf($key = NULL)
    {
        if ($key) {
            return $this->$key;
        } else {
            return '';
        }
    }


    /**
     * ------------------
     * @return mixed
     * -----------------
     * push data
     */
    public function push()
    {
        $data = array(
            'base' => array(
//                'fid'=>$this->fid,
                'title' => $this->title,
                'keyword' => $this->keyword,
                'pic' => $this->pic,
                'author' => $this->author,
                'recorder' => $this->recorder,
                'description' => $this->description,
                'duration' => $this->duration,
                'source' => $this->source,
                'caption' => $this->caption,
                'captionlanguage' => $this->captionlanguage,
                'audiolanguage' => $this->audiolanguage,
                'autoaudit' => $this->autoaudit,
            ),
            'files' => array(
                array(
                    'type' => $this->type,
                    'url' => $this->url
                )
            ),
            'tvs' => array(
                array(
                    'channel' => $this->channel,
                    'playtime' => $this->playtime
                )
            )
        );
        $this->data = json_encode($data);
        $ret = \Tool::getCurl($this->api, TRUE, array('data' => $this->data));
        if ($ret['httpCode'] == 200) {
            $content = json_decode($ret['content'], TRUE);
            ##push error
            if (isset($content['errors'])) {
                echo empty($content['errors']) ? 'push error' : $content['errors'];
                exit;
            }
            if ($content['sourceid'] == '' || $content['success'] != TRUE) {
                \App::abort(403, 'push error');
            }
            return $ret['content'];
        } else {
            \App::abort(500, 'http code error');
        }
    }

}
