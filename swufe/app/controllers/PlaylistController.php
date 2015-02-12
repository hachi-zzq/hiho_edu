<?php

use HiHo\Model\PlayID;

class PlaylistController extends BaseController
{

    /**
     * 公开笔记列表
     * @return mixed
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getIndex()
    {
        $input = Input::get('order');

        if (empty($input)) {
            $playlists = Playlist::whereRaw("deleted_at IS NULL")->orderBy("created_at", "desc")->paginate(12);
        } elseif ($input == 'hot') {
            $playlists = Playlist::whereRaw("deleted_at IS NULL")->orderBy("liked", "desc")->paginate(12);
        } else {
            $playlists = Playlist::whereRaw("deleted_at IS NULL")->orderBy("viewed", "desc")->paginate(12);
        }

        $playlists = $this->getPlaylistInfo($playlists);

        return View::make('list_index')->with('playlists', $playlists)->with('input', $input);
    }

    /**
     * 添加笔记小窗
     * @return mixed
     */
    public function add()
    {
        $data = array();
        $playlists = Playlist::where('user_id', '=', Auth::user()->user_id)->get();
        foreach ($playlists as $k => $pl) {
            $data['playlists'][$k]['title'] = $pl->title;
            $data['playlists'][$k]['id'] = $pl->id;
        }
        return View::make('list_add')->with('data', $data);
    }

    /**
     *碎片添加到自己的笔记本中
     * @return string
     * @return json
     * @author zhuzhengqian
     */
    public function addInMyPlaylist()
    {
        $playlist_id = Input::get('playlist_id');
        //兼容传playid情况
        if($playid=Input::get('playid')){
            $objPlayId = PlayID::where('play_id',$playid)->where('entity_type','FRAGMENT')->first();
            if(!$objPlayId){
                return json_encode(array("msgCode" => -3, 'message' => 'entity_type not exist'));
            }
            $objPlayFragment = Fragment::find($objPlayId->entity_id);
            if(!$objPlayFragment){
                return json_encode(array("msgCode" => -4, 'message' => 'fragment not exist'));
            }

        }
        $guid = Input::get('guid') ? Input::get('guid') : $objPlayFragment->guid;
        $objFrgmemnt = \Fragment::where('guid', $guid)->first();
        if (!$objFrgmemnt) {
            return json_encode(array('msgCode' => -1, 'message' => 'fragment not exist'));
        }
        $fragmentId = $objFrgmemnt->id;
        $videoId = $objFrgmemnt->video_id;
        $title = Input::get('title');
        $description = Input::get('description');
        //check
        if (\PlaylistFragment::where('fragment_id', $fragmentId)->where('playlist_id', $playlist_id)->first()) {
            return json_encode(array("msgCode" => -2, 'message' => 'fragment exist'));
        }

        $playlistFragment = new PlaylistFragment();
        $playlistFragment->playlist_id = $playlist_id;
        $playlistFragment->fragment_id = $fragmentId;
        $playlistFragment->video_id = $videoId;
        $playlistFragment->title = addslashes(strip_tags($title));
        $playlistFragment->description = addslashes(strip_tags($description));
        $playlistFragment->save();

        //update playlist total number and length
        $newLength = $objFrgmemnt->end_time - $objFrgmemnt->start_time;
        $playlist = Playlist::find($playlist_id);
        $totalLength = $playlist->totel_district;
        $totalLength = $totalLength + $newLength;
        $totalNumber = $playlist->totel_number;
        $totalNumber++;
        Playlist::where('id', '=', $playlist_id)->update(array('totel_district' => $totalLength, 'totel_number' => $totalNumber));

        return json_encode(array("msgCode" => 0, 'message' => 'add success'));
    }

    public function create()
    {
        //set data here
        return View::make('list_create');
    }

    /**
     *新增笔记
     * @return string
     * @author zhuzhengqian
     */
    public function createPost()
    {
        $inputData = Input::only('title', 'description');
        $rules = array(
            'title' => 'required'
        );
        $v = Validator::make($inputData, $rules);
        //check rules
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -2, "message" => $messages->first()));
        }

        $title = addslashes(strip_tags($inputData['title']));
        $description = addslashes(strip_tags($inputData['description']));
        //check exist
        if (Playlist::where('title', $title)->first()) {
            return json_encode(array("status" => -1, "message" => '该笔记已经存在'));
        }

        if (!Auth::guest()) {
            $userID = Auth::user()->user_id;
        }
        $playlist = new Playlist();
        $playlist->user_id = $userID;
        $playlist->title = $title;
        $playlist->description = $description;
        $playlist->save();
        return json_encode(array("status" => 0, "message" => 'create success', 'data' => $playlist));
    }

    public function deletePost()
    {
        $playlist_id = Input::get('playlist_id');
        $input = array('playlist_id' => $playlist_id);
        $rules = array('playlist_id' => 'numeric');
        $v = Validator::make($input, $rules);
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -2, "message" => $messages));
        }

        $playlist = Playlist::find($playlist_id);
        if ($playlist->user_id != Auth::user()->user_id) {
            return json_encode(array("status" => -1, "message" => 'cannot delete'));
        }
        $playId = $playlist->getPlayIdStr();
        $playlist->delete();

        PlaylistFragment::where('playlist_id', $playlist_id)->delete();
        \Favorite::where('user_id', Auth::user()->user_id)->where('play_id', $playId)->delete();
        return json_encode(array("status" => 0, "message" => 'delete success'));
    }

    public function my()
    {
        if (!(Auth::user())) {
            return Redirect::to('/login');
        }

        $playlists = Playlist::whereRaw("user_id = ? AND deleted_at IS NULL", array(Auth::user()->user_id))->orderby('created_at', 'desc')->paginate(12);
        $playlists = $this->getPlaylistInfo($playlists);
        return View::make('list_my')->with('data', $playlists);
    }

    /**
     * 获得碎片集、碎片数、收藏数等
     * @param $playlists
     * @return mixed
     */
    private function getPlaylistInfo($playlists)
    {
        if ($playlists) {
            foreach ($playlists as $playlist) {
                $playlistFragments = PlaylistFragment::where('playlist_id', '=', $playlist->id)->get();
                $fragments = array();
                $videos = array();
                $videoInfos = array();

                foreach ($playlistFragments as $k => $pf) {
                    $fragments[$k] = Fragment::where('id', '=', $pf->fragment_id)->first();
                    if ($fragments[$k]) {
                        // $fragments[$k]->playid = PlayID::isExistWithEntity($fragments[$k])->play_id;
                        $fragments[$k]->playid = $fragments[$k]->getPlayIdStr();
                        $videos[$k] = Video::find($fragments[$k]->video_id);
                        if (empty($videos[$k])) {
                            unset($videos[$k]);
                            unset($fragments[$k]);
                            continue;
                        }
                        $videoInfos[$k] = VideoInfo::whereRaw("video_id = ?", array($fragments[$k]->video_id))->first();
                    } else {
                        continue;
                    }
                }

                // $playlist->playid = PlayID::isExistWithEntity($playlist)->play_id;
                $playlist->playid = $playlist->getPlayIdStr();
                $playlist->fragments = $fragments;
                $playlist->count = count($fragments);
                $playlist->first = reset($fragments);
                $playlist->videos = $videos;
                $playlist->videoInfos = $videoInfos;
                $playlist->favoriteCount = \Favorite::where('play_id', $playlist->playid)->count();
            }
        }
        return $playlists;
    }

    /**
     * 详情页
     * @param $playlist_id
     * @return mixed
     */
    public function detail($playlist_id)
    {
        $playlist = new stdClass();
        $playlist->p = Playlist::where('id', '=', $playlist_id)->first();
        if (empty($playlist->p)) {
            return View::make('no_exist')->with('data', '笔记');
        }
        $playlist->playid = PlayID::isExistWithEntity($playlist->p)->play_id;

        $playlist->playlistFragments = PlaylistFragment::whereRaw("playlist_id = ? order by rank asc", array($playlist_id))->get();
        $playlist->count = count($playlist->playlistFragments);
        $videoLength = 0;
        foreach ($playlist->playlistFragments as $f) {
            $videoLength = 0;
            $f->video = Video::find($f->video_id);
            $f->info = VideoInfo::where('video_id', '=', $f->video_id)->first();
            $f->fragment = $fragmentSingle = Fragment::where('id', '=', $f->fragment_id)->first();
            if ($f->fragment) {
                $f->playid = PlayID::isExistWithEntity($f->fragment)->play_id;
                $videoLength += ($fragmentSingle->end_time - $fragmentSingle->start_time);
            }
            else {
                $f->playid = '';
                $videoLength += 0;
            }
        }
        $playlist->videoLength = $videoLength;
        return View::make('list_detail')->with('playlist', $playlist);
    }

    public function edit($playlist_id)
    {
        $data = array();
        $data['playlist'] = Playlist::where('id', '=', $playlist_id)->get();
        return View::make('list_edit')->with('data', $data);
    }

    public function editPost()
    {
        $input = Input::only('playlist_id', 'title', 'description');
        $rules = array(
            'playlist_id' => 'required',
            'title' => 'required'
        );
        $v = Validator::make($input, $rules);
        //check rules
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -1, "message" => $messages->first()));
        }

        if (!Auth::user()) {
            return json_encode(array("status" => -2, "message" => "not logged in"));
        }

        $user_id = Auth::user()->user_id;
        $playlist_id = $input['playlist_id'];
        $title = addslashes(strip_tags($input['title']));
        $description = addslashes(strip_tags($input['description']));
        $playlist = Playlist::whereRaw("id = ? AND user_id = ?", array($playlist_id, $user_id));
        if (empty($playlist)) {
            return json_encode(array("status" => -3, "message" => "cannot edit current user"));
        }

        Playlist::where('id', $playlist_id)->update(array('title' => $title, 'description' => $description));
        return json_encode(array('status' => 0, 'message' => 'edit success'));
    }

    public function deleteFragmentPost()
    {
        $inputData = Input::only('playlist_id', 'fragment_id');
        $rules = array(
            'playlist_id' => 'required',
            'fragment_id' => 'required'
        );
        $v = Validator::make($inputData, $rules);
        //check rules
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -2, "message" => $messages->first()));
        }

        if (!Auth::user()) {
            return json_encode(array('status' => -1, 'message' => 'not logged in'));
        }

        $playlist_id = $inputData['playlist_id'];
        $fragment_id = $inputData['fragment_id'];
        $user_id = Auth::user()->user_id;
        $playlist = Playlist::whereRaw("id = ? AND user_id = ?", array($playlist_id, $user_id))->first();
        if (empty($playlist)) {
            return json_encode(array('status' => -3, 'message' => 'current user cannot edit'));
        }

        PlaylistFragment::whereRaw("playlist_id = ? AND fragment_id = ?", array($playlist_id, $fragment_id))
            ->delete();

        //update playlist
        $fragment = Fragment::find($fragment_id);
        $fragmentLength = $fragment->end_time - $fragment->start_time;
        $playlist = Playlist::find($playlist_id);
        $totalLength = $playlist->totel_district;
        $totalNumber = $playlist->totel_number;
        $totalLength -= $fragmentLength;
        $totalNumber--;
        Playlist::whereRaw("id = ?", array($playlist_id))->update(array('totel_district' => $totalLength, 'totel_number' => $totalNumber));

        return json_encode(array('status' => 0, 'message' => 'delete success'));
    }

    public function sort()
    {
        $playlist_id = Input::get('playlist_id');
        $fragment_ids = Input::get('ids');
        $fragmentIDsArray = explode(',', $fragment_ids);
        $k = 1;
        foreach ($fragmentIDsArray as $fid) {
            PlaylistFragment::whereRaw("playlist_id = ? AND fragment_id = ?", array($playlist_id, $fid))->update(array('rank' => $k));
            $k++;
        }
        return json_encode(array('status' => 0, 'message' => 'sort success'));
    }

    /**
     * Ajax验证auth
     * @params null
     * @author zhuzhengqian
     */

    /**
     * 获得当前用户的笔记本列表(AJAX)
     * @return bool|string
     * @author Zhengqian
     */
    public function getMyNoteList()
    {
        if (Request::ajax()) {
            if (Auth::guest()) {
                return json_encode(array('msgCode' => -1, 'message' => 'no login'));
            } else {
                //返回我的笔记
                $playlists = \Playlist::where('user_id', Auth::user()->user_id)->get();
                return json_encode(array('msgCode' => 0, 'message' => 'auth pass', 'list' => $playlists->toArray()));
            }
        }
        return false;
    }

    /**
     * 复制笔记到自己
     * @author zhuzhengqian
     */
    public function copyToMine()
    {
        $playlistId = Input::get('playlist_id');
        $objPlaylist = Playlist::find($playlistId);
        if (!$objPlaylist) {
            return json_encode(array('msgCode' => -1, 'message' => '笔记不存在'));
        }

        if (Auth::guest()) {
            return json_encode(array('msgCode' => -2, 'message' => '先登录'));
        }

        $userId = Auth::user()->user_id;
        if (count(Playlist::where('user_id', $userId)->where('title', $objPlaylist->title)->get())) {
            return json_encode(array('msgCode' => -3, 'message' => '你已经添加过该笔记'));
        }

        $newPlaylist = new Playlist();
        $newPlaylist->title = $objPlaylist->title;
        $newPlaylist->description = $objPlaylist->description;
        $newPlaylist->totel_district = $objPlaylist->totel_district;
        $newPlaylist->totel_number = $objPlaylist->totel_number;
        $newPlaylist->user_id = $userId;
        $newPlaylist->save();

        //playlist fragment
        $objPlaylistFragment = PlaylistFragment::where('playlist_id', $playlistId)->get();
        if ($objPlaylistFragment) {
            foreach ($objPlaylistFragment as $pf) {
                $obj = new PlaylistFragment();
                $obj->playlist_id = $newPlaylist->id;
                $obj->video_id = $pf->video_id;
                $obj->fragment_id = $pf->fragment_id;
                $obj->title = $pf->title;
                $obj->description = $pf->description;
                $obj->save();
            }
        }

        return json_encode(array('msgCode' => 0, 'message' => 'add success'));
    }

    /**
     * #返回playlist的中的fragment的播放地址
     * @author zhuzhengqian
     * @return mixed
     */
    public function getFragmentPlayUrl($playid){
        if(!$playid){
            return Response::json(array('msgCode'=>-1,'message'=>'playid is required','data'=>NULL));
        }
        // 判断 PlayID 存在
        $playid = PlayID::isExistWithId($playid); // PLAYID OBJ
        // 判断 $entity 类型
        $entity = $playid->getEntity();
        if (!$entity) {
            return Response::json(array('msgCode'=>-2,'message'=>'entity is not exist','data'=>NULL));
        }
        $objVideo = Video::find($entity->video_id);
        // 生成碎片的播放地址
        $player = new HiHo\Sewise\Player();
        $player->loadVideo($objVideo);
        $player->clip($entity->start_time, $entity->end_time);
        $arrResource = array_values($player->getResource())[0];
        return json_encode(array('msgCode'=>0,'message'=>'success','data'=>$arrResource));
    }


    /**
     * 修改标题
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function editTitlePost()
    {
        $inputData = Input::only('playlist_id', 'title');
        $rules = array(
            'playlist_id' => 'required',
            'title' => 'required'
        );
        $v = Validator::make($inputData, $rules);
        //check rules
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -2, "message" => $messages->first()));
        }

        if (!Auth::user()) {
            return json_encode(array('status' => -1, 'message' => 'not logged in'));
        }

        $user_id = Auth::user()->user_id;
        $playlist = Playlist::whereRaw("id = ? AND user_id = ?", array($inputData['playlist_id'], $user_id))->first();
        if (empty($playlist)) {
            return json_encode(array('status' => -3, 'message' => 'current user cannot edit'));
        }

        Playlist::whereRaw("id = ? AND user_id = ?", array($inputData['playlist_id'], $user_id))->update(array('title' => $inputData['title']));
        return json_encode(array('status' => 0, 'message' => 'edit success'));
    }

    /**
     * 修改列表中段视频的标题
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function editFragmentTitlePost()
    {
        $inputData = Input::only('playlist_id', 'fragment_id', 'title');
        $rules = array(
            'playlist_id' => 'required',
            'fragment_id' => 'required',
            'title' => 'required'
        );
        $v = Validator::make($inputData, $rules);
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -2, "message" => $messages->first()));
        }

        if (!Auth::user()) {
            return json_encode(array('status' => -1, 'message' => 'not logged in'));
        }
        $user_id = Auth::user()->user_id;
        $playlist = Playlist::whereRaw("id = ? AND user_id = ?", array($inputData['playlist_id'], $user_id))->first();
        if (empty($playlist)) {
            return json_encode(array('status' => -3, 'message' => 'current user cannot edit'));
        }

        PlaylistFragment::whereRaw("playlist_id = ? AND fragment_id = ?", array($inputData['playlist_id'], $inputData['fragment_id']))->update(array('title' => $inputData['title']));
        return json_encode(array('status' => 0, 'message' => 'edit success'));
    }

    /**
     * 修改列表中段视频的评论
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function editFragmentCommentPost()
    {
        $inputData = Input::only('playlist_id', 'fragment_id', 'comment');
        $rules = array(
            'playlist_id' => 'required',
            'fragment_id' => 'required',
            'comment' => 'required'
        );
        $v = Validator::make($inputData, $rules);
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array("status" => -2, "message" => $messages->first()));
        }

        if (!Auth::user()) {
            return json_encode(array('status' => -1, 'message' => 'not logged in'));
        }

        $user_id = Auth::user()->user_id;
        $playlist = Playlist::whereRaw("id = ? AND user_id = ?", array($inputData['playlist_id'], $user_id))->first();
        if (empty($playlist)) {
            return json_encode(array('status' => -3, 'message' => 'current user cannot edit'));
        }

        PlaylistFragment::whereRaw("playlist_id = ? AND fragment_id = ?", array($inputData['playlist_id'], $inputData['fragment_id']))->update(array('comment' => $inputData['comment']));
        return json_encode(array('status' => 0, 'message' => 'edit success'));
    }

}