<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class UpdateSolrDocument
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class UpdateSolrDocument extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'hiho:update-solr-document';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all documents of solr server.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        // 从 CONFIG 读取搜索服务器 URL、Core
        $solr = new Apache_Solr_Service(
            Config::get('hiho.search_engine.solr_server'),
            Config::get('hiho.search_engine.solr_port'),
            Config::get('hiho.search_engine.solr_core'),
            false,
            new Apache_Solr_Compatibility_Solr4CompatibilityLayer()
        );

        if (!$solr->ping()) {
            die('PING solr server error.');
        } else {
            $this->info("PING Solr server time is " . $solr->ping() . ".");
        };

        // 遍历视频
        $documents_num = 0;

        $videos = Video::with('info')->with('pictures')->get();
        foreach ($videos as $video) {
            $document = new Apache_Solr_Document();
            $document->video_id = $video->video_id;
            $document->guid = $video->guid;
            $document->playid = $video->getPlayIdStr();
            $document->url = null; // TODO

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

            // INFO TODO: INFO 的插入顺序, 第一个应是首选语言
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
            $fts = SubtitleFt::where('video_id', '=', $video->video_id)->get();
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

            // TODO: comments
            // TODO: 不知道标题的语言...

            $this->info("Adding a new document, video_id is " . $video->video_id . ".");

            $solr->addDocument($document);
            $documents_num++;
        }

        // TODO: 400 Bad request;
        // [doc=1475] missing required field: title

        // TODO: 连接异常时...

        $this->info("Updated " . $documents_num . ' Documents.\n');

        $solr->commit();
        $solr->optimize();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }

}
