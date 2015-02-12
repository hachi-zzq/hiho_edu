<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CleanUserTestData extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'hiho:clean-user-test-data';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'clean the user test data from database';

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
	 * @author zhuzhengqian
	 * @return mixed
	 */
	public function fire()
	{
		//清理开发测试时候的用户数据
        $arrTable = array(
            'advertisements',##广告数据
            'annotations', ##注释表
            'comments',
            'favorites',
            'fragments',
            'fragments_resources',
            'fragments_shares',
            'fragments_tags',
            'highlights',
            'playlists',
            'playlists_favorites',
            'playlists_fragments',
            'questions',
            'question_records',
            'rest_logs',
            'tags',
            'topics',
            'topics_videos',
            'users_tokens',
            'videos_attachments',
            'videos_references',
        );

        foreach($arrTable as $tab){
            DB::statement("TRUNCATE TABLE  `".$tab."`");
            echo "clean table $tab\n";
        }
        echo "clean complete!!\n";
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
