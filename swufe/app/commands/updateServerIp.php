<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateServerIp extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'hiho:update-server-ip';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'update the server ip to new ';

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
        //修改fragment里面图片地址
        $objFragment = \Fragment::all();
        foreach($objFragment as $fg){
            $obj = \Fragment::find($fg->id);
            $obj->cover = str_replace('swufe.autotiming.com','autotiming.swufe.edu.cn',$obj->cover);
            $obj->save();
            echo "save Fragment cover id $obj->id\n";
            unset($obj);
        }

        //修改sewiseVideoPic表里面的ip
       $objSewisePic = \SewiseVideosPicture::all();
       foreach($objSewisePic as $pic){
            $obj = \SewiseVideosPicture::find($pic->id);
            $obj->src = str_replace('171.221.3.200','autotiming.swufe.edu.cn',$obj->src);
           $obj->save();
           echo "save SewiseVideoPic id $pic->id\n";
           unset($obj);
       }

        //修改subtitle表中ip
        $objSubtitle =  \DB::table('subtitles')->whereRaw("deleted_at is null and url != ''")->select('id','url')->get();
        foreach($objSubtitle as $sub){
            $obj = \HiHo\Model\Subtitle::find($sub->id);
            $obj->url = str_replace('202.115.123.50','171.221.3.200',$obj->url);
            $obj->save();
            echo "save Subtitle id $obj->id\n";
            unset($obj);
        }
        //修改video_pic表中ip
        $objVideoPic = \VideoPicture::all();
        foreach($objVideoPic as $videoPic){
            $obj = \VideoPicture::find($videoPic->id);
            $obj->src = str_replace('171.221.3.200','autotiming.swufe.edu.cn',$obj->src);
            $obj->save();
            echo "save VideoPic id $obj->id\n";
            unset($obj);
        }
        //修改video_resource
        $objResource = \VideoResource::all();
        foreach($objResource as $resource){
            $obj = \VideoResource::find($resource->id);
            $obj->src = str_replace('171.221.3.200','autotiming.swufe.edu.cn',$obj->src);
            $obj->save();
            echo "save VideoResource id $obj->id\n";
            unset($obj);
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
