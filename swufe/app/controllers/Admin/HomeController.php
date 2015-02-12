<?php namespace HihoEdu\Controller\Admin;

use HihoEdu\Controller\Admin\AdminBaseController;

class HomeController extends AdminBaseController
{

    /**
     *admin home
     * @return mixed
     * @author zhuzhengqian
     */
    public function index()
    {
        //admin info
        $objAdmin = \User::find(\Session::get('admin_id'));
        //video count
        $videoCount = \Video::count();
        //playlist count
        $playlistCount = \Playlist::count();
        //fragment count
        $fragmentCount = \Fragment::count();
        //new register
        $objNewUser = \User::orderBy('created_at','DESC')->take(7)->get();
        //new comment
        $objComment = \Comment::orderBy('created_at','DESC')->take(10)->get();
        //顺便统计评论的视频和用户的信息
        if($objComment){
            foreach($objComment as $comment){
                $comment->videoInfo = \VideoInfo::where('video_id',$comment->video_id)->first();
                $comment->userInfo = \User::find('user_id');
            }
        }
        return \View::make('admin.home.index',compact('videoCount','playlistCount','fragmentCount','objNewUser','objComment','objAdmin'));
    }


}
