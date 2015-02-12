<?php namespace HiHo\Search;

use HiHo\Model\SearchHistory;
use HiHo\Model\Video;
use HiHo\Model\VideoInfo;
use HiHo\Model\VideoTvPlay;
use HiHo\Model\VideoTag;
use HiHo\Model\TvChannel;
use HiHo\Model\SubtitleFt;
use HiHo\Model\Tag;
use HiHo\Other\Hash;
use HiHo\Search\Suggestion;

/**
 * Class Searcher
 * @package HiHo\Search
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Searcher
{

    protected $keywords;

    protected $time;

    protected $paginate;

    protected $withSubtitle;

    protected $withInfo;

    protected $withChannel;

    protected $withCategory;

    protected $withTag;

    protected $languages;

    protected $useCache;

    protected $videos;

    protected $fragments;

    protected $subtitles;

    protected $searchResult;

    const MAX_LINE_IN_SUBTITLE = 6;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->keywords = array();
        $this->withSubtitle = TRUE;
        $this->withInfo = TRUE;
        $this->withSource = TRUE;
        $this->withTag = TRUE;
        $this->languages = array('en', 'en-US');
        $this->useCache = TRUE;
        $this->searchResult = array();

    }

    /**
     * @param mixed $keywords
     * @paeam int $wd
     */
    public function setKeywords($keywords, $wd = "1")
    {
        $this->keywords = $keywords;
        \Session::put('query', $keywords . "|||" . $wd);
        $suggestion = new Suggestion($wd);
        $kw = $suggestion->addSearchResultCount();

        /*
        //增加搜索记录
        $sh = new SearchHistory();
        $sh->user_id = \Auth::check()?\Auth::user()->user_id:Null;
        $sh->keywords_id = $kw->id;
        $sh->keywords = $keywords;
        $sh->language = $kw->language;
        $sh->save();
        */

    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @param mixed $withInfo
     */
    public function setWithInfo($withInfo)
    {
        $this->withInfo = $withInfo;
    }

    /**
     * @param mixed $withCategory
     */
    public function setWithCategory($withCategory)
    {
        $this->withCategory = $withCategory;
    }

    /**
     * @param mixed $withChannel
     */
    public function setWithChannel($withChannel)
    {
        $this->withChannel = $withChannel;
    }

    /**
     * @param mixed $withSubtitle
     */
    public function setWithSubtitle($withSubtitle)
    {
        $this->withSubtitle = $withSubtitle;
    }

    /**
     * @param mixed $withTag
     */
    public function setWithTag($withTag)
    {
        $this->withTag = $withTag;
    }


    /**
     * @param mixed $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * @param boolean $useCache
     */
    public function setUseCache($useCache)
    {
        $this->useCache = $useCache;
    }

    /**
     * @param mixed $paginate
     */
    public function setPaginate($paginate)
    {
        $this->paginate = $paginate;
    }

    /**
     * @return mixed
     */
    public function getSearchResult()
    {
        return $this->searchResult;
    }

    /**
     * 搜索含有关键词的信息的视频
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $kw
     * @return array
     */
    protected function queryWithInfo($kw)
    {
        // TODO: 过滤语言
        // TODO: Redis

        $videosInfo = VideoInfo::
            where('title', 'LIKE', '%' . $kw . '%')
            ->orWhere('author', 'LIKE', '%' . $kw . '%')
            ->orWhere('description', 'LIKE', '%' . $kw . '%')
            ->get();

        foreach ($videosInfo as $i) {
            $this->videos[] = $i->video_id;
        }
    }

    /**
     * 搜索含有关键词的字幕
     *
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $kw
     */
    protected function queryWithSubtitle($kw)
    {
        // TODO: 过滤语言

        $subtitlesFt = SubtitleFt::where('content', 'LIKE', '%' . $kw . '%')
            ->where('is_original', '=', 1)
            ->get();

        foreach ($subtitlesFt as $s) {
            $this->subtitles[] = $s;
            $this->videos[] = $s->video_id;
        }
    }

    /**
     * 搜索含有关键词的频道的视频
     * @author Luyu<luyu.zhang@autotiming.com> sky bao
     * @param $kw
     */
    protected function queryWithChannel($kw)
    {
        $tvChannel = TvChannel::where('name', 'LIKE', '%' . $kw . '%')
            ->where('status', '=', 0)
            ->get();
        if ($tvChannel) {
            foreach ($tvChannel as $tv) {
                $videoTvPlay = VideoTvPlay::where('channel_id', $tv->id)
                    ->get();
                if ($videoTvPlay) {
                    foreach ($videoTvPlay as $video) {
                        $this->videos[] = $video->video_id;
                    }
                }
            }
        }

    }

    /**
     * 搜索含有关键词 Tag 的视频
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $kw
     */
    protected function queryWithTag($kw)
    {
        $tag = Tag::
            where('name', '=', $kw)
            ->get()->first();

        if ($tag) {
            $videoTags = VideoTag::
                where('tag_id', '=', $tag->id)
                ->get();

            foreach ($videoTags as $t) {
                $this->videos[] = $t->video_id;
            }
        }
    }

    /**
     * 记录搜索历史
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $kw
     * @param $user_id
     */
    public function saveSearchHistory($kw, $user_id = NULL)
    {
        $history = new SearchHistory();
        $history->keywords = $kw;
        $history->result_count = 0;
        $history->user_id = $user_id;
        $history->save();
    }

    /**
     * 查询
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function query()
    {
        /**
         * 1. 搜索带有所有关键词的 VIDEOS
         * 2. 搜索带有所有关键词的 SUBTITLES
         * 3. SUBTITLES 取带有关键词的行 切为 碎片
         * 4. 碎片与 VIDEOS 进行绑定数据结构, 单行碎片太长, 需要切割文字...
         * 5. 权重计算
         * 6. 缓存
         * 7. 记录搜索历史
         */
        $kw = $this->keywords;

        /**
         * 按视频描述信息搜索
         */
        if ($this->withInfo) {
            $this->queryWithInfo($kw);
        }

        /**
         * 按字幕碎片搜索
         */
        if ($this->withSubtitle) {
            $this->queryWithSubtitle($kw);
        }

        /**
         * 按视频标签搜索
         */
        if ($this->withTag) {
            $this->queryWithTag($kw);
        }

        /**
         * 按视频频道搜索
         */
        if ($this->withChannel) {
            $this->queryWithChannel($kw);
        }

//        if ($this->subtitles) {
//            $this->generateFragments($this->subtitles, $kw);
//        }

        $this->searchResult = $this->generateResult($this->videos, $this->fragments);

    }

    /**
     * 搜索单个视频碎片
     * @author sky bao
     */
    public function querySingleVideoSubtitles($videoId, $kw)
    {
        //查找视频字幕
        $subtitlesFt = SubtitleFt::where('video_id', $videoId)->first();
        $subitileResult = array();
        $fragments = '';
        if ($subtitlesFt) {
            $subitileResult[] = $subtitlesFt;
            $this->generateFragments($subitileResult, $kw);
            if (isset($this->fragments[$videoId])) {
                $fragments = $this->fragments[$videoId];
            }
        }
        return $fragments;
    }

    /**
     * 生成搜索结果结构(视频 + 碎片)
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $videos
     * @param $subtitles
     */
    private function generateResult($videos, $fragments)
    {
        if (!$videos) {
            $videos = array('-1'); // hack
        }
        $queryWhere = '1>0 ';
        switch ($this->time) {
            case 'week':
                $week = time() - 60 * 60 * 24 * 7;
                $queryWhere .= 'and unix_timestamp(created_at) >= ' . $week;
                break;
            case 'month':
                $month = time() - 60 * 60 * 24 * 30;
                $queryWhere .= 'and unix_timestamp(created_at) >= ' . $month;
                break;
            case 'year':
                $year = time() - 60 * 60 * 24 * 365;
                $queryWhere .= 'and unix_timestamp(created_at) >= ' . $year;
        }
        if ($this->paginate) {
            $videos = Video::whereIn('video_id', array_unique($videos))
                ->whereRaw($queryWhere)
                ->paginate($this->paginate);

            foreach ($videos as $video) {
                $video->vinfo = Video::find($video->video_id);
            }

        } else {
            $videos = Video::whereIn('video_id', array_unique($videos))
                ->whereRaw($queryWhere)
                ->get();
            foreach ($videos as $video) {
                $video->vinfo = Video::find($video->video_id);
            }
        }

        /**
         * 查找这堆视频的字幕中碎片的行
         */
        foreach ($videos as &$v) {
            if (isset($fragments[$v->video_id])) {
                $v->fragments = $fragments[$v->video_id];
            }

            if ($v->info->count() > 0) {
                $v->info = $v->info->toArray();
            } else {
                $v->info = NULL;
            }

            if ($v->pictures->count() > 0) {
                $v->pictures = $v->pictures->toArray();
            } else {
                $v->pictures = NULL;
            }
        }

        return $videos;
    }

    /**
     * 生成碎片
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $subtitles
     * @param $kw
     */
    private function generateFragments($subtitles, $kw)
    {
        foreach ($subtitles as $sFt) {
            // TODO: 根据碎片出现次数提升权重

            // 查找关键词所在正文行数
            $lines = $this->findKeywordInLine($sFt->content, $kw);
            $i = 1;
            foreach ($lines as $key => $value) {
                // 同一个字幕符合条件的碎片太多,跳过
                if ($i > self::MAX_LINE_IN_SUBTITLE) {
                    continue;
                }

                // VIEW 里 {{ date('H:i:s', (int)$f['time'])}}
                // 该句碎片的时间位置
                $time = explode(':', $this->getFragmentTimeByLineNumber($sFt->timeline, $key));

                $this->fragments[$sFt->video_id][] = array(
                    // TODO: 字幕语言、ID 等
                    'line' => $key,
                    'time' => reset($time),
                    'text' => $value
                );
                $i++;
            }
        }
    }

    /**
     * 正则查找关键词所在行
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $content
     * @param xml
     * @return array
     */
    private function findKeywordInLine($content, $keyword)
    {
        return preg_grep("/$keyword/i", $this->sliceLine($content));
    }

    /**
     * 获得字幕某行的开始时间和结束时间
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $timeline
     * @param $lineNumber
     * @return bool
     */
    private function getFragmentTimeByLineNumber($timeline, $lineNumber)
    {
        // TODO: 线上这里容易报错
        // 查找 TIMELINE 里 $lineNumber 行
        $arrTimeLine = $this->sliceLine($timeline);
        return isset($arrTimeLine[$lineNumber - 1]) ? $arrTimeLine[$lineNumber - 1] : 0;
    }

    /**
     * 文本分行(数组内每个元素是一行)
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $text
     * @return array
     */
    private function sliceLine($text)
    {
        $lines = array();
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $text) as $line) {
            $lines[] = $line;
        }
        return $lines;
    }
} 