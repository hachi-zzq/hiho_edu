<?php namespace HiHo\Subtitle;

use HiHo\Model\Subtitle;
use HiHo\Sewise\Player;

/**
 * 字幕切割者
 * @package HiHo\Subtitle
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Shear
{
    private $video;

    private $fullSubtitleJson;

    private $fullSubtitle;

    private $fragmentSubtitle;

    private $fragmentSubtitleJson;

    private $adjustKeyFramesTime = TRUE;

    private $clipFragment = TRUE;

    /**
     * 加载视频
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $video
     */
    public function loadVideo($video)
    {
        $this->video = $video;
        return;
    }

    /**
     * 截取
     * 分为只调整时间，和调整时间并截取的两种情况。
     * 感谢 Hualong Bao 之前写的一版算法，现在的逻辑是在之前的基础上改造的。
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $startTime
     * @param $endTime
     */
    public function clip($startTime, $endTime)
    {
        $this->player = new Player;
        $this->player->loadVideo($this->video);
        $this->player->clip($startTime, $endTime);

        /**
         * 我们需要字幕设时间偏移，相对于新的时间。
         * 需要计算出 视频第一个关键帧 - 碎片第一个关键帧的时间差
         */
        $offset = $this->player->getVSt() - $this->player->getFSt();

        $this->fragmentSubtitle = array();

        // 根据 ST 和 ET 偏移逐行、逐字时间
        foreach ($this->fullSubtitle as $lk => $line) {

            $newLine = array();
            foreach ($line as $wk => $wd) {
                $newWd = $wd;
                $newWd->st = round($wd->st + $offset, 3);
                $newWd->et = round($wd->et + $offset, 3);
                $newLine[] = $newWd;
            }
            $this->fragmentSubtitle[] = $newLine;
        }

        // 按句裁剪?
        if ($this->clipFragment) {
            // 剪辑片段
            foreach ($this->fragmentSubtitle as $lk => &$line) {
                // 如果截取则去掉负数 和 超限部分
                foreach ($line as $wk => $wd) {
                    if ($wk == 0) {
                        $firstWd = $wk; // 本行开始单词
                        $endWd = count($line) - 1; // 本行结束单词
                    }
                }

                if ($line[$endWd]->et < 0) {
                    // 删除行尾时间 < 0
                    unset($this->fragmentSubtitle[$lk]);
                }
                if ($line[$firstWd]->st > ($endTime - $startTime)) {
                    // 删除行首时间 > 碎片长度
                    unset($this->fragmentSubtitle[$lk]);
                }
            }
        }

        return;
    }

    /**
     * 输出
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $type
     */
    public function output($type)
    {
        $type = strtolower($type);
        $this->fragmentSubtitle = array(
            'srt' => array_values($this->fragmentSubtitle),
            'time' => array(
                'fSt' => $this->player->getFSt(),
                'fEt' => $this->player->getFEt(),
                'vSt' => $this->player->getVSt(),
                'vEt' => $this->player->getVEt(),
                'adjusted' => $this->adjustKeyFramesTime),
            'keyFrames' => $this->player->getKeyFrames(),
            'success' => true,

        );
        $this->fragmentSubtitleJson = json_encode($this->fragmentSubtitle);

        if ($type == 'srt') {
            return Subtitle::generateSrt($this->fragmentSubtitleJson);
        } elseif ($type == 'json') {
            return $this->fragmentSubtitleJson;
        } else {
            return;
        }
    }

    /**
     * 加载并转换字幕为 Array
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $content
     * @param string $type
     */
    public function loadSubtitle(SubtitleInterface $subtitle)
    {
        $this->fullSubtitleJson = $subtitle->getContentJson();

        if (!$this->video) {
            $this->loadVideo($subtitle->video);
        }
        try {
            $this->fullSubtitle = json_decode($this->fullSubtitleJson)->srt;
        } catch (Exception $e) {
            return;
        }
        return;
    }
} 