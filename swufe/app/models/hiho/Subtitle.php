<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use HiHo\Subtitle\SubtitleInterface;

/**
 * 字幕 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Subtitle extends \Eloquent implements SubtitleInterface
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
    /**
     * 触发 SAVE 时的自动方法
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param array $options
     */
    public function save(array $options = array())
    {
        parent::save($options);

        if ($this->type == 'JSON') {
            // 如果已经有 FT 则更新
            $ft = SubtitleFt::where('subtitle_id', '=', $this->id)->first();
            if (!$ft) {
                // 如不存在则创建 FT
                $ft = new SubtitleFt();
            }

            // content = 纯文本,一行一句
            $json = $this->content;
            $ft->content = self::generateTxt($json);

            // timeline = 纯时间,一行一句
            $json = $this->content;
            $ft->timeline = self::generateTextTimeLine($json);

            // 其它字段来自本字幕
            $ft->video_id = $this->video_id;
            $ft->subtitle_id = $this->id;
            $ft->is_original = $this->is_original;
            $ft->language = $this->language;
            $ft->save();
            unset($ft);
        }

    }

    /**
     * 一对多关系
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return mixed
     */
    public function video()
    {
        return $this->belongsTo('Video');
    }

    /**
     * 获得该字幕的 Json 内容(包括是组的情况)
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return mixed
     */
    public function getContentJson()
    {
        if($this->type=='JSON'){
            return $this->content;
        }
        return "{}";
    }

    /**
     * 获得该字幕的语言
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * 根据 SW 的 JSON 生成 SRT 字幕
     * @author Hualong Bao<hualong.bao@autotiming.com>
     * @param $json_str
     * @return string
     */
    static public function generateSrt($json)
    {
        $data = json_decode($json);
        $lines = $data->srt;

        $out = '';
        foreach ($lines as $i => $line) {
            if (floatval($line[0]->st) > 0) {
                $st = self::formatTime(floatval($line[0]->st));
            } else {
                $st = self::formatTime(0);
            }
            $et = self::formatTime(floatval($line[count($line) - 1]->et));
            $p = '';
            $pre = null;

            foreach ($line as $word) {
                $w = $word->token;
                if ($pre !== null and preg_match("/^(\w|\d)/", $w)) {
                    if (!preg_match("/^(ve|s|ll)$/i", $w) or !preg_match("/^('|’)$/", $pre))
                        $p .= ' ';
                }
                $p .= $w;
                $pre = $w;
            }

            $out .= ($i + 1) . "\r\n{$st} --> {$et}\r\n{$p}\r\n\r\n";
        }
        return $out;
    }

    /**
     * 根据 SW 的 JSON 生成 TXT 字幕
     * @author Hualong Bao<hualong.bao@autotiming.com>
     * @param $json_str
     * @return string
     */
    static public function generateTxt($json)
    {
        $data = json_decode($json);
        $lines = $data->srt;
        $out = '';
        if (!is_array($lines)) {
            return '';
        }

        foreach ($lines as $i => $line) {
            $p = '';
            $pre = null;

            foreach ($line as $word) {
                $w = $word->token;
                if ($pre !== null and preg_match("/^(\w|\d)/", $w)) {
                    if (!preg_match("/^(ve|s|ll)$/i", $w) or !preg_match("/^('|’)$/", $pre))
                        $p .= ' ';
                }
                $p .= $w;
                $pre = $w;
            }

            // TXT 是最后一行
            if ($i + 1 == count($lines)) {
                $out .= "{$p}";
            } else {
                $out .= "{$p}\r\n";
            }
        }
        return $out;
    }

    /**
     * 根据 SW 的 JSON 生成 TXT 字幕的时间
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $json
     * @return string
     */
    static public function generateTextTimeLine($json)
    {
        $data = json_decode($json);
        $lines = $data->srt;
        $out = '';
        if (!is_array($lines)) {
            return '';
        }

        foreach ($lines as $i => $line) {
            $st = floatval($line[0]->st);
            $et = floatval($line[count($line) - 1]->et);
            $p = $st . ':' . $et;

            // TXT 是最后一行
            if ($i + 1 == count($lines)) {
                $out .= "{$p}";
            } else {
                $out .= "{$p}\r\n";
            }
        }
        return $out;
    }

    /**
     * 格式化时间
     * @author hualong
     * @param $seconds
     * @return string
     */
    static public function formatTime($seconds)
    {
        $f = round($seconds * 1000) % 1000;
        $num = ($seconds * 1000 - $f) / 1000;
        return gmdate('H:i:s', $num) . '.' . sprintf('%03d', $f);
    }

}