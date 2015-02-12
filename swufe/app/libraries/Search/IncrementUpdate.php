<?php

namespace HiHo\Search;

/**
 * Solr服务器增量更新
 * @author Hanxiang<hanxiang.qiu@autotiming.com>
 */
class IncrementUpdate
{

    /**
     * 新增视频
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */

    public static function videoCreated($video_id)
    {
        // $solr = new \Apache_Solr_Service(
        //     \Config::get('hiho.search_engine.solr_server'),
        //     \Config::get('hiho.search_engine.solr_port'),
        //     \Config::get('hiho.search_engine.solr_core'),
        //     false,
        //     new \Apache_Solr_Compatibility_Solr4CompatibilityLayer()
        // );

        $solr = \App::make('\Apache_Solr_Service');

        $video = \Video::where('video_id', $video_id)->with('info')->with('pictures')->first();
        if (empty($video)) {
            return false;
        }
        $document = new \Apache_Solr_Document();
        $document->video_id = $video->video_id;
        $document->guid = $video->guid;
        $document->playid = $video->getPlayIdStr();
        $document->url = null;

        // Invalid Date String:'2013-12-07 19:07:56
        $document->created_at = $video->created_at->format('Y-m-d\\TH:i:s') . 'Z'; // UTC
        $document->updated_at = $video->updated_at->format('Y-m-d\\TH:i:s') . 'Z'; // UTC

        $document->country = $video->country;
        $document->language = $video->language;
        $document->length = $video->length;
        $document->aspect_ratio ? $document->aspect_ratio = $video->aspect_ratio : NULL;

        $document->source_id = $video->source_id;
        $document->origin_id = $video->origin_id;
        $document->liked = $video->liked;
        $document->viewed = $video->viewed;

        // PICTURES
        foreach ($video->pictures()->get() as $picture) {
            $document->addField('thumbnails', $picture->src);
            $document->addField('covers', $picture->src);
        }

        // INFO
        foreach ($video->info()->get() as $info) {
            $document->addField('title', $info->title ? $info->title : 'Unknown');
            $info->author ? $document->addField('author', $info->author) : NULL;
            $info->description ? $document->addField('description', $info->description) : NULL;
        }

        // CATEGORIES
        foreach ($video->categories()->get() as $category) {
            $document->addField('categories', $category->id);
        }

        // TAGS
        foreach ($video->tags()->get() as $tag) {
            $document->addField('tags', $tag->id);
        }

        // SUBTITLES FULLTEXT
        $fts = \SubtitleFt::where('video_id', '=', $video->video_id)->get();
        foreach ($fts as $ft) {
            if ($ft->language == 'en') {
                $ft->content ? $document->addField('subtitle_content_en', $ft->content) : NULL;
                $ft->timeline ? $document->addField('subtitle_timeline', $ft->timeline) : NULL;
            } elseif ($ft->language == 'zh_cn') {
                $ft->content ? $document->addField('subtitle_content_zh', $ft->content) : NULL;
                $ft->timeline ? $document->addField('subtitle_timeline', $ft->timeline) : NULL;
            } else {
                $ft->content ? $document->addField('subtitle_content', $ft->content) : NULL;
                $ft->timeline ? $document->addField('subtitle_timeline', $ft->timeline) : NULL;
            }
        }

        try {
            $solr->addDocument($document);
            $solr->commit();
            $solr->optimize();
        } catch (Apache_Solr_HttpTransportException $e) {
            return false;
        }
    }

    /**
     * 删除视频
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public static function videoDeleted($video_id)
    {
        $solr = new \Apache_Solr_Service(
            \Config::get('hiho.search_engine.solr_server'),
            \Config::get('hiho.search_engine.solr_port'),
            \Config::get('hiho.search_engine.solr_core'),
            false,
            new \Apache_Solr_Compatibility_Solr4CompatibilityLayer()
        );
        $solr->deleteById($video_id);
        $solr->commit();
        $solr->optimize();
    }
}