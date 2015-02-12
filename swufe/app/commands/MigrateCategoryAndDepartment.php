<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MigrateCategoryAndDepartment extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'hiho:create-tree-path';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the tree path in categories and departments table.';

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
        $this->info('Create tree path in categories table');
        $this->migrateCategory();
        $this->info('Create tree path in categories table completed');
        $this->info('Create tree path in departments table');
        $this->migrateDepartment();
        $this->info('Create tree path in departments table completed');

    }

    public function migrateCategory($parentId = 0, $parentPath = '')
    {
        $path = rtrim($parentPath, '/') . '/' . $parentId;
        if ($parentId == 0) {
            $path = '/';
        }
        DB::table('categories')->where('parent', '=', $parentId)->update(array('path' => $path));
        $nodes = DB::table('categories')->where('parent', '=', $parentId)->get();
        foreach ($nodes as $c) {
            $this->migrateCategory($c->id, $c->path);
        }
    }

    public function migrateDepartment($parentId = 0, $parentPath = '')
    {
        $path = rtrim($parentPath, '/') . '/' . $parentId;
        if ($parentId == 0) {
            $path = '/';
        }
        DB::table('departments')->where('parent', '=', $parentId)->update(array('path' => $path));
        $nodes = DB::table('departments')->where('parent', '=', $parentId)->get();
        foreach ($nodes as $c) {
            $this->migrateDepartment($c->id, $c->path);
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
