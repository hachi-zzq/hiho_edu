<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use J20\Uuid\Uuid;
use HiHo\Sewise\ImageService;

/**
 * 碎片 MODEL
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Fragment extends \Eloquent
{
    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    protected $primaryKey = 'id';

    protected $table = 'fragments';

    /**
     * 绑定加载事件
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public static function boot()
    {
        parent::boot();

        // 创建 PlayID
        self::created(function ($fragment) {
            $playid = PlayID::createWithEntity($fragment);
        });

        // 删除 PlayID
        self::deleted(function ($fragment) {
            PlayID::dropWithEntity($fragment);
        });
    }

    /**
     * @return mixed
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function video()
    {
        return $this->belongsTo('Video');
    }

    /**
     * @return mixed
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * 获得 PlayId 字符串
     * @return bool|null
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getPlayIdStr()
    {
        return PlayID::createWithEntity($this)->play_id;
    }

    /**
     * 用 ST 和 ET 获得一个新碎片
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $video
     * @param $st
     * @param $et
     * @param $user
     * @return Fragment
     */
    public static function getFragmentByStAndEt($video, $st, $et, $user = NULL)
    {
        $user_id = $user ? $user->user_id : NULL;
        $fragment = Fragment::where('video_id', '=', $video->video_id)
            ->where('start_time', '=', round($st, 3))
            ->where('end_time', '=', round($et, 3))
            ->where('user_id', '=', $user_id)
            ->first();

        if (empty($fragment)) {

            $fragment = new self;
            $fragment->guid = Uuid::v4();
            $fragment->cover = self::getCoverV2($video, $st, $et);
            $fragment->video_id = $video->video_id;
            $fragment->start_time = $st;
            $fragment->end_time = $et;
            $fragment->user_id = $user_id;
            $fragment->save();

        }

        return $fragment;
    }

    /**
     * 获取封面图
     * @author Guanjun
     * @auth Luyu.Zhang 重构中...
     */
    static public function getCoverV2($oid, $st, $et)
    {
        if (!isset($oid) || !isset($st)) {
            return false;
        }

        $vdn_server = VideoResource::where('video_id', $oid->video_id)->where('type', 'flv')->first();
        $resource_url = $vdn_server->src;

        $service_api_domain = ImageService::getImageApiDomain($resource_url);
        $sewise_image_url = ImageService::getImageUrlWithTaskIdAndTime($service_api_domain, $oid->origin_id, $st);

        if (!$sewise_image_url) {
            return action('VideoImageController@getNotFound');
        }

        $img = self::getImage($sewise_image_url);
        @mkdir("screenshot/" . date("Y-m-d", time()) . "/", 0777, true);
        $screenshot = "screenshot/" . date("Y-m-d", time()) . "/" . Uuid::v4(false) . ".jpeg";
        $fp2 = @fopen($screenshot, 'w');
        fwrite($fp2, $img);
        fclose($fp2);

        return asset($screenshot);
    }

    /*
     * 功能：php完美实现下载远程图片保存到本地
     * 参数：文件url,保存文件目录,保存文件名称，使用的下载方式
     * 当保存文件名称为空时则使用远程文件原来的名称
     */
    static public function getImage($url)
    {
        //暂时解决方案，服务器问题，类似后台push getstatus
        if ($_SERVER["SERVER_ADDR"] == '10.200.255.200') {
            $url = str_replace('171.221.3.200', '127.0.0.1', $url);
        }

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $img = curl_exec($ch);
        curl_close($ch);
        return $img;
    }

    /**
     *重写delete方法
     * @return mixed
     */
    public function delete()
    {
        //comment
        \Comment::where('fragment_id', $this->id)->delete();
        //favorite
        \Favorite::where('play_id', $this->getPlayIdStr())->delete();
        //fragmentShare
        \FragmentShare::where('fragment_id', $this->id)->delete();
        //fragmetn tag
        \FragmentTag::where('fragment_id', $this->id)->delete();
        //playlist fragmnet
        \PlaylistFragment::where('fragment_id', $this->id)->delete();

        return parent::delete();
    }

}