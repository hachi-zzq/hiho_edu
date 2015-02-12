<?php namespace HihoEdu\Controller\Admin;
/**
 * Created with JetBrains PhpStorm.
 * User: zhu
 * DateTime: 14-6-26 下午2:13
 * Email:www321www@126.com
 */
class PlayListController extends AdminBaseController
{


    /**
     *笔记列表
     * @return mixed
     * @author zhuzhengqian
     */
    public function index()
    {
        $lists = \Playlist::orderby('id', 'desc')->paginate(20);
        if($lists){
            foreach($lists as $list){
                $list->fragmentCount = \PlaylistFragment::where('playlist_id',$list->id)->count();
                $list->user = \User::find($list->user_id);
            }
        }
        return \View::make('admin.playlist.index', compact('lists'));

    }

    /**
     *屏蔽/激活笔记
     * @param int $id
     * @return mixed
     * @author zhuzhengqian
     */
    public function check($id = NULL)
    {
        empty($id) and \App::abort(400, 'playlist is required');
        $objPlay = \Playlist::find($id);
        empty($objPlay) and \App::abort(404, 'playlist is not found');
        $statusToDo = $objPlay->status == 'NORMAL' ? '-1' : 'NORMAL';
        $objPlay->status = $statusToDo;
        $objPlay->save();
        ##redirect
        return \Redirect::to('/admin/playlists/index')->with('success_tips', '操作成功');
    }

    /**
     *删除笔记
     * @param $id
     * @author zhuzhengqian
     */
    public function destory($id){
        empty($id) and \App::abort(400, 'playlist is required');
        $objPlay = \Playlist::find($id);
        empty($objPlay) and \App::abort(404, 'playlist is not found');
        $objPlay->delete();

        return \Redirect::to('/admin/playlists/index')->with('success_tips', '操作成功');
    }
}
