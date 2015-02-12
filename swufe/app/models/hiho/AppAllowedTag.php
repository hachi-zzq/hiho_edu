<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * App 允许的 Tag Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class AppAllowedTag extends \Eloquent
{

    protected $table = 'apps_allowed_tags';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

}