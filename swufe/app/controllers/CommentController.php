<?php

use HiHo\Model\Comment;
use HiHo\Other\Tool;

class CommentController extends BaseController
{

    /**
     * 添加视频或剪辑的评论
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function addPost()
    {
        $input = Input::only('video_id', 'fragment_id', 'playing_time', 'content');
        $rules = array(
            'video_id' => 'required',
            'content' => 'required'
        );

        $v = Validator::make($input, $rules);
        //check rules
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -2, "message" => $messages->first()));
        }

        $comment = new Comment();
        $comment->guid = Uuid::v4();
        $comment->user_id = Auth::user() ? Auth::user()->user_id : 0;
        $comment->video_id = $input['video_id'];
        $comment->fragment_id = $input['fragment_id'];
        $comment->reply_id = 0;
        $comment->playing_time = $input['playing_time'];
        $comment->content = addslashes(strip_tags($input['content']));
        $comment->playing_time = $input['playing_time'];
        $comment->save();

        //return new comment info
        $new = array();
        $new['avatar'] = Auth::user() ? Auth::user()->getAvatar : '/static/hiho-edu/img/avatar_default.png';
        if (Auth::user()) {
            $new['userName'] = empty(Auth::user()->nickname) ? Auth::user()->email : Auth::user()->nickname;
        } else {
            $new['userName'] = 'Anonymous';
        }

        $newComment = Comment::find($comment->id);
        $new['created_at'] = Tool::timeTransfer($newComment->created_at);
        $new['content'] = $newComment->content;

        return json_encode(array("status" => 0, "message" => 'add success', 'comment' => $new));
    }

    /**
     * 回复评论
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function replyPost()
    {
        $input = Input::only('video_id', 'fragment_id', 'reply_id', 'playing_time', 'content');
        $rules = array(
            'video_id' => 'required',
            'reply_id' => 'required',
            'content' => 'required'
        );

        $v = Validator::make($input, $rules);
        //check rules
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -2, "message" => $messages->first()));
        }

        $comment = new Comment();
        $comment->guid = Uuid::v4();
        $comment->user_id = Auth::user() ? Auth::user()->user_id : 0;
        $comment->video_id = $input['video_id'];
        $comment->fragment_id = $input['fragment_id'];
        $comment->reply_id = $input['reply_id'];
        $comment->playing_time = $input['playing_time'];
        $comment->content = $input['content'];
        $comment->playing_time = $input['playing_time'];
        $comment->save();

        return json_encode(array("status" => 0, "message" => 'add success'));
    }
}