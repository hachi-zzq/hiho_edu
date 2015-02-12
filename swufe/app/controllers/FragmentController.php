<?php

use \Mobile_Detect;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use HiHo\Model\User;
use HiHo\Model\PlayID;
use HiHo\Model\Video;
use HiHo\Model\Fragment;

/**
 * 碎片
 * @package hiho.com
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class FragmentController extends BaseController
{

    /**
     * 段视频列表
     * @return mixed
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function index()
    {
        $input = Input::get("order");

        if (empty($input)) {
            $fragments = Fragment::whereRaw("cover != ? AND deleted_at IS NULL", array(""))->orderBy("created_at", "desc")->paginate(20);
        } elseif ($input == "hot") {
            $fragments = Fragment::whereRaw("cover != ? AND deleted_at IS NULL", array(""))->orderBy("viewed", "desc")->paginate(20);
        } else {
            $fragments = Fragment::whereRaw("cover != ? AND deleted_at IS NULL", array(""))->orderBy("liked", "desc")->paginate(20);
        }

        // 附加VideoInfo,SubTitle
        foreach ($fragments as $item) {
            $fragment = Fragment::find($item->id);
            $item->extend = VideoInfo::where('video_id', $item->video_id)->first();

            if (!$item->extend) {
                $item->extend = new stdClass();
                $item->extend->title = "title not found";
                $item->extend->video_id = $item->video_id;
            }

            if (!empty($fragment->user_id)) {
                $item->userinfo = User::find($fragment->user_id);
            } else {
                $user = new stdClass();
                $user->nickname = "anonymous";
                $item->userinfo = $user;

            }

            $item->info = $fragment;
            $item->playid = $fragment->getPlayIdStr();
            $item->video = Video::find($item->video_id);
            $item->videoInfo = VideoInfo::where('video_id', '=', $item->video_id)->first();
            $item->favoriteCount = Favorite::where('play_id', '=', $item->playid)->count();

        }

        return View::make('fragment_index')->with("fragments", $fragments)->with("input", $input);
    }

    /**
     * Ajax 创建分享链接, 返回新碎片的 PlayID
     * @return string
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getCreateWithPlayId()
    {
        $playid = Input::get('playid');
        $st = Input::get('st');
        $et = Input::get('et');
        $user_id = Input::get('user_id');

        // 检查必填参数
        if (!isset($st) || !isset($et) || !isset($playid)) {
            return json_encode(array('status' => '-1', 'message' => '请传递必要的参数: playid, st, et, user_id'));
        }

        // 判断 PlayID 存在
        $playid = PlayID::isExistWithId($playid); // PLAYID OBJ

        // 获得该 PlayID 对应的实体
        if (!$playid) {
            return json_encode(array('status' => '-2', 'message' => 'playid 不存在'));
        }

        // 判断 $entity 类型
        $entity = $playid->getEntity();
        if (!$entity) {
            return json_encode(array('status' => '-3', 'message' => 'playid 对应的播放资源不存在。'));
        }

        // 判断 $entity 类型, 路由到相应方法
        if ($entity instanceof Video) {
            $objVideo = $entity;

            // 检查 UserID 合法性
            if ($user_id) {
                try {
                    $user = Video::findOrFail($user_id);
                } catch (ModelNotFoundException $e) {
                    return json_encode(array('status' => '-5', 'message' => '用户 ID 不存在。'));
                }
            } else {
                $user = Auth::check() ? Auth::user() : NULL;
            }

            $objFragment = Fragment::getFragmentByStAndEt($objVideo, $st, $et, $user);

            return json_encode(array(
                'status' => '1',
                'message' => 'success',
                'fragment_guid' => $objFragment->guid,
                'fragment_playid' => $objFragment->getPlayIdStr()
            ));
        } else if ($entity instanceof Fragment) {
            return json_encode(array('status' => '-4', 'message' => 'playid 必须对应一个完整视频。'));
        } else if ($entity instanceof Playlist) {
            return json_encode(array('status' => '-4', 'message' => 'playid 必须对应一个完整视频。'));

        } else {
            return json_encode(array('status' => '-4', 'message' => 'playid 必须对应一个完整视频。'));
        }
    }


}