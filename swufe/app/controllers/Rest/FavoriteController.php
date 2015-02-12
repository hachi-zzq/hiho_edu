<?php namespace HiHo\Edu\Controller\Rest;

use \Favorite;
use HiHo\Model\Fragment;
use \Input;
use \URL;
use \Validator;
use \DB;
use \Auth;
use \VideoInfo;
use \Playlist;
use \PlaylistFavorite;

/**
 * RestAPI 收藏管理
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class FavoriteController extends BaseController
{

    /**
     * 我的收藏
     * @author Luyu<luyu.zhang@autotiming.com> zhuzhengqian <zhengqian zhu@autotiming.com>
     */
    public function getVideoindex()
    {
        // 表单验证规则
        $input = \Input::only('token','page','since_id','limit','show_keyframes');
        $since_id = $input['since_id'] ? $input['since_id'] : 0;
        $limit = $input['limit'] ? $input['limit'] : 15;
        $show_keyframes = $input['show_keyframes']=='false' ? 0 : 1;
        $rules = array(
            'token' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $v = $this->verifyToken($input['token']);
        if (!$v) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }

        $objFavorete = DB::table('playid')
            ->join('favorites', 'playid.play_id', '=', 'favorites.play_id')
            ->where('favorites.user_id',$v)
            ->where('favorites.deleted_at', null)
            ->where('playid.entity_type', 'VIDEO')->get();
        $arrId = array();
        if($objFavorete){
            foreach($objFavorete as $favorite){
                array_push($arrId,$favorite->video_id);
            }
        }

        if($arrId) $ids = implode(',',$arrId); else $ids = -1;
        $videos = \Video::whereRaw("video_id > $since_id and video_id in ($ids) and deleted_at IS NULL")->paginate($limit);
        if($videos){
            foreach($videos as &$video){
                $video_info = VideoInfo::where("video_id", $video->video_id)->get();
                $arrInfo = array();
                foreach($video_info as $info){
                    $stdClass = new \stdClass();
                    $stdClass->title = $info->title;
                    $stdClass->description = $info->description;
                    $stdClass->is_original = $info->is_original;
                    $stdClass->language = $info->language;
                    //分类
                    $objVideoCategory = \VideoCategory::where('video_id',$info->video_id)->first();
                    $stdClass->category = '';
                    if($objVideoCategory){
                        $objCategory = \Category::find($objVideoCategory->category_id);
                        if($objCategory){
                            $stdClass->category = $objCategory->name;
                        }
                    }

                    $teacher = \TeacherVideo::where('video_id',$info->video_id)->first();
                    $stdClass->teacher = '';
                    $stdClass->department = '';
                    $stdClass->parent_department = new \stdClass();
                    if($teacher){
                        $stdClass->teacher = \Teacher::find($teacher->teacher_id)->name;
                        $department = \DepartmentTeacher::where("teacher_id",$teacher->teacher_id)->first();
                        if($department){
                            $stdClass->department = \Department::find($department->department_id)->first();
                            if($stdClass->department->parent != 0){
                                $stdClass->parent_department =  \Department::where('id',$stdClass->department->parent)->first();
                            }
                        }
                    }
                    array_push($arrInfo,$stdClass);
                }
                $video->info = $arrInfo;

                ##pictures
                $video->pictures = \VideoPicture::where('video_id',$video->video_id)->get();
                if( ! $show_keyframes){
                    unset($video->keyframes);
                }
            }
        }

        return $this->encodeResult('10401', 'Succeed', $videos->toArray());
    }


    /**
     *碎片收藏列表
     * @atuhor zhuzhengqian
     */
    public function getFragmentindex(){
        $input = \Input::only('token','page','since_id','limit');
        $since_id = $input['since_id'] ? $input['since_id'] : 0;
        $limit = $input['limit'] ? $input['limit'] : 15;
        $rules = array(
            'token' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $v = $this->verifyToken($input['token']);
        if (!$v) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }
        $objFavorete = DB::table('playid')
            ->join('favorites', 'playid.play_id', '=', 'favorites.play_id')
            ->where('favorites.id','>', $since_id)
            ->where('favorites.user_id', $v)
            ->where('favorites.deleted_at', null)
            ->where('playid.entity_type', 'FRAGMENT')->get();
        $arrId = array();
        if($objFavorete){
            foreach($objFavorete as $favorite){
                array_push($arrId,$favorite->entity_id);
            }
        }
        if($arrId){
            $ids = implode(',',$arrId);
        }else{
            $ids = -1;
        }
        $fragments = \Fragment::whereRaw("id in ($ids) and deleted_at is null")->paginate($limit);
        foreach ($fragments as $key => &$f) {
            $f->info = \VideoInfo::with('video')->where('video_id',$f->video_id)->get();
            $f->subtitle = 'TODO: subtitle content';
            unset($f);
        }

        $arrResponse = $fragments->toArray();
        $responseData = $arrResponse['data'];
        $arrTmp = array();
        if($responseData){
            foreach($responseData as $f){
                if(count($f['info'])>0){
                    array_push($arrTmp,$f);
                }
            }
        }

        $arrResponse['data'] = $arrTmp;
        return $this->encodeResult('10408','success',$arrResponse);

    }


    /**
     *收藏的笔记列表
     * @author zhuzhengqian
     */
    public function getPlaylistindex(){
        $input = \Input::only('token','page','since_id','limit');
        $since_id = $input['since_id'] ? $input['since_id'] : 0;
        $limit = $input['limit'] ? $input['limit'] : 15;
        $rules = array(
            'token' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $v = $this->verifyToken($input['token']);
        if (!$v) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }
        $objPlaylist = DB::table('playid')
            ->join('favorites', 'playid.play_id', '=', 'favorites.play_id')
            ->where('favorites.user_id', $v)
            ->where('favorites.deleted_at', null)
            ->where('playid.entity_type', 'PLAYLIST')->get();
        $arrId = array();
        if($objPlaylist){
            foreach($objPlaylist as $playlist){
                array_push($arrId,$playlist->entity_id);
            }
        }
        if($arrId){
            $ids = implode(',',$arrId);
        }else{
            $ids = -1;
        }

        $objPlaylist = \Playlist::whereRaw("id in ($ids)")->paginate($limit);
        //采集playlist其他信息一起返回
        if($objPlaylist){
            foreach($objPlaylist as $playlist){
                //获取封面，默认返回里面碎片的封面
                $objPlaylistjFragment = \PlaylistFragment::where('playlist_id',$playlist->id)->get();
                if($objPlaylistjFragment){
                    $arr = array();
                    foreach($objPlaylistjFragment as $playlistFragment){
                        $objFragment = \Fragment::find($playlistFragment->fragment_id);
                        array_push($arr,$objFragment->cover);
                    }
                    $playlist->pictures = $arr;
                }
            }
        }

        echo $this->encodeResult('10409','success',$objPlaylist->toArray());
    }

    /**
     * 新增收藏
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getVideocreate()
    {
        return $this->encodeResult('20005', 'HTTP 请求方式非法', array('allowMethod' => array('POST')));
    }

    /**
     * 新增收藏
     * @author Luyu<luyu.zhang@autotiming.com>  zhuzhengqian <zhengqian zhu@autotiming.com>
     *
     */
    public function postVideocreate()
    {
        // 表单验证规则
        $input = Input::only('token', 'video_guid','fragment_guid');
        $rules = array(
            'token' => array('required'),
            'video_guid' => array('required', 'min:36', 'max:36'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $v = $this->verifyToken($input['token']);
        if (!$v) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }

        $video = \Video::where('guid', '=', $input['video_guid'])->get()->first();
        if(isset($input['fragment_guid'])){
            $fragment = \Fragment::where('guid', '=', $input['fragment_guid'])->first();
            if(!$fragment){
                return $this->encodeResult('20402', 'Video does not exist.');
            }
        }

        /**
         * 判断是否存在
         */
        if (!$video) {
            return $this->encodeResult('20401', 'Video does not exist.');
        }
        $f = \Favorite::where("user_id", $v)->where('play_id', $video->getPlayIdStr())->first();
        if(isset($input['fragment_guid'])){
            $f = \Favorite::where("user_id", $v)->where('play_id', $fragment->getPlayIdStr())->first();
        }

        // 新增收藏
        if (!$f) {
            $f = new \Favorite();
            $f->play_id = $video->getPlayIdStr();
            $f->user_id = $v;

            if ($input['fragment_guid']) {
                // 如果收藏的是碎片
                $f->play_id = $fragment->getPlayIdStr();
            }

            $f->save();

            return $this->encodeResult('10402', 'Succeed.');
        } else {
            return $this->encodeResult('10403', 'Succeed.');
        }

    }


    /**
     *新增笔记收藏
     * @author zhuzhengqian
     */
    public function postPlaylistcreate(){
        // 表单验证规则
        $input = Input::only('token', 'playlist_id');
        $rules = array(
            'token' => array('required'),
            'playlist_id' => array('required')
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $v = $this->verifyToken($input['token']);
        if (!$v) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }

        $playlist = Playlist::find($input['playlist_id']);

        /**
         * 判断是否存在
         */
        if (!$playlist) {
            return $this->encodeResult('20401', 'playlist does not exist.');
        }

        $f = \Favorite::where("user_id", $v)->where('play_id', $playlist->getPlayIdStr())->first();

        // 新增收藏
        if (!$f) {
            $f = new \Favorite();
            $f->play_id =$playlist->getPlayIdStr();
            $f->user_id = $v;
            $f->save();
            return $this->encodeResult('10402', 'Succeed.');
        } else {
            return $this->encodeResult('10403', 'Succeed.');
        }
    }

    /**
     * 取消收藏
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getVideodestroy()
    {
        return $this->postVideodestroy();
    }

    /**
     * 取消收藏
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function postVideodestroy()
    {
        // 表单验证规则
        $input = Input::only('token', 'video_guid');
        $rules = array(
            'token' => array('required'),
            'video_guid' => array('required', 'min:36', 'max:36'),
            'fragment_guid' => array('min:36', 'max:36'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $v = $this->verifyToken($input['token']);
        if (!$v) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }

        $video = \Video::where('guid', '=', $input['video_guid'])->get()->first();

        /**
         * 判断是否存在
         */
        if (!$video) {
            return $this->encodeResult('20401', 'Video does not exist.');
        }

        $f = \Favorite::where('play_id', $video->getPlayIdStr())->first();

        // TODO: 记录 Fragment ID

        // 删除收藏
        if ($f) {
            $f->delete();
            return $this->encodeResult('10404', 'succeed');
        } else {
            return $this->encodeResult('10405', 'succeed');
        }
    }


    /**
     *笔记取消收藏
     * @author zhuzhengqian
     */
    public function postPlaylistdestroy(){
        // 表单验证规则
        $input = Input::only('token', 'playlist_id');
        $rules = array(
            'token' => array('required'),
            'playlist_id' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $v = $this->verifyToken($input['token']);
        if (!$v) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }
        $pl = Playlist::where('id',$input['playlist_id'])->first();
        if(!$pl){
            return $this->encodeResult('20401', 'Play list does not exist.');
        }
        $playlist = \Favorite::where('play_id', $pl->getPlayIdStr())->first();
        // 删除收藏
        if ($playlist) {
            $playlist->delete();
            return $this->encodeResult('10404', 'succeed');
        } else {
            return $this->encodeResult('10405', 'succeed');
        }

    }


    /**
     *判断有没有收藏
     * @author zhuzhengqian
     */
    public function getCollected(){
        $input = \Input::only('token','video_guid','fragment_guid','playlist_id');
        $rules = array(
            'token' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 验证 Token 身份
        $user_id = $this->verifyToken($input['token']);
        if (!$user_id) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }
        if(isset($input['video_guid'])){
            $video = \Video::where('guid',$input['video_guid'])->first();
            if(!$video){
                return $this->encodeResult('20401','the video is not exist',null);
            }
            if(\Favorite::where("user_id", $user_id)->where('play_id', $video->getPlayIdStr())->first()){
               return $this->encodeResult('10406','the video is collected',null);
            }else{
                return $this->encodeResult('10407','the video is not collected',null);
            }
        }elseif(isset($input['fragment_guid'])){
            $fragment = Fragment::where('guid',$input['fragment_guid'])->first();
            if(!$fragment){
                return $this->encodeResult('20402','the fragment is not exist',null);
            }
            if(\Favorite::where("user_id", $user_id)->where('play_id', $fragment->getPlayIdStr())->first()){
                return  $this->encodeResult('10406','the fragment is collected',null);
            }else{
                return $this->encodeResult('10407','the fragment is not collected',null);
            }
        }elseif(isset($input['playlist_id'])){
            $pl = Playlist::where('id',$input['playlist_id'])->first();
            if( !$pl){
               return $this->encodeResult('20410','the playlist is not exist',null);
            }
            if(\Favorite::where("user_id", $user_id)->where('play_id', $pl->getPlayIdStr())->first()){
                return $this->encodeResult('10406','the playlist is collected',null);
            }else{
                return  $this->encodeResult('10407','the playlist is not collected',null);
            }
        }else{
            return $this->encodeResult('20002', 'The input parameter is not correct.',null);
        }
    }
}