<?php namespace HihoEdu\Controller\Admin;

class FragmentController extends AdminBaseController
{

    /**
     *段视频列表
     * @return mixed
     * @author zhuzhengqian
     */
    public function index()
    {
        $objFragment = \Fragment::orderby('id', 'desc')->paginate(parent::PAGE_SIZE);
        if($objFragment){
            foreach($objFragment as $fragment){
                //收集其他信息，一起返回
                //user
                $objUser = \User::find($fragment->user_id);
                $videoInfo = \VideoInfo::where('video_id',$fragment->video_id)->first();
                if($videoInfo){
                    $fragment->title = $videoInfo->title;
                }
                if($objUser) $fragment->user = $objUser;
            }
        }
        return \View::make('admin.fragment.bs3_fragment_list', compact('objFragment'));
    }

    /**
     *短视频删除
     * @param int $id
     * @return mixed
     * @author zhuzhengqian
     */
    public function delete($id = NULL)
    {
        empty($id) and \App::abort(400, 'fragment is required');
        $objFragment = \Fragment::find($id);
        empty($objFragment) and \App::abort(404, 'fragment is not found');
        $objFragment->delete();

        //删除fragment的时候，playlist中的数据统计相应的变换
        $objPlaylistFragment = \PlaylistFragment::where('fragment_id',$id)->first();
        if($objPlaylistFragment){
            $objPlay = \Playlist::find($objPlaylistFragment->playlist_id);
            $objPlay->totel_number =  $objPlay->totel_number-1;
            $objPlay->save();
        }
        return \Redirect::to('admin/fragments/index')->with('success_tips', 'fragment delete success');
    }


}
