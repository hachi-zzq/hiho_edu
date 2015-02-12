<?php

namespace HiHo\Search;

use Illuminate\Support\Facades\Config;
use \Apache_Solr_Service;
use \Apache_Solr_Compatibility_Solr4CompatibilityLayer;

/**
 * HiHo 搜索引擎 Solr 客户端抽象类
 * @package HiHo\Search
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class Client
{
    /**
     * TODO: 后续
     * - 记录日志
     * - ITEM TO ITEM 推荐记录
     */

    private $config;

    private $solr;

    private $connection;

    private $params;

    private $sort;

    private $select;

    private $filter_string;

    private $filter_query;

    private $response_header;

    private $response_highlighting;

    public function __construct($config = array())
    {
        // 从 CONFIG 读取搜索服务器 URL、Core
        $this->solr = new Apache_Solr_Service(
            Config::get('hiho.search_engine.solr_server'),
            Config::get('hiho.search_engine.solr_port'),
            Config::get('hiho.search_engine.solr_core'),
            false,
            new Apache_Solr_Compatibility_Solr4CompatibilityLayer()
        );

        // 写入链接状态
        if (!$this->solr->ping()) {
            $this->connection = False;
        } else {
            $this->connection = True;
        };

        // 默认字符串过滤器
        $this->filter_string = array('*', ':', '~', '+', '-', '(', ')');
    }

    /**
     * @param mixed $filter_query
     */
    public function setFilterQuery($filter_query)
    {
        $this->filter_query = $filter_query;
    }

    /**
     * @return mixed
     */
    public function getFilterQuery()
    {
        return $this->filter_query;
    }

    /**
     * @param mixed $filter_string
     */
    public function setFilterString($filter_string)
    {
        $this->filter_string = $filter_string;
    }

    /**
     * @return mixed
     */
    public function getFilterString()
    {
        return $this->filter_string;
    }

    /**
     * Example: $this->select = 'video_id,title';
     * @param mixed $select
     */
    public function setSelect($select)
    {
        $this->select = $select;
    }

    /**
     * @return mixed
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return mixed
     */
    public function getResponseHeader()
    {
        return $this->response_header;
    }

    /**
     * @return mixed
     */
    public function getResponseHighlighting()
    {
        return $this->response_highlighting;
    }

    /**
     * 调用搜索并返回结果
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $query
     * @param int $offset
     * @param int $limit
     * @return mixed|null
     */
    public function search($query, $offset = 0, $limit = 15)
    {
        if (!$this->connection) {
            return NULL;
        }

        // 预处理输入关键词, 排除高级搜索特性
        // 不修改默认操作符, 默认是 OR
        if ($this->filter_string) {
            $query = str_replace($this->filter_string, " ", $query);
        }

        // 设定过滤器
        // Example: $this->filter_query = array('length:[0 TO 200]', 'language:en');
        $this->filter_query = array();

        $this->params = array(
            'fq' => $this->filter_query,
            'sort' => $this->sort,
            'fl' => $this->select,
            'df' => 'text',
            'hl' => 'true',
            'hl.fl' => "title, description, subtitle_content, subtitle_content_en, subtitle_content_zh, comments",
            'hl.simple.pre' => '<em>',
            'hl.simple.post' => '</em>',

        );

        // $limit == $start, 含...
        $response = $this->solr->search($query, $offset, $limit, $this->params);

        if ($response->getHttpStatus() == 200) {
            // $response->responseHeader
            // $response->response->numFound
            // $response->response->docs

            foreach ($response->response->docs as $doc) {
            }

            $this->response_header = $response->responseHeader;
            $this->response_highlighting = $response->highlighting;

            return $response->response;

        } else {
            // echo $response->getHttpStatusMessage();
            return NULL;
        }
    }

    /**
     * 正则查找关键词所在行
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $content
     * @param $keyword
     * @return array
     */
    private function findKeywordInLine(&$arrText, $keyword)
    {
        return preg_grep('/' . $keyword . '/i', $arrText);
    }


    /**
     * 通过行号数组+行号获得时间戳
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $arrTimeLine
     * @param $lineNumber
     * @return int
     */
    private function getTimestampByLineNumber(&$arrTimeLine, $lineNumber)
    {
        $timestamp = isset($arrTimeLine[$lineNumber - 1]) ? $arrTimeLine[$lineNumber - 1] : 0;
        return $timestamp;
    }

    /**
     * 高亮关键词, 即替换带 EM
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $text
     * @param $keywords
     * @return mixed
     */
    public function highlightSubtitle($text, $keywords)
    {
        // TODO: 多关键词分别替换
        // TODO: 不能截断单词...
        $regular = '/' . $keywords . '/i';
        return preg_replace($regular, '<em>' . $keywords . '</em>', $text);
    }

    /**
     * 格式化生成碎片字幕
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $fulltext
     * @param $timezone
     * @param $keywords
     */
    public function formatFragments(&$fulltext, &$timezone, $keywords)
    {
        $fragments = array();

        // 时间轴拆成行号为键的数组
        $arrTimeLine = $this->sliceLinesToArray($timezone);
        $arrText = $this->sliceLinesToArray($fulltext);

        // Fulltext 中查找带关键词的句, 读行号
        // TODO: 拼合多个包含关键词的行号
        $lines = $this->findKeywordInLine($arrText, $keywords);

        foreach ($lines as $key => $value) {

            // 文本高亮 <em>
            $text = $this->highlightSubtitle($value, $keywords);

            $timestamp = $this->getTimestampByLineNumber($arrTimeLine, $key);
            $st = @reset(explode(':', $timestamp));

            // 行号, 时间戳(: 分隔), 文本
            $fragments[] = array(
                'line' => $key,
                'timestamp' => $timestamp,
                'st' => $st,
                'text' => $text
            );
        }

        return $fragments;
    }

    /**
     * 文本分行为数组(数组内每个元素是一行)
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $text
     * @return array
     */
    private function sliceLinesToArray($text)
    {
        $lines = array();
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $text) as $line) {
            $lines[] = $line;
        }
        return $lines;
    }


} 