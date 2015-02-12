<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use HiHo\Model\PlayID;

/**
 * Class UpdateAppPlayId
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class UpdateAllPlayId extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'hiho:update-playid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all playid.';

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
        // 更新 Video
        Video::chunk(100, function ($videos) {
            foreach ($videos as $video) {
                $playid = PlayID::createWithEntity($video);
            }
        });

        // 更新 Fragment
        Fragment::chunk(100, function ($fragments) {
            foreach ($fragments as $fragment) {
                $playid = PlayID::createWithEntity($fragment);
            }
        });

        // 更新 Playlist
        Playlist::chunk(100, function ($playlists) {
            foreach ($playlists as $playlist) {
                $playid = PlayID::createWithEntity($playlist);
            }
        });

        // TODO: 检查已失效的 PlayID 删除之, 判断实体是否存在
        PlayID::chunk(100, function ($playids) {
            foreach ($playids as $playid) {
                if (PlayID::isExistWithTypeAndId($playid->entity_type, $playid->entity_id)) {
                    echo '';
                } else {
                    echo '';
                }
            }
        });
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
