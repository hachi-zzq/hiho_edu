<?php namespace HiHo\Sewise;

use Guzzle\Http\Client;
use HiHo\Model\FragmentResource;

/**
 * Class Player
 * @package HiHo\Sewise
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Player
{
    private $video;

    private $keyFrames;

    private $adjustKeyFramesTime = TRUE;

    private $resource;

    private $mp4Resources;

    /**
     * Video 的开始时间(不修正关键帧)
     * @var floot
     */
    private $vStOrigin;

    /**
     * Video 的结束时间(不修正关键帧)
     * @var floot
     */
    private $vEtOrigin;

    /**
     * Video 的开始时间(修正关键帧)
     * @var floot
     */
    private $vSt;

    /**
     * Video 的结束时间(修正关键帧)
     * @var floot
     */
    private $vEt;

    /**
     * 碎片的开始时间, 即播放地址的最终时间
     * @var floot
     */
    private $fSt;

    /**
     * 碎片的结束时间, 即播放地址的最终时间
     * @var floot
     */
    private $fEt;


    /**
     * 加载视频
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $video
     * @param $is_use_mp4 是否截取mp4
     */
    public function loadVideo($video)
    {
        $this->video = $video;
        return;
    }

    /**
     * 裁剪修正视频
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $startTime
     * @param $endTime
     */
    public function clip($startTime = 0, $endTime = 0)
    {
        /**
         * 1. 解析带有 START TIME 和 END TIME 的 URL，提取 START TIME 和 END TIME
         * 2. START TIME = 0 的视频, START TIME = 0 + 第一个关键帧，END TIME 不变
         * 3. START TIME !=0 的视频，START TIME = 原 ST 之前最接近的关键帧，END TIME = 原 ET 之后最接近的关键帧
         * 4. 返回新的 START TIME、END TIME
         * 5. 如果需要切割（ST & ET），最终播放地址 =
         *     新 START TIME + 切割 ST，然后找最近关键帧
         *     新 START TIME + 切割 ET，然后找最近关键帧
         *
         * VST & FST
         */

        $this->loadResource();

        if ($this->adjustKeyFramesTime) {
            $this->loadKeyFrames($this->video);
        }

        if ($this->adjustKeyFramesTime) {
            $this->getStAndEtInKeyFrames();
        }

        // 没有关键帧
        if (!($this->keyFrames)) {
            $this->fSt = $this->vStOrigin + $startTime;
            if ($endTime <= 0) {
                $this->fEt = $this->vEtOrigin;
            } else {
                $this->fEt = $this->vStOrigin + $endTime;
            }

            $this->mp4Resources = Mp4::getMp4Resource($this->video, $this->fSt, $this->fEt);
            $this->updateResource();
            $this->groupResource();
            return;
        }

        // 找出小于 fST 的数中最大的
        $stArr = array();
        foreach ($this->keyFrames as $f) {
            // TODO: 再次检查算法 $this->vStOrigin != 0 and
            if (($f <= $this->vSt + $startTime) or $f <= $startTime) {
                $stArr[] = $f;
            }
        }
        $this->fSt = (float)max($stArr);

        // 找出大于 fET 的数中最小的
        if ($endTime >= $f) {
            $this->fEt = end($this->keyFrames);
        } else {
            $etArr = array();
            foreach ($this->keyFrames as $f) {
                if (($f >= $this->vSt + $endTime) or $f >= $endTime) {
                    $etArr[] = $f;
                }
            }
            $this->fEt = (float)min($etArr);
        }

        if ($endTime <= 0) {
            $this->fEt = (float)$this->vEtOrigin;
        }

        $this->mp4Resources = Mp4::getMp4Resource($this->video, $this->fSt, $this->fEt);
        $this->updateResource();
        $this->groupResource();
        return;
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return mixed
     */
    public function getFSt()
    {
        return $this->fSt;
    }

    /**
     * @return mixed
     */
    public function getFEt()
    {
        return $this->fEt;
    }

    /**
     * @return \HiHo\Sewise\floot
     */
    public function getVEt()
    {
        return $this->vEt;
    }

    /**
     * @return \HiHo\Sewise\floot
     */
    public function getVSt()
    {
        return $this->vSt;
    }

    /**
     * @return boolean
     */
    public function getAdjustKeyFramesTime()
    {
        return $this->adjustKeyFramesTime;
    }

    /**
     * @return mixed
     */
    public function getKeyFrames()
    {
        return $this->keyFrames;
    }


    /**
     * 加载原始播放地址
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    private function loadResource()
    {
        $this->resource = $this->video->resource()->get()->toArray();

        foreach ($this->resource as $r) {
            if ($r['type'] == 'm3u8') {
                $this->setStAndEtInSrc($r['src']);
            }
        }
    }

    /**
     * 载入视频关键帧
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $video
     */
    private function loadKeyFrames($video)
    {
        // 有关键帧吗？
        if (!unserialize($video->keyframes) or !isset($video->keyframes[0])) {
            $this->adjustKeyFramesTime = FALSE;
            return;
        }
        $this->keyFrames = unserialize($video->keyframes);
        return;
    }

    /**
     * 获得关键帧中的 ST 和 ET
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    private function getStAndEtInKeyFrames()
    {
        // TODO: Undefined property: HiHo\Sewise\Player::$keyFrames
        $this->vSt = 0 + $this->keyFrames[0];
        $this->vEt = 0 + end($this->keyFrames);

        return;
    }

    /**
     * 循环更新 URL
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    private function updateResource()
    {
        foreach ($this->resource as &$r) {
            $s1 = preg_replace(
                '/starttime=\-{0,1}[0-9]{0,}\.{0,1}[0-9]{0,}/',
                'starttime=' . $this->fSt,
                $r['src']
            );
            $s2 = preg_replace(
                '/endtime=\-{0,1}[0-9]{0,}\.{0,1}[0-9]{0,}/',
                'endtime=' . $this->fEt,
                $s1);

            $r['src'] = $s2;
        }
        return;
    }

    /**
     * 获得播放地址内的 ST 和 ET
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $url
     */
    private function setStAndEtInSrc($url)
    {
        if (isset(explode("?", $url)[1])) {
            preg_match('/starttime=\-{0,1}[0-9]{0,}\.{0,1}[0-9]{0,}/', $url, $st);
            preg_match('/endtime=\-{0,1}[0-9]{0,}\.{0,1}[0-9]{0,}/', $url, $et);
            $this->vStOrigin = (float)explode('=', $st['0'])[1];
            $this->vEtOrigin = (float)explode('=', $et['0'])[1];
            $this->vSt = (float)explode('=', $st['0'])[1];
            $this->vEt = (float)explode('=', $et['0'])[1];
        } else {
            $this->vSt = 0;
            $this->vEt = 0;
        }
        return;
    }

    /**
     * 将字幕按清晰度、按类型分组
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return array
     */
    private function groupResource()
    {
        $resource = $this->resource;
        $newResource = array();

        /**
         * 清晰度分为 SD、HD 和 FULL HD，根据像素高度 720 和 1080 为临界点判断
         */
        foreach ($resource as $r) {
            if ($r['height'] >= 1080) {
                $newResource['FULLHD'][strtoupper($r['type'])] = $r;
            } else if ($r['height'] >= 720 and $r['height'] < 1080) {
                $newResource['HD'][strtoupper($r['type'])] = $r;
            } else {
                $newResource['SD'][strtoupper($r['type'])] = $r;
            }
        }

        /**
         * 加入 MP4 文件
         */
        if ($this->mp4Resources) {
            $r = $this->mp4Resources->toArray();
            if ($r['height'] >= 1080) {
                $newResource['FULLHD'][strtoupper($r['type'])] = $r;
            } else if ($r['height'] >= 720 and $r['height'] < 1080) {
                $newResource['HD'][strtoupper($r['type'])] = $r;
            } else {
                $newResource['SD'][strtoupper($r['type'])] = $r;
            }
        }

        $this->resource = $newResource;
        return;
    }
}