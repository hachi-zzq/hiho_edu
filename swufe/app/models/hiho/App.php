<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * App Model
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class App extends \Eloquent
{
    protected $table = 'apps';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('User');
    }

}