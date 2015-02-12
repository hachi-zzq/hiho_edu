<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 碎片URL MODEL
 * STATUS: NORMAL / UNDEFINITION / WAITING
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class FragmentResource extends \Eloquent
{
    protected $table = 'fragments_resources';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
}