<?php

use HiHo\Model\Favorite;

class FavoriteController extends BaseController
{

    /**
     * 视频添加到收藏
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function addPost()
    {
        $playid = Input::get('video_id');
        //判断登录 by zhuzhengqian
        if (Auth::guest()) {
            return json_encode(array('status' => -2, 'message' => 'login first'));
            exit;
        }
        $user_id = Auth::user()->user_id;

        $playIDModel = PlayID::where('play_id', $playid)->first();
        if (!$playIDModel) {
            return json_encode(array('status' => -3, 'message' => 'no such video exists'));
        }

        $hasFavorite = Favorite::where("user_id", $user_id)->where('play_id', $playIDModel->play_id)->first();
        if ($hasFavorite) {
            return json_encode(array('status' => -1, 'message' => 'has been added'));
        }

        $favorite = new Favorite();
        $favorite->user_id = $user_id;
        $favorite->play_id = $playIDModel->play_id;
        $favorite->save();

        return json_encode(array('status' => 0, 'message' => 'add success'));
    }

    /**
     * @return string
     */
    public function addFragmentPost()
    {
        $playid = Input::get('fragment_playid');
        if (!$playid) {
            return json_encode(array("msgCode" => -5, 'message' => 'play id not exist'));
        }
        $objPlayId = PlayID::where('play_id', $playid)->where('entity_type', 'FRAGMENT')->first();
        if (!$objPlayId) {
            return json_encode(array("msgCode" => -3, 'message' => 'entity_type not exist'));
        }
        $objPlayFragment = Fragment::find($objPlayId->entity_id);
        if (!$objPlayFragment) {
            return json_encode(array("msgCode" => -4, 'message' => 'fragment not exist'));
        }
        if (Auth::guest()) {
            return json_encode(array('msgCode' => -2, 'message' => 'login first'));
            exit;
        }
        $user_id = Auth::user()->user_id;
        $hasFavorite = Favorite::where("user_id", $user_id)->where('play_id', $objPlayId->play_id)->first();
        if ($hasFavorite) {
            return json_encode(array('msgCode' => -1, 'message' => 'has been added'));
        }
        $favorite = new Favorite();
        $favorite->user_id = $user_id;
        $favorite->play_id = $objPlayId->play_id;
        $favorite->save();

        return json_encode(array('msgCode' => 0, 'message' => 'add success'));
    }

    /**
     *收藏整个笔记
     * @author zhuzhengqian
     */
    public function addPlaylist()
    {
        $playid = Input::get('play_id');
        if (Auth::guest()) {
            return json_encode(array('status' => -1, 'message' => 'login first'));
        }

        $playIDModel = PlayID::where('play_id', $playid)->first();
        if (!$playIDModel || $playIDModel->entity_type != "PLAYLIST") {
            return json_encode(array('status' => -2, 'message' => 'no playlist exists'));
        }

        $user_id = Auth::user()->user_id;
        $hasFavorite = Favorite::where("user_id", $user_id)->where('play_id', $playid)->first();
        if ($hasFavorite) {
            return json_encode(array('status' => -3, 'message' => 'has been added'));
        }

        $objFavorite = new Favorite();
        $objFavorite->user_id = $user_id;
        $objFavorite->play_id = $playid;
        $objFavorite->save();

        return json_encode(array('status' => 0, 'message' => 'add success'));
    }

    /** 
     * 我收藏的视频(课程)
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function videos()
    {
        $input = Input::get("favorites");
        $favorites = DB::table('playid')
            ->join('favorites', 'playid.play_id', '=', 'favorites.play_id')
            ->where('favorites.user_id', Auth::user()->user_id)
            ->where('favorites.deleted_at', null)
            ->where('playid.entity_type', 'VIDEO')
            ->paginate(12);
        foreach ($favorites as $favorite) {
            $favorite->v = \Video::where('video_id', $favorite->entity_id)->first();
            $favorite->count = Favorite::where('play_id', $favorite->play_id)->count();
            $favorite->videoInfo = VideoInfo::where("video_id", "=", $favorite->entity_id)->first();
            if (!$favorite->v) {
                continue;
            } else {
                $favorite->playid = $favorite->v->getPlayIdStr();
                $favorite->info = VideoInfo::where("video_id", $favorite->entity_id)->first();
                if (!$favorite->info) {
                    $info = new \stdClass();
                    $info->title = "title not found";
                    $favorite->info = $info;
                }
                $favorite->pic = VideoPicture::where("video_id",$favorite->entity_id)->first();
                if (!$favorite->pic) {
                    $pic = new \stdClass();
                    $pic->src = "/static/img/video_default.png";
                    $favorite->pic = $pic;
                }

            }

            $teacher = TeacherVideo::where('video_id', '=', $favorite->entity_id)->first();
            if ($teacher) {
                $favorite->teacher = $teacherInfo = Teacher::where('id', '=', $teacher->teacher_id)->first();
                $department = DepartmentTeacher::where('teacher_id', '=', $teacherInfo->id)->first();
                if ($department) {
                    $favorite->department = Department::where('id', '=', $department->department_id)->first();
                } else {
                    $favorite->department = array();
                }
            } else {
                $favorite->teacher = array();
                $favorite->department = array();
            }
        }

        return View::make('favorite_my')->with('favorites', $favorites)->with('input', $input);
    }

    /**
     * 我收藏的笔记
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function playlists()
    {

        $favorites = DB::table('playid')
            ->join('favorites', 'playid.play_id', '=', 'favorites.play_id')
            ->where('favorites.user_id', Auth::user()->user_id)
            ->where('favorites.deleted_at', null)
            ->where('playid.entity_type', 'PLAYLIST')
            ->paginate(12);
        foreach ($favorites as $favorite) {
            $favorite->playlist = $playlist = Playlist::where('id', '=', $favorite->entity_id)->first();
            $favorite->listCount = PlaylistFragment::where('playlist_id', '=', $playlist->id)->count();
            $favorite->length = $playlist->totel_district;
            $favorite->viewed = $playlist->viewed;
            $playlistFragments = PlaylistFragment::where('playlist_id', '=', $playlist->id)->get();
            foreach ($playlistFragments as $k => $pfs) {
                $favorite->fragment = Fragment::where('id', '=', $pfs->fragment_id)->first();
                if ($favorite->fragment) {
                    $favorite->video = Video::find($favorite->fragment->video_id);
                    if (empty($favorite->video)) {
                        unset($favorite->video);
                        unset($favorite->fragment);
                        continue;
                    }
                } else {
                    continue;
                }
            }
            $favorite->count = Favorite::where('play_id', $favorite->play_id)->count();

        }
        return View::make('favorite_playlists')->with('favorites', $favorites);
    }

    /**
     * 我收藏的剪辑
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function clips()
    {
        $favorites = DB::table('playid')
            ->join('favorites', 'playid.play_id', '=', 'favorites.play_id')
            ->where('favorites.user_id', Auth::user()->user_id)
            ->where('favorites.deleted_at', null)
            ->where('playid.entity_type', 'FRAGMENT')
            ->paginate(12);
        foreach ($favorites as $favorite) {
            $fragment = Fragment::where('id', '=', $favorite->entity_id)->first();
            if ($fragment) {
                $favorite->fragment = $fragment;
                $favorite->playid = $favorite->fragment->getPlayIdStr();
            }else{
                $favorite->fragment = new Fragment();
            }
            $favorite->videoInfo = VideoInfo::where('video_id', '=', $fragment->video_id)->first();

        }
        return View::make('favorite_clips')->with('favorites', $favorites);
    }

    public function share()
    {

        if (!(Auth::user())) {
            return Redirect::to('/');
        }

        $userID = Auth::user()->user_id;
        $favoriteShares = FragmentShare::whereRaw("user_id = ? AND deleted_at IS NULL", array($userID))->paginate(12);

        foreach ($favoriteShares as $fs) {
            $fs->fragment = Fragment::find($fs->fragment_id);
            $fs->info = VideoInfo::where('video_id', '=', $fs->fragment->video_id)->first();
        }

        return View::make('myshare')->with('data', $favoriteShares);
    }

    /**
     * 删除收藏
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    public function deletePost()
    {
        $input = Input::only('playid');
        $rules = array(
            'playid' => array('required'),
        );
        $v = Validator::make($input, $rules);
        if ($v->fails()) {
            $messages = $v->messages();
            return json_encode(array('status' => -1, 'message' => $messages->first()));
        }
        if (!Auth::user()) {
            return json_encode(array('status' => -2, 'message' => 'need login'));
        }
        $user_id = Auth::user()->user_id;

        $playIdModel = PlayID::where('play_id', $input['playid'])->first();
        if (!$playIdModel) {
            return json_encode(array('status' => -3, 'message' => 'no such entity exists'));
        }
        $favorite = Favorite::where("user_id", $user_id)->where("play_id", $input['playid'])->first();
        if (!$favorite) {
            return json_encode(array('status' => -4, 'message' => 'current user cannot operate'));
        }
        $favorite->delete();
        return json_encode(array('status' => 0, 'message' => 'success'));
    }
}
