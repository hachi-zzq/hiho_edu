<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 热门关键词 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class SearchHotKeyword extends \Eloquent
{
    protected $table = 'search_hot_keywords';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

}