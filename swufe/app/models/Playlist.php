<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Playlist extends \Eloquent
{

    use SoftDeletingTrait;

    protected $table = 'playlists';

    /**
     * 绑定加载事件
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public static function boot()
    {
        parent::boot();

        // 创建 PlayID
        self::created(function ($playlist) {
            $playid = PlayID::createWithEntity($playlist);
        });

        // 删除 PlayID
        self::deleted(function ($playlist) {
            PlayID::dropWithEntity($playlist);
        });
    }

    /**
     * 用户一对多关系
     * @return mixed
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    /**
     * 碎片一对多关系
     * @return mixed
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function fragments()
    {
        $pfs = PlaylistFragment::with('Fragment')->
            where('playlist_id', '=', $this->id)->orderBy('rank', 'ASC')->get();
        return $pfs;
    }

    /**
     * 收藏一对多关系
     * @return mixed
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function favorites()
    {
        return $this->hasMany('PlaylistFavorite');
    }

    /**
     * 获得 PlayId 字符串
     * @return bool|null
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getPlayIdStr()
    {
        return PlayID::createWithEntity($this)->play_id;
    }

    /**
     * 获取某个笔记内短视频的所有评论
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function getPlaylistComments($playlist_id)
    {
        $comments = array();
        $playlistFragments = PlaylistFragment::whereRaw("playlist_id = ?", array($playlist_id))->get();
        if (empty($playlistFragments)) {
            return $comments;
        }

        foreach ($playlistFragments as $pf) {
            $fragment = Fragment::find($pf->fragment_id);
            if (!$fragment) {
                continue;
            }
            $commentModel = new Comment();
            $fragmentComments = $commentModel->getComments($fragment->video_id, $fragment->id);
            foreach ($fragmentComments as $fc) {
                array_push($comments, $fc);
            }
        }
        return $comments;
    }

    /**
     *
     * @return mixed
     * @author zhuzhengqian
     */
    public function delete()
    {
        //删除playlist_fragment
        \PlaylistFragment::where('playlist_id', $this->id)->delete();
        //删除favorite
        \Favorite::where('play_id', $this->getPlayIdStr())->delete();
        return parent::delete();
    }
}