<?php

use Illuminate\Support\Facades\Cache;
use HiHo\Search\Client;

/**
 * 搜索
 * @package edu.hiho.com
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class SearchController extends \BaseController
{
    /**
     * 搜索结果页
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return mixed
     */
    public function getSearch()
    {
        // 表单验证规则
        $input = Input::only('keywords');
        $rules = array(
            'keywords' => array('required', 'min:2', 'max:64'),
        );
        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return Redirect::to('/')
                ->with('error_tips', $messages->first());
        }

        // 语言列表
        $languages = Language::all();

        // 准备分页
        $paginator = App::make('paginator');
        $perPage = 5;
        $page = $paginator->getCurrentPage();

        // 引入新搜索客户端
        $keywords = $input['keywords'];
        $client = new Client();
        $response = $client->search($keywords, (($page - 1) * $perPage), $perPage);

        // 查询时间
        $queryTime = $client->getResponseHeader()->QTime / 1000;

        // 高亮标题等
        $highlighting = $client->getResponseHighlighting();

        // 预处理数据
        if (isset($response->docs)) {
            foreach ($response->docs as &$row) {
                $video_id = $row->video_id;
                $row->created_at = new DateTime(str_replace('Z', 'UTC', $row->created_at));
                $row->updated_at = new DateTime(str_replace('Z', 'UTC', $row->updated_at));

                if (isset($highlighting->$video_id->title)) {
                    // 替换标题高亮
                    $row->title = reset($highlighting->$video_id->title);
                }

                if (isset($highlighting->$video_id->description)) {
                    // 替换描述高亮
                    $row->description = reset($highlighting->$video_id->description);
                }

                // 输出字幕结果高亮
                // 1. 遍历 TXT 字幕全文, 替换分词后的[关键词]为高亮, 即带 EM 标签的词
                // 2. 增加按行号配对的时间戳, 即显示的时间和 URL 的 STARTTIME
                $row->title = is_string($row->title) ? $row->title : array_values($row->title)[0];
                if (isset($row->thumbnails)) {
                    $row->thumbnails = is_string($row->thumbnails) ? $row->thumbnails : array_values($row->thumbnails)[0];
                }
                else {
                    $videoPicture = VideoPicture::where('video_id', $video_id)->first();
                    if ($videoPicture) {
                        $row->thumbnails = $videoPicture->src;
                    }
                    else {
                        $row->thumbnails = '/static/img/video_default.png';
                    }
                }

                $timezone = $row->subtitle_timeline;
                if (isset($row->subtitle_content_en)) {
                    $fulltext = $row->subtitle_content_en;
                } else if (isset($row->subtitle_content_zh)) {
                    $fulltext = $row->subtitle_content_zh;
                }

                // 缓存碎片处理结果
                $fkey = sprintf('search_fragment_%s_%s', $row->video_id, hash('sha1', $keywords));

                if (Cache::has($fkey)) {
                    $fragments = Cache::get($fkey);
                } else {
                    $fragments = $client->formatFragments(
                        $fulltext, $timezone, $keywords
                    );
                    Cache::put($fkey, $fragments, 1);
                }

                $row->fragments = $fragments;
            }
        }

        $searchResult = $paginator->make($response->docs, $response->numFound, $perPage);

        // 返回搜索结果
        return View::make('search_result')
            ->with('languages', $languages)
            ->with('keywords', $keywords)
            ->with('searchResult', $searchResult)
            ->with('queryTime', $queryTime);
    }

    /**
     * 获得单个视频的高亮字幕
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string
     */
    public function getSingleSubtitleFt()
    {
        // 废弃需要重写的方法
    }
}