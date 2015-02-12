<?php namespace HihoEdu\Controller\Admin;

use Illuminate\Support\Facades\Redirect;
use \TopicVideos;
use \Topic;
use \Recommend;
use HiHo\Edu\Sewise\VideoStatus;
use J20\Uuid\Uuid;

class VideoController extends AdminBaseController
{


    /**
     *视频列表
     * @param null $type
     * @return mixed
     * @author zhuzhengqian
     */
    public function uploadList($type = NULL)
    {
        ##判断是get or post
        $type = \Input::get('type') ? \Input::get('type') : $type;
        $title = \Input::get('title');
        $videoPageSize = 20;
        ##get video status
        $arrSourceid = array();
        $videos = \SewiseVideosInfo::whereRaw("source_id != '' and status != '7' and status !='-7' ")->paginate($videoPageSize);
        ##localhost test
//        $videos = '';
        if ($videos) {
            foreach ($videos as $video) {
                ##save source id
                $arrSourceid[$video->id] = $video->source_id;
            }
        }
        ##get status and save the infos
        if ($arrSourceid) {
            $status = new VideoStatus($arrSourceid);
            $ret = json_decode($status->getStatus(), TRUE);
//            print_r($ret);
            foreach ($ret['record'] as $r) {
                $objVideo = \SewiseVideosInfo::where('source_id', '=', $r['sourceid'])->first();
                $objVideo->status = $r['status'];
                if (!empty($r['tasks']))
                    $objVideo->task_id = $r['tasks'][0]['record']['taskid'];
                $objVideo->save();
                ##cover pic
                $objPic = \SewiseVideosPicture::where('video_id', '=', $objVideo->id)->first();
                if (!empty($r['tasks']) && empty($objPic)) {
                    $objPic = new \SewiseVideosPicture();
                    $objPic->video_id = $objVideo->id;
                    $objPic->src = $this->saveImgToLocal($r['tasks'][0]['record']['cover']['src']);
                    $objPic->save();
                }
            }
        }
        ##get new list
        if (empty($type)) {
            $videos = \SewiseVideosInfo::orderBy('created_at', 'desc')->where('title','LIKE',"%$title%")->paginate($videoPageSize);
        } elseif($type=='-99'){
            $videos = \SewiseVideosInfo::orderBy('created_at', 'desc')->where('title','LIKE',"%$title%")->where('source_id','')->paginate($videoPageSize);
        } else{
            $in = $this->handerType($type);
            $videos = \SewiseVideosInfo::orderBy('created_at', 'desc')->where('title','LIKE',"%$title%")->whereIn('status', $in)->paginate($videoPageSize);
        }

        ##get pic
        if ($videos) {
            foreach ($videos as $video) {
                $video->pic = \SewiseVideosPicture::where('video_id', $video->id)->first();
            }
        }
        return \View::make('admin.video.bs3_upload_list', compact('videos', 'type','title'));
    }

    /**
     * #保存远程图片到本地
     * @author zhuzhengqian
     * @return mixed
     */
    public function saveImgToLocal($url){
        @mkdir("screenshot/" . date("Y-m-d", time()) . "/", 0777, true);
        $screenshot = "screenshot/" . date("Y-m-d", time()) . "/" . Uuid::v4(false) . ".jpeg";
        $fp2 = @fopen($screenshot, 'w');
        fwrite($fp2, file_get_contents($url));
        fclose($fp2);
        return asset($screenshot);
    }

    /**
     *添加视频
     * @return mixed
     * @author zhengqian
     */
    public function videoAdd()
    {
//        return \View::make('admin.video.upload');
        return \View::make('admin.video.bs3_upload');
    }

    /**
     *上传视频
     * @return mixed
     * @author zhuzhengqian
     */
    public function doUpload()
    {
        ## linux
        $targetDir = "/data/www/data/userdata/vismam/resource/" . date("Ym");
        ## windows
//        $targetDir = public_path().'/upload/'.date("Ymd");
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777);
        }
        $fileData = \Input::file('Filedata');
        $allowExt = array('mp4', 'flv');
        //check extension
        $ext = $fileData->guessExtension();
        if (!in_array($ext, $allowExt)) {
            return \Response::json(array('error' => "INVALID_FILETYPE", 'msg' => 'INVALID_FILETYPE'), 403);
        }
        $realName = $fileData->getClientOriginalName();
        $tmpName = $fileData->getRealPath();
        $randomName = date('His') . rand(1, 100) . '.' . $ext;
        //move
        ##windows
        $fileData->move($targetDir, $randomName);
        ##linux
//        @system("mv $tmpName /tmp/11111.mp4");
        //check file
        $fileUrl = $targetDir . '/' . $randomName;
        if (!is_file($fileUrl) || (filesize($fileUrl) != $fileData->getClientSize())) {
            return \Response::json(array('error' => 'file is not complete', 'msg' => 'file is not complete'), 403);
        }
        //save in mysql
        $videos = new \SewiseVideosInfo();
        $videos->title = substr($realName, 0, strrpos($realName, '.'));
        $videos->resource_path = $fileUrl;
        $videos->bytesize = $fileData->getClientSize();
        $videos->save();
        return \Response::json(array('error' => 0, 'msg' => 'file upload success'), 200);
    }

    /**
     *添加修改视频信息
     * @param int $video_id
     * @return mixed
     * @author zhuzhengqian
     */
    public function modify($video_id = NULL)
    {
        empty($video_id) and \App::abort('404', 'video_id is required');
        $objVideo = \VideoInfo::where('video_id',$video_id)->first();
        empty($objVideo) and \App::abort('404', 'video is not found');
        $departments = $this->getDepartment();
        $categories = $this->getCategories();
        $topics = $this->getAllTopics();
        $specialities = $this->getSpecialities();
        $teachers = \Teacher::all();
        if ($teachers) {
            foreach ($teachers as $t) {
                if (\TeacherVideo::where('video_id', $objVideo->video_id)->where('teacher_id', $t->id)->first()) {
                    $t->selected = 1;
                }
            }
        }

        if ($topics) {
            foreach ($topics as $topic) {
                if (\TopicVideos::where('video_id', $objVideo->video_id)->where('topic_id', $topic->id)->first()) {
                    $topic->selected = 1;
                }
            }
        }
        $specialitiesSelected = $objVideo->video->specialities;
        $specialitySelected = empty($specialitiesSelected) ? None : $specialitiesSelected->get(0);
        return \View::make('admin.video.bs3_modify', compact('objVideo', 'departments', 'categories', 'teachers', 'topics', 'specialities','specialitySelected'));
    }


    /**
     *删除上传视频视频
     * @param int $video_id
     * @return mixed
     * @author zhuzhengqian
     */
    public function videoUploadDelete($video_id = NULL)
    {
        empty($video_id) and \App::abort('404', 'video id is required');
        $objVideo = \SewiseVideosInfo::find($video_id);
        empty($objVideo) and \App::abort('404', 'video is note found');
        $objVideo->delete();
        //delete video fiel
        @unlink($objVideo->video_url);
        return \Redirect::to('/admin/videos/uploadList')->with('success_tips', 'video delete success');
    }

    /**
     *删除视频
     * @param null $video_id
     * @return mixed
     * @author zhuzhengqian
     */
    public function videoDelete($video_id = NULL)
    {
        empty($video_id) and \App::abort('404', 'video id is required');
        //删除video
        $objVideo = \Video::find($video_id);
        empty($objVideo) and \App::abort('404', 'video is note found');
        $objVideo->delete();


        return \Redirect::to('/admin/video/list')->with('success_tips', 'video delete success');
    }

    /**
     *post修改视频信息
     * @return mixed
     * @author zhuzhengqian
     */
    public function modifyPost()
    {
        $inputData = \Input::only('title', 'id', 'category', 'teacher', 'topic', 'description', 'access_level', 'speciality');
        ##check title
        if (trim($inputData['title']) == '') {
            return \Redirect::to('/admin/video/modify/' . $inputData['id'])->with('title_tips', 'title is required');
        }
        $access_level = $inputData['access_level'];
        if($access_level){
            if(!is_numeric($access_level)){
                return \Redirect::to('/admin/video/modify/' . $inputData['id'])->with('title_tips', '权限等级必须为数字');
            }
        }else{
            $access_level = 0;
        }
        $specialityId = $inputData['speciality'];
        if ($specialityId) {
            $speciality = \Speciality::where('id', $specialityId)->first();
            if (!$speciality) {
                return \Redirect::to('/admin/video/modify/' . $inputData['id'])->with('title_tips', '专业不存在');
            }
        }
        ##save video info
        $objVideo = \VideoInfo::where('video_id', $inputData['id'])->first();
        $objVideo->title = addslashes($inputData['title']);
        $objVideo->description = addslashes($inputData['description']);
        $objVideo->save();
        ##save category
        //删除分类
        \VideoCategory::where('video_id', $objVideo->video_id)->delete();
        $categoryIds = $inputData['category'];
        if (!empty($categoryIds)) {
            $categoryIds = array_unique($categoryIds);
            foreach ($categoryIds as $categoryId) {
                if ($categoryId != 0) {
                    $obj = new \VideoCategory();
                    $obj->video_id = $objVideo->video_id;
                    $obj->category_id = $categoryId;
                    $obj->save();
                }
            }
        }
        ##save teacher
        if ($inputData['teacher']) {
            $objTeacher = \TeacherVideo::where('video_id', $objVideo->video_id)->first();
            if (!$objTeacher) {
                $obj = new \TeacherVideo();
                $obj->video_id = $objVideo->video_id;
                $obj->teacher_id = $inputData['teacher'];
                $obj->save();
            } else {
                $objTeacher->teacher_id = $inputData['teacher'];
                $objTeacher->save();
            }
        }else{
            \TeacherVideo::where('video_id', $objVideo->video_id)->delete();
        }
//        ##save topic
        if ($inputData['topic']) {
            $objTopic = TopicVideos::where('video_id', $objVideo->video_id)->first();
            if (!$objTopic) {
                $obj = new TopicVideos();
                $obj->video_id = $objVideo->video_id;
                $obj->topic_id = $inputData['topic'];
                $obj->save();
            } else {
                $objTopic->topic_id = $inputData['topic'];
                $objTopic->save();
            }
        }else{
            TopicVideos::where('video_id', $objVideo->video_id)->delete();
        }

        //save speciality
        if ($specialityId) {
            $objSpeciality = \VideoSpeciality::where('video_id', $objVideo->video_id)->first();
            if (!$objSpeciality) {
                $obj = new \VideoSpeciality();
                $obj->video_id = $objVideo->video_id;
                $obj->speciality_id = $specialityId;
                $obj->save();
            } else if ($objSpeciality->speciality_id != $specialityId) {
                $objSpeciality->speciality_id = $specialityId;
                $objSpeciality->save();
            }
        } else {
            \VideoSpeciality::where('video_id', $objVideo->video_id)->delete();
        }

        $video = $objVideo->video;
        $video->access_level = $access_level;
        $video->save();
        return \Redirect::to('/admin/video/list')->with('success_tips', '信息修改成功');

    }

    /**
     *视频状态过滤器
     * @param $code
     * @return array
     * @author zhuzhengqian
     */
    public function handerType($code)
    {
        $inRet = array();
        switch ($code) {
            case '7':
                $inRet = array('7');
                break;
            case '-7':
                $inRet = array('-7');
                break;
            case '6':
                $inRet = array('6');
                break;
            case 'else':
                $inRet = array('2', '-2', '0', '3', '-3', '4', '-4', '5', '-5', '8', '-8');
                break;
        }
        return $inRet;
    }



    /**
     *视频列表，区别于上传管理中的列表
     * @param null
     * @return mixed
     * @author zhuzhengqian
     */
    public function videoList()
    {
        $input = \Input::all();
        $arrAllId = array();
        $objVideo = \VideoInfo::all();
        if($objVideo){
            foreach($objVideo as $video){
                array_push($arrAllId,$video->video_id);
                unset($video);
            }
        }

        if(isset($input['category']) && !empty($input['category'])){
            $arrCatIds = array();
            $objVideo = \VideoCategory::where('category_id',$input['category'])->get();
            if($objVideo){
                foreach($objVideo as $video){
                    array_push($arrCatIds,$video->video_id);
                }
            }
            $arrAllId = array_intersect($arrAllId,$arrCatIds);
            unset($objVideo);
        }

        //filter teacher_id
        if(isset($input['teacher']) && !empty($input['teacher'])){
            $arrTeacherIds = array();
            $objVideo = \TeacherVideo::where('teacher_id',$input['teacher'])->get();
            if($objVideo){
                foreach($objVideo as $video){
                    array_push($arrTeacherIds,$video->video_id);
                }
            }
            $arrAllId = array_intersect($arrAllId,$arrTeacherIds);
            unset($objVideo);
        }
        //filter topic_id
        if(isset($input['topic']) && !empty($input['topic'])){
            $arrTopicIds = array();
            $objVideo = \TopicVideos::where('topic_id',$input['topic'])->get();
            if($objVideo){
                foreach($objVideo as $video){
                    array_push($arrTopicIds,$video->video_id);
                }
            }
            $arrAllId = array_intersect($arrAllId,$arrTopicIds);
            unset($objVideo);
        }

        if($arrAllId){
            $strId = implode(',',$arrAllId);
        }else{
            $strId = -1;
        }
        if( ! \Input::except('page')){
            $objVideo =  \VideoInfo::orderby('video_id','desc')->paginate(parent::PAGE_SIZE);
        }else{
            $keyword = isset($input['title'])&&!empty($input['title']) ? $input['title'] : '';
            $objVideo = \VideoInfo::whereRaw("video_id in ($strId) and title like '%$keyword%' and deleted_at is null order by video_id desc")->paginate(parent::PAGE_SIZE);
        }
        $departments = $this->getDepartment();
        $categories = $this->getCategories();
        $topics = $this->getAllTopics();
        $teachers = \Teacher::all();
        if ($objVideo) {
            foreach ($objVideo as $video) {
                //pic
                $objPictures = \VideoPicture::where('video_id', $video->video_id)->first();
                if ($objPictures) {
                    $video->pictures = $objPictures;
                }
                //teacher
                $objTeahcer = \TeacherVideo::where('video_id', $video->video_id)->first();
                if ($objTeahcer) {
                    $video->teacher_info = \Teacher::find($objTeahcer->teacher_id);
                }
                //length
                $objLength = \Video::where('video_id', $video->video_id)->first();
                if ($objLength) {
                    $video->videoInfo = $objLength;
                }

                //category
                $videoCategories = $video->video ? $video->video->categories: array();
                $categoryNames = '';
                foreach($videoCategories as $cat){
                    $categoryNames.=$cat->name.' ';
                }
                $video->category = $categoryNames;
                //topic
                $objTopic = \TopicVideos::where('video_id',$video->video_id)->first();
                if($objTopic){
                    $video->topic = \Topic::find($objTopic->topic_id);
                }
            }
        }
        return \View::make('admin.video.bs3_list', compact('objVideo','departments','categories','topics','teachers'));
    }

    /**
     *为视频绑定相关信息
     * @param null
     * @return string
     * @author zhuzhengqian
     */
    function ajaxBindVideoInfo(){
        if(\Request::ajax()){
            $input = \Input::all();
            if(empty($input['check_id'])){
                return '请选择要操作的视频';
            }
            foreach($input['check_id'] as $video_id){
                //category
               if($input['category']){
                   //指定分类
                   $objCategory = \VideoCategory::where('video_id', $video_id)->first();
                   if (!$objCategory) {
                       $obj = new \VideoCategory();
                       $obj->video_id = $video_id;
                       $obj->category_id = $input['category'];
                       $obj->save();
                   } else {
                       $objCategory->category_id = $input['category'];
                       $objCategory->save();
                   }
               }
                //topic
                if ($input['topic']) {
                    $objTopic = TopicVideos::where('video_id', $video_id)->first();
                    if (!$objTopic) {
                        $obj = new TopicVideos();
                        $obj->video_id = $video_id;
                        $obj->topic_id = $input['topic'];
                        $obj->save();
                    } else {
                        $objTopic->topic_id = $input['topic'];
                        $objTopic->save();
                    }
                }
                //teacher
                if ($input['teacher']) {
                    $objTeacher = \TeacherVideo::where('video_id', $video_id)->first();
                    if (!$objTeacher) {
                        $obj = new \TeacherVideo();
                        $obj->video_id = $video_id;
                        $obj->teacher_id = $input['teacher'];
                        $obj->save();
                    } else {
                        $objTeacher->teacher_id = $input['teacher'];
                        $objTeacher->save();
                    }
                }



            }//end foreach
            return json_encode(array('msgCode'=>'0','message'=>'操作成功','data'=>null));
        }else{
            return json_encode(array('msgCode'=>'-1','message'=>'必须为ajax请求','data'=>null));
        }
    }

}
