<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class VideoTitleCorrect extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'hiho:correct-video-title';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'correct the video wrong title';

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
        $objVideoInfo = \VideoInfo::all();
        foreach($objVideoInfo as $info){
            $objVideo = \Video::find($info->video_id);
            if($objVideo){
                $taskId = $objVideo->origin_id;
                $objSewiseInfo = \SewiseVideosInfo::where('task_id',$taskId)->first();
                if($objSewiseInfo){
                    $title = $objSewiseInfo->title;
                    $objRowInfo = \VideoInfo::where('video_id',$info->video_id)->first();
                    $objRowInfo->title = $title;
                    $objRowInfo->save();
                    echo "save video id $objRowInfo->video_id\n";
                }
            }

        }
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
