<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CleanSubtitleFT
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class CleanSubtitleFT extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'hiho:clean-subtitle-ft';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean the HiHo Subtitle FT in Database.';

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
        /**
         * 1. 删除数据库内所有的 Subtitle type 为 TXT
         * 2. 重新生成 TXT, 注意处理换行问题
         * 3. 生成 FT
         */
        $this->info('Deleteing the Subtitles with type = txt.');
        DB::table('subtitles')->where('type', '=', 'TXT')->delete();

        $this->info('Createing the FT of Subtitles.');

        // Allowed memory size of 134217728 bytes exhausted (tried to allocate 297639 bytes)
        Subtitle::where('type', '=', 'JSON')->chunk(100, function ($subtitles) {
            foreach ($subtitles as $subtitle) {
                $subtitle->save(); // 触发 生成 FT
                echo 'created a ft that id is ' . $subtitle->id . ". \r\n";
                unset($subtitle);
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
