<?php

use HiHo\Model\PlayID;
use HiHo\Model\Video;
use HiHo\Model\Fragment;
use HiHo\Sewise\Player;

/**
 * 基于 PlayID 的通用播放控制器
 *
 * @see HiHo\Model\PlayID;
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class PlayController extends BaseController
{
    const ACCESS_CATEGORY = 'C';
    const ACCESS_VIDEO = 'V';
    const ACCESS_PASS = 'P';

    /**
     * PlayID 的路由转换器
     * @param null $playid
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function playAnything($playid = NULL)
    {
        // 判断 PlayID 存在
        $playid = PlayID::isExistWithId($playid); // PLAYID OBJ

        // 获得该 PlayID 对应的实体
        if (!$playid) {
            App::abort(404, 'PlayID is not exist');
            return;
        }
        // 判断 $entity 类型
        $entity = $playid->getEntity();
        if (!$entity) {
            App::abort(404, 'Entity of PlayID is not exist');
            return;
        }
        // 判断 $entity 类型, 路由到相应方法
        if ($entity instanceof Video) {
            // 处理碎片 ST 和 ET 传入的情况
            if (Input::get("type") == 'video' and Input::get("st") and Input::get("et")) {
                return $this->getGoToFragment($entity, Input::get("st"), Input::get("et"), Input::get("user_id"));
            } else {
                return $this->playVideo($playid, $entity);
            }
        } else if ($entity instanceof Fragment) {
            return $this->playFragment($playid, $entity);
        } else if ($entity instanceof Playlist) {

            return $this->playNote($playid, $entity);
        } else {
            App::abort(403, 'Entity type is ERROR!');
            return;
        }
    }

    /**
     * 播放视频
     *
     * 视频对象的播放支持传递 st 和 et 生成新碎片, 随后跳转到新 PlayID, 例如:
     * http://hiho.com/play/AbCdEfGh?type=video&st=xxx&et=xxx (生成碎片)
     *
     * TODO: 结构化课程的上线支持简单模式和学习模式, 例如:
     * http://hiho.com/play/AbCdEfGh?type=video&mode=simple
     * http://hiho.com/play/AbCdEfGh?type=video&mode=learning
     *
     * @param $playid
     * @param $video
     * @return mixed
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    protected function playVideo($playid, $objVideo)
    {
        $objVideoInfo = $objVideo->info()->first();
        $accessChecked = $this->checkAccess($objVideo);
        if (!$accessChecked[self::ACCESS_PASS]) {
            return View::make('no_access');
        }

        // 增加视频播放次数
        $objVideo->viewed = $objVideo->viewed + 1;
        $objVideo->save();

        // TODO: 修改所有涉及到的 API

        // TODO: 区分 PC 端、iOS 和 Android 提供不同的播放 View
        if (!$playid or !$objVideo) {
            return View::make('no_exist')->with('data', '视频');
        }

        // 播放模式
        $playmode = 'SIMPLE';
        $playmode = 'LEARNING';

        $objVideo->playid = $objVideo->getPlayIdStr();
        $objVideo->favorites = Favorite::where("play_id", $objVideo->playid)->count();

        // 获得分类信息(多)
        $arrCategories = $objVideo->categories()->get();

        // 获得专业信息(多)
        $arrSpecialities = $objVideo->specialities()->get();

        // 获取主讲老师信息
        $tv = TeacherVideo::where('video_id', '=', $objVideo->video_id)->first();
        if (!empty($tv)) {
            $objMainSpeaker = Teacher::where('id', '=', $tv->teacher_id)->first();
        } else {
            $objMainSpeaker = null;
        }
        // 播放地址
        $arrResource = array(
            'flv' => VideoResource::whereRaw("video_id = ? and type = ? AND deleted_at IS NULL", array($objVideo->video_id, 'flv'))->first(),
            'mp4' => VideoResource::whereRaw("video_id = ? and type = ? AND deleted_at IS NULL", array($objVideo->video_id, 'mp4'))->first(),
            'm3u8' => VideoResource::whereRaw("video_id = ? and type = ? AND deleted_at IS NULL", array($objVideo->video_id, 'm3u8'))->first(),
        );
        // 当前用户收藏状态
        if (Auth::user()) {
            $favorited = Favorite::where('play_id', $objVideo->playid)->where('user_id', Auth::user()->user_id)->first();
        } else {
            $favorited = false;
        }

        // 附件
        $arrAttachments = VideoAttachment::where('video_id', '=', $objVideo->video_id)->get();

        return View::make('play_video')
            ->with('playmode', $playmode)
            ->with('objVideo', $objVideo)
            ->with('objVideoInfo', $objVideoInfo)
            ->with('objMainSpeaker', $objMainSpeaker)
            ->with('arrCategories', $arrCategories)
            ->with('arrSpecialities', $arrSpecialities)
            ->with('arrAttachments', $arrAttachments)
            ->with('arrResource', $arrResource)
            ->with('favorited', $favorited)
            ->with('accessChecked', $accessChecked);
    }

    /**
     * 播放碎片
     *
     * TODO: 具有注释性质的 type 参数, 例如:
     * http://hiho.com/play/AbCdEfGh?type=fragment
     *
     * @param $playid
     * @param $objFragment
     * @return mixed
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    protected function playFragment($playid, $objFragment)
    {
        $objVideo = $objFragment->video()->first();
        $objVideo->playid = $objVideo->getPlayIdStr();
        $objVideo->favorites = Favorite::where("play_id", $objVideo->playid)->count();
        $objVideoInfo = $objVideo->info()->first();
        // 增加碎片播放次数
        $objFragment->viewed = $objFragment->viewed + 1;
        $objFragment->save();

        // TODO: 区分 PC 端、iOS 和 Android 提供不同的播放 View
        if (!$playid or !$objFragment or !$objFragment->video()->first()) {
            App::abort(404, 'PlayID or Fragment is not found');
            return;
        }

        // 修正时间, 是否有必要性?
        $objFragment->start_time = round($objFragment->start_time, 3);
        $objFragment->end_time = round($objFragment->end_time, 3);
        $objFragment->playid = $objFragment->getPlayIdStr();

        // 播放模式
        $playmode = 'SIMPLE';
        // 获得分类信息(多)
        $arrCategories = $objVideo->categories()->get();

        // 获得专业信息(多)
        $arrSpecialities = $objVideo->specialities()->get();

        // 获取主讲老师信息
        $tv = TeacherVideo::where('video_id', '=', $objVideo->video_id)->first();
        if (!empty($tv)) {
            $objMainSpeaker = Teacher::where('id', '=', $tv->teacher_id)->first();
        } else {
            $objMainSpeaker = null;
        }

        // 已收藏数
        $objFragment->favourites = Favorite::where("play_id", $objFragment->playid)->count();

        // 设置默认碎片标题
        if (empty($objFragment->title)) {
            $objFragment->title = $objVideoInfo->title;
        }

        // 设置默认碎片作者 -> VIEW
        $objFragmentCreator = User::find($objVideoInfo->user_id);

        // 生成碎片的播放地址
        $player = new Player();
        $player->loadVideo($objVideo);
        $player->clip($objFragment->start_time, $objFragment->end_time);
        $arrResource = array_values($player->getResource())[0];

        // 当前用户收藏状态
        if (Auth::user()) {
            $favorited = Favorite::where('play_id', $objFragment->playid)->where('user_id', Auth::user()->user_id)->first();
        } else {
            $favorited = false;
        }

        return View::make("play_fragment")
            ->with('playmode', $playmode)
            ->with('objFragment', $objFragment)
            ->with('objVideo', $objVideo)
            ->with('objVideoInfo', $objVideoInfo)
            ->with('objMainSpeaker', $objMainSpeaker)
            ->with('objFragmentCreator', $objFragmentCreator)
            ->with('arrResource', $arrResource)
            ->with('arrCategories', $arrCategories)
            ->with('arrSpecialities', $arrSpecialities)
            ->with('favorited', $favorited);
    }

    /**
     * 播放笔记(播单)
     *
     * TODO: 笔记应支持从某个子碎片播放, 例如:
     * http://hiho.com/play/AbCdEfGh?type=note&start_by=AbCdEfGh
     *
     * @param $playid
     * @param $playlist
     * @return mixed
     * @author Luyu<luyu.zhang@autotiming.com> Zhengqian<zhengqian.zhu@autotiming.com>
     */
    protected function playNote($playid, $objPlaylist)
    {
        // 增加笔记播放次数
        $objPlaylist->viewed = $objPlaylist->viewed + 1;
        $objPlaylist->save();

        // TODO: 区分 PC 端、iOS 和 Android 提供不同的播放 View
        if (!$objPlaylist) {
            App::abort(404, 'playlist is not exist');
        }

        // 播放模式
        $playmode = 'SIMPLE';

        $objPlaylist->playid = $objPlaylist->getPlayIdStr();
        $objPlaylistFragments = $objPlaylist->fragments();

        foreach ($objPlaylistFragments as $k => &$objPf) {
            $objFragment = $objPf->fragment()->first();
            if (!$objFragment) {
                unset($objPlaylistFragments[$k]);
                continue;
            }

            $objVideo = $objFragment->video()->first();
            if (!$objVideo) {
                unset($objPlaylistFragments[$k]);
                continue;
            }

            $objVideo->playid = $objVideo->getPlayIdStr();

            // 生成碎片的播放地址
            $player = new Player();
            $player->loadVideo($objVideo);
            $player->clip($objFragment->start_time, $objFragment->end_time);
            $objPf->resource = array_values($player->getResource())[0];

            $objPf->playid = $objFragment->getPlayIdStr();
            $objPf->length = $objFragment->end_time - $objFragment->start_time;
            $objPf->cover = $objFragment->cover;

            $objPf->objFragment = $objFragment;
            $objPf->objVideo = $objVideo;
            $objPf->sort = $k + 1;
        }

        if (Request::ajax()) {
            return json_encode($objPlaylistFragments);
        }

        // 当前用户收藏状态
        if (Auth::user()) {
            $favorited = Favorite::where('play_id', $objPlaylist->playid)->where('user_id', Auth::user()->user_id)->first();
        } else {
            $favorited = false;
        }

        return View::make('play_note')
            ->with('playmode', $playmode)
            ->with('objPlaylist', $objPlaylist)
            ->with('objPlaylistFragments', $objPlaylistFragments)
            ->with('favorited', $favorited);

    }

    /**
     * 生成新碎片跳转
     *
     * @return string
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    private function getGoToFragment(Video $video, $st, $et, $user_id)
    {
        // 检查必填参数
        if (!isset($st) || !isset($et)) {
            return json_encode(array('status' => '-1', 'message' => '必填参数 ST 或 ET 没有传递.'));
        }

        if ($st > $et) {
            return json_encode(array('status' => '-2', 'message' => '时间非法, ST 不能大于 ET!'));
        }

        // 检查 UserID 合法性
        if ($user_id) {
            try {
                $user = User::findOrFail($user_id);
            } catch (ModelNotFoundException $e) {
                $user = NULL;
            }
        } else {
            $user = Auth::check() ? Auth::user() : NULL;
        }

        $fragment = Fragment::getFragmentByStAndEt($video, $st, $et, $user);
        $fragment_playid = $fragment->getPlayIdStr();

        // GO TO PLAY
        return Redirect::action('PlayController@playAnything', array($fragment_playid));
    }

    // TODO: REVIEW


    /**
     * 下载附件,并统计次数
     * @param $id
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function downloadAttachment($id)
    {
        $att = VideoAttachment::find($id);
        if (empty($att) or !file_exists(public_path() . $att->path)) {
            echo "附件不存在";
            exit;
        } else {
            $filePath = public_path() . $att->path;
            $att->downloaded++;
            $att->save();
            return \Response::download($filePath, $att->title);
        }
    }

    /**
     * @param Video $video
     * @return array
     * @author Haiming<haiming.wang@autotiming.com>
     */
    private function checkAccess(Video $video)
    {
        $accessChecked = array(self::ACCESS_PASS => false, self::ACCESS_CATEGORY => false, self::ACCESS_VIDEO => false);
        if (!$video) {
            return $accessChecked;
        }
        if ($video->checkCategoryAccess(Auth::user())) {
            $accessChecked[self::ACCESS_CATEGORY] = true;
        }
        if ($video->checkVideoAccess(Auth::user())) {
            $accessChecked[self::ACCESS_VIDEO] = true;
        }
        $accessChecked[self::ACCESS_PASS] = ($accessChecked[self::ACCESS_CATEGORY] and $accessChecked[self::ACCESS_VIDEO]);
        return $accessChecked;
    }
}
