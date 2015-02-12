<?php namespace HiHo\Edu\Controller\Rest;

use \Playlist;
use \PlaylistFragment;

class PlaylistController extends BaseController
{

    /**
     * 笔记列表
     * @return string
     * @author zhuzhengqian
     */
    public function getIndex()
    {
        $inputData = \Input::only('token', 'type', 'page', 'limit', 'since_id');
        $sinceId = $inputData['since_id'] ? $inputData['since_id'] : 0;
        $limit = $inputData['limit'] ? $inputData['limit'] : parent::PAGE_SIZE;
        $where = '';
        if ($inputData['token']) {
            // 验证 Token 身份,返回Userid
            $userId = $this->verifyToken($inputData['token']);
            if (!$userId) {
                return $this->encodeResult('20100', 'Token validation fails.');
            }
            $where .= ' and user_id= ' . $userId;
        }
        if ($inputData['type']) {
            $where .= ' and type = ' . $inputData['type'];
        }

        $objPlaylists = \Playlist::whereRaw("id > $sinceId $where")->paginate($limit);

        // 采集playlist其他信息一起返回
        foreach ($objPlaylists as &$playlist) {
            // TODO: 可能使用同前台一样的 getPlaylistInfo 合并到 Model

            // 获取封面，默认返回里面碎片的封面
            $objPlaylistjFragment = \PlaylistFragment::where('playlist_id', $playlist->id)->get();
            if ($objPlaylistjFragment) {
                $arr = array();
                if ($objPlaylistjFragment) {
                    foreach ($objPlaylistjFragment as $playlistFragment) {
                        $objFragment = \Fragment::find($playlistFragment->fragment_id);
                        if ($objFragment) {
                            array_push($arr, $objFragment->cover);
                        }
                    }
                }
                $playlist->playid = $playlist->getPlayIdStr();
                $playlist->pictures = $arr;
            }
        }

        return $this->encodeResult('12500', 'succeed', $objPlaylists->toArray());
    }

    /**
     * 返回笔记中视频或者片段的列表
     * @return string
     * @author zhuzhengqian
     */
    public function getFragments()
    {
        $inputData = \Input::only('playlist_id');
        $rules = array(
            'playlist_id' => 'required'
        );
        $v = \Validator::make($inputData, $rules);
        ##check params
        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        $objList = \PlaylistFragment::whereRaw("playlist_id = {$inputData['playlist_id']}")->get();
        $arrId = array();
        if ($objList) {
            foreach ($objList as $list) {
                array_push($arrId, $list->fragment_id);
            }
        }

        if ($arrId) {
            $ids = implode(',', $arrId);
        } else {
            $ids = -1;
        }

        // TODO: 该方法返回数据结构不标准, 可能需要重构

        $std = new \stdClass();
        $fragments = \Fragment::whereRaw("id in ($ids) and deleted_at is null")->get();
        foreach ($fragments as $key => &$f) {
            $f->playid = $f->getPlayIdStr();
            $f->info = \VideoInfo::where('video_id', $f->video_id)->get();
            $f->subtitle = 'TODO: subtitle content';
        }
        $std->data = $fragments;


        return $this->encodeResult('12501', 'succeed', $std);
    }


    /**
     * 取消收藏
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getDestroy()
    {
        return $this->postDestroy();
    }

    /**
     * 删除笔记
     * @author zhuzhengqian
     */
    public function postDestroy()
    {
        // TODO: 增加 PlayID

        $inputData = \Input::only('token', 'playlist_id');
        $rules = array(
            'token' => 'required',
            'playlist_id' => 'required'
        );
        $v = \Validator::make($inputData, $rules);
        ##check params
        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份,返回Userid
        $userId = $this->verifyToken($inputData['token']);
        if (!$userId) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }

        //check exist
        if (!$objPlaylist = Playlist::find($inputData['playlist_id'])) {
            return $this->encodeResult('22501', 'the playlist is not exist', null);
        }
        //check privlieges
        if ($objPlaylist->user_id != $userId) {
            return $this->encodeResult('22502', 'DENY ACCESS', null);
        }

        $objPlaylist->delete();

        return $this->encodeResult('12502', 'success', null);
    }


}
