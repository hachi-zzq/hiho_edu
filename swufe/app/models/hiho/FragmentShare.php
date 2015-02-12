<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * 碎片分享  Model
 * @author houcheng<houcheng.zhang@autotiming.com>
 */
class FragmentShare extends \Eloquent
{
    protected $table = 'fragments_shares';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    public function fragment()
    {
        return $this->belongsTo('Fragment');

    }

}