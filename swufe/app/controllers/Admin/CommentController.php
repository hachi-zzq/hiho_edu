<?php namespace HihoEdu\Controller\Admin;
/**
 * Created with JetBrains PhpStorm.
 * User: zhu
 * DateTime: 14-6-10 下午5:19
 * Email:www321www@126.com
 */

class CommentController extends AdminBaseController
{

    /**
     *评论列表
     * @return mixed
     * @author zhuzhengqian
     */
    public function index()
    {
        $objComment = \DB::table('comments')->whereRaw('deleted_at is null')->orderBy('created_at', 'desc')->paginate(parent::PAGE_SIZE);
        if ($objComment) {
            foreach ($objComment as $comment) {
                $comment->user = \User::find($comment->user_id);
                $comment->video = \VideoInfo::where('video_id', '=', $comment->video_id)->first();
            }
        }
        return \View::make('admin.comment.list', compact('objComment'));

    }

    /**
     *删除评论
     *@param int $id
     * @return mixed
     * @author zhuzhengqian
     */
    public function delete($commentId = NULL)
    {
        empty($commentId) and \App::abort('404', 'comment id is require');
        \Comment::destroy($commentId);
        return \Redirect::to('/admin/comments/index')->with('success_tips', '成功删除了id为'.$commentId.'的评论');
    }


    /**
     *修改评论
     * @return mixed
     * @param $id
     * @author zhuzhengqian
     */
    public function modify($id=NULL){
        // check get or post
        $id = \Input::get('id') ? \Input::get('id') : $id;
        if(!$id || !$objComment=\Comment::find($id)){
            return \Redirect::to('/admin/comment/modify/'.$id)->with('error_tips', '试图操作不存在的评论对象');
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $objComment->content = addslashes(\Input::get('content'));
            $objComment->save();
            return \Redirect::to('/admin/comments/index')->with('success_tips', '成功修改了id为'.$objComment->id.'的评论');
        }
        return \View::make('admin.comment.modify',compact('objComment'));
    }


}
