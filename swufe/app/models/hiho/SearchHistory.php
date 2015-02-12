<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 搜索历史 Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class SearchHistory extends \Eloquent
{
    protected $table = 'search_histories';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
}