<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * App 允许的分类 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class AppAllowedCategory extends \Eloquent
{

    protected $table = 'apps_allowed_categories';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

}