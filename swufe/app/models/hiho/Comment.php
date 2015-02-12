<?php namespace HiHo\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * Comment Model
 */
class Comment extends \Eloquent
{

    protected $table = 'comments';

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    /**
     * 获取某个视频或剪辑的所有评论
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public static function getComments($video_id, $fragment_id = '', $reply_id = 0)
    {
        if ($fragment_id) {
            $comments = Comment::whereRaw("video_id = ? AND fragment_id = ? AND reply_id = ?", array($video_id, $fragment_id, $reply_id))->orderby('created_at', 'desc')->get();
        } else {
            $comments = Comment::whereRaw("video_id = ? AND reply_id = ?", array($video_id, $reply_id))->orderby('created_at', 'desc')->get();
        }

        if ($comments) {
            foreach ($comments as $comment) {
                $comment->subComments = self::getComments($video_id, $comment->fragment_id, $comment->id);
                $user = User::find($comment->user_id);
                if ($user) {
                    $comment->userName = $user->nickname ? $user->nickname : $user->email;
                    $comment->userAvatar = $user->avatar ? $user->avatar : '/static/hiho-edu/img/avatar_default.png';
                } else {
                    $comment->userName = 'Anonymous';
                    $comment->userAvatar = '/static/hiho-edu/img/avatar_default.png';
                }
            }
        }
        return $comments;
    }
}