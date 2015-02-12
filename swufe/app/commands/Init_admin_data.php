<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use \HiHo\Model\User;

class Init_admin_data extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'hiho:init-admin-data';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'init user table and add admin info';

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
		//
        $obj = user::where('email','swufe@example.com')->first();
        if($obj){
            $user = User::find($obj->user_id);
        }else{
            $user = new User();
        }
        $user->guid = \Uuid::v4();
        $user->email = 'swufe@example.com';
        $user->nickname = '系统管理员';
        $user->password = \Hash::make('admin123456');
        $user->last_time = date('Y-m-d H:i:s');
        $user->avatar = '/static/hiho-edu/img/avatar_default.png';
        $user->status = User::STATUS_NORMAL;
        $user->is_admin = 1;
        $user->save();
        echo "email: $user->email\n";
        echo "password: admin123456\n";
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
