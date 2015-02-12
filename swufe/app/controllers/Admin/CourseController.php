<?php namespace HihoEdu\Controller\Admin;

use \View;
use \VideoAttachment;
use \Highlight;
use \Fragment;
use \Annotation;
use \Question;
use \Video;
use \Subtitle;
use \VideosReference;
use \Input;
use \Response;

/**
 * Created with JetBrains PhpStorm.
 * User: zhu
 * DateTime: 14-6-10 下午5:19
 * Email:www321www@126.com
 */

class CourseController extends AdminBaseController
{

    /**
     *视频上传显示页面
     *
     */
    public function uploadAttachmentShow($video_id){
        $objAttachment = VideoAttachment::where('video_id',$video_id)->get();
        return View::make('admin.course.upload_attachment',compact('objAttachment','video_id'));
    }

    /**
     * #上传图片
     * @return mixed
     * @author zhuzhengqian
     */
    public function imgUpload(){
        $date = date("Ymd");
        $targetDir = public_path().'/upload/'.$date;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777);
        }
        $fileData = \Input::file('file');
        $allowExt = array('jpg','jpeg','gif','png');
        //check extension
        $ext = $fileData->guessExtension();
        if (!in_array($ext, $allowExt)) {
            return \Response::json(array('error' => -1, 'msg' => 'INVALID_FILETYPE'), 403);
        }
        $realName = $fileData->getClientOriginalName();
        $tmpName = $fileData->getRealPath();
        $randomName = date('His') . rand(1, 100) . '.' . $ext;
        $size = $fileData->getClientSize();
        //move
        ##windows
        $fileData->move($targetDir, $randomName);
        ##linux
//        @system("mv $tmpName /tmp/11111.mp4");
        //check file
        $fileUrl = $targetDir . '/' . $randomName;
        echo sprintf("/upload/%s/%s",$date,$randomName);
    }

    /**
     *删除附件
     * @param $id
     * @return mixed
     * @author Haiming<haiming.wang@autotiming.com>
     */
    public function deleteAttachment($id)
    {
        $objAttachment = VideoAttachment::where('id', $id)->first();
        if ($objAttachment) {
            $objAttachment->delete();
            return json_encode(array("status" => 0, "message" => "删除成功"));
        } else {
            return json_encode(array("status" => -1, "message" => "删除失败，不存在该附件"));
        }
    }

    /**
     *上传视频附件
     * @author zhuzhnegqian
     */
    public function uploadAttachment($video_id){
        ## linux
//        $targetDir = "/data/www/data/userdata/vismam/resource/" . date("Ym");
        ## windows
        $targetDir = public_path().'/upload/'.date("Ymd");
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777);
        }
        $fileData = \Input::file('Filedata');
        $allowExt = array('mp4', 'flv', 'm3u8','jpg','jpeg','gif','png','zip','rar','mp3','doc','ppt','docx','md');
        //check extension
        $ext = $fileData->guessExtension();
        if (!in_array($ext, $allowExt)) {
            return \Response::json(array('error' => "INVALID_FILETYPE", 'msg' => 'INVALID_FILETYPE'), 403);
        }
        $realName = $fileData->getClientOriginalName();
        $tmpName = $fileData->getRealPath();
        $randomName = date('His') . rand(1, 100) . '.' . $ext;
        $size = $fileData->getClientSize();
        //move
        ##windows
        $fileData->move($targetDir, $randomName);
        ##linux
//        @system("mv $tmpName /tmp/11111.mp4");
        //check file
        $fileUrl = $targetDir . '/' . $randomName;
        if (!is_file($fileUrl) || (filesize($fileUrl) != $size)) {
            return \Response::json(array('error' => 'file is not complete', 'msg' => 'file is not complete'), 403);
        }
        //save in mysql
        $attachment = new VideoAttachment();
        $attachment->video_id = $video_id;
        $attachment->title = $realName;
        $attachment->path = '/upload/'.date("Ymd").'/'.$randomName;
        $attachment->size = $size;
        $attachment->ext = $ext;
        $attachment->save();


        return \Response::json(array('error' => 0, 'msg' => 'file upload success'), 200);
    }


    /**
     * #删除附件
     */
    public function attachmentDestroy($id){
        $obj = VideoAttachment::find($id);
        $obj->delete();
        return \Redirect::to('/admin/course/uploadAttachmentShow/'.$obj->video_id);

    }

    /**
     *post创建视频重点片段
     * @author zhuzhengqian
     */
    public function highlightCreate(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }
        $inputData = Input::all();
        $rule = array(
            'video_guid'=>'required',
            'st'=>'required',
            'et'=>'required',
            'title'=>'required',
            'description'=>''
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }
        $objVideo = Video::where('guid',$inputData['video_guid'])->first();
        if(!$objVideo){
            return Response::json(array('msgCode'=>-1,'message'=>'video is not found','data'=>NULL));
        }
        $screenShot = \Fragment::getCoverV2($objVideo,$inputData['st'],$inputData['et']);
        $objHighlight = new Highlight();
        $objHighlight->video_id = $objVideo->video_id;
        $objHighlight->user_id = \Auth::user()->user_id;
        $objHighlight->st = $inputData['st'];
        $objHighlight->et = $inputData['et'];
        $objHighlight->title = $inputData['title'];
        $objHighlight->description = isset($inputData['description']) ? $inputData['description'] : '';
        $objHighlight->thumbnail = $screenShot;
        $objHighlight->save();
        return Response::json(array('msgCode'=>0,'message'=>'create highlight success','data'=>$objHighlight->toArray()));
    }


    /**
     * #查看重点详情
     */
    public function highlightDetail(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }
        $inputData = Input::all();
        $rule = array(
            'highlight_id'=>'required'
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }
        $objHighlight = Highlight::find($inputData['highlight_id']);
        if( ! $objHighlight){
            return Response::json(array('msgCode'=>-1,'message'=>'video is not found','data'=>NULL));
        }
        return Response::json(array('msgCode'=>0,'message'=>'highlight detail','data'=>$objHighlight->toArray()));
    }

    /**
     * #重点修改
     * @return mixed
     * @author zhuzhengqian
     */
    public function highlightModify(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }
        $inputData = Input::all();
        $rule = array(
            'highlight_id'=>'required',
            'video_guid'=>'',
            'st'=>'required',
            'et'=>'required',
            'title'=>'required',
            'user_id'=>'',
            'description'=>''
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }
        $objHighlight = Highlight::find($inputData['highlight_id']);
        $objVideo = Video::where('video_id',$objHighlight->video_id)->first();
        $screenShot = \Fragment::getCoverV2($objVideo,$inputData['st'],$inputData['et']);
        if(!$objVideo){
            return Response::json(array('msgCode'=>-1,'message'=>'video is not found','data'=>NULL));
        }
        if( ! $objHighlight){
            return Response::json(array('msgCode'=>-3,'message'=>'highlight is not found','data'=>NULL));
        }
        $objHighlight->user_id = \Auth::user()->user_id;
        $objHighlight->st = $inputData['st'];
        $objHighlight->et = $inputData['et'];
        $objHighlight->title = $inputData['title'];
        $objHighlight->description = isset($inputData['description']) ? $inputData['description'] : '';
        $objHighlight->thumbnail = $screenShot;
        $objHighlight->save();
        return Response::json(array('msgCode'=>0,'message'=>'modify highlight success','data'=>$objHighlight->toArray()));
    }

    /**
     *删除重点
     * @return mixed
     * @author zhuzhengqian
     */
    public function highlightDestroy(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }

        $inputData = Input::all();
        $rule = array(
            'id'=>'required',
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }
        $id = $inputData['id'];
        $objHighlight = Highlight::find($id);
        if( ! $objHighlight){
            return Response::json(array('msgCode'=>-1,'message'=>'highlight is not found','data'=>NULL));
        }
        $objHighlight->delete();

        return Response::json(array('msgCode'=>0,'message'=>'highlight destroy success','data'=>NULL));
    }

    /**
     *视频注释创建
     * @return mixed
     * @author zhuzhengqian
     */
    public function annotationCreate(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }

        $inputData = Input::all();
        $rule = array(
            'video_guid'=>'required',
            'st'=>'required',
            'et'=>'required',
            'content'=>'required',
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }

        $objVideo = Video::where('guid',$inputData['video_guid'])->first();
        if(!$objVideo){
            return Response::json(array('msgCode'=>-1,'message'=>'video is not found','data'=>NULL));
        }

        $objHighlight = new Annotation();
        $objHighlight->video_id = $objVideo->video_id;
        $objHighlight->st = $inputData['st'];
        $objHighlight->et = $inputData['et'];
        $objHighlight->content = $inputData['content'];
        $objHighlight->save();

        return Response::json(array('msgCode'=>0,'message'=>'create annotation success','data'=>$objHighlight->toArray()));
    }


    /**
     * #注释修改
     * @return mixed
     * @author zhuzhengqian
     */
    public function annotationModify(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }

        $inputData = Input::all();
        $rule = array(
            'annotation_id'=>'required',
            'video_guid'=>'',
            'st'=>'required',
            'et'=>'required',
            'content'=>'required',
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }

        $objHighlight = Annotation::find($inputData['annotation_id']);
        if(!$objHighlight){
            return Response::json(array('msgCode'=>-1,'message'=>'highlight not exist','data'=>NULL));
        }
        $objHighlight->st = $inputData['st'];
        $objHighlight->et = $inputData['et'];
        $objHighlight->content = $inputData['content'];
        $objHighlight->save();

        return Response::json(array('msgCode'=>0,'message'=>'modify annotation success','data'=>$objHighlight->toArray()));
    }

    /**
     * #注释详情
     * @return mixed
     * @author zhuzhengqian
     */
    public function annotationDetail(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }

        $inputData = Input::all();
        $rule = array(
            'annotation_id'=>'required'
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }

        $objAnotation = Annotation::find($inputData['annotation_id']);
        if( ! $objAnotation){
            return Response::json(array('msgCode'=>-1,'message'=>'annotation is not found','data'=>NULL));
        }

        return Response::json(array('msgCode'=>0,'message'=>'annotation success','data'=>$objAnotation->toArray()));

    }

    /**
     * #删除注释
     * @return mixed
     * @author zhuzhengqian
     *
     */
    public function annotationDestroy(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }

        $inputData = Input::all();
        $rule = array(
            'annotation_id'=>'required'
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }

        $objAnotation = Annotation::find($inputData['annotation_id']);
        if( ! $objAnotation){
            return Response::json(array('msgCode'=>-1,'message'=>'annotation is not found','data'=>NULL));
        }
        $objAnotation->delete();
        return Response::json(array('msgCode'=>0,'message'=>'annotation destroy success','data'=>NULL));
    }

    /**
     *添加问题
     * @author zhuzhengqian
     */
    public function questionCreate(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }

        $inputData = Input::all();
        $rule = array(
            'video_guid'=>'required',
            'time_point'=>'required',
            'sort'=>'',
            'title'=>'required',
            'type'=>'required|in:radio,checkbox',
            'answers'=>'required',
            'correct_answers'=>'required',
            'correct_operation'=>'',
            'error_operation'=>'required|in:goto,tips,continue',
            'target_highlight_id'=>'requiredif:error_operation,goto',
            'tips_content'=>'requiredif:error_operation,tips',
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }
        $objVideo = Video::where('guid',$inputData['video_guid'])->first();
        if(!$objVideo){
            return Response::json(array('msgCode'=>-1,'message'=>'video is not found','data'=>NULL));
        }
        $objQuestion = new \Question();
        $objQuestion->video_id = $objVideo->video_id;
        $objQuestion->sort = !empty($inputData['sort']) ? $inputData['sort'] : 0;
        $objQuestion->time_point = $inputData['time_point'];
        $objQuestion->type = $inputData['type'];
        $objQuestion->title = $inputData['title'];
        $objQuestion->answers = serialize(json_decode($inputData['answers'],true));
        $objQuestion->correct_answers = serialize(json_decode($inputData['correct_answers'],true));
        $objQuestion->correct_operation =  'continue';
        $objQuestion->error_operation = $inputData['error_operation'];
        $objQuestion->target_highlight_id = !empty($inputData['target_highlight_id']) ? $inputData['target_highlight_id'] : NULL;
        $objQuestion->tips_content = !empty($inputData['tips_content']) ? $inputData['tips_content'] : NULL;
        $objQuestion->save();
        return Response::json(array('msgCode'=>0,'message'=>'question create success','data'=>$objQuestion->toArray()));
    }


    /**
     * #问题修改
     * @return mixed
     * @author zhuzhengqian
     */
    public function questionsModify(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }

        $inputData = Input::all();
        $rule = array(
            'question_id'=>'required',
            'video_guid'=>'',
            'time_point'=>'required',
            'sort'=>'',
            'title'=>'required',
            'type'=>'required|in:radio,checkbox',
            'answers'=>'required',
            'correct_answers'=>'required',
            'correct_operation'=>'',
            'error_operation'=>'required|in:goto,tips,continue',
            'target_highlight_id'=>'requiredif:error_operation,goto',
            'tips_content'=>'requiredif:error_operation,tips',
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }

        $objQuestion = Question::find($inputData['question_id']);
        $objQuestion->sort = !empty($inputData['sort']) ? $inputData['sort'] : 0;
        $objQuestion->time_point = $inputData['time_point'];
        $objQuestion->type = $inputData['type'];
        $objQuestion->title = $inputData['title'];
        $objQuestion->answers = serialize(json_decode($inputData['answers'],true));
        $objQuestion->correct_answers = serialize(json_decode($inputData['correct_answers'],true));
        $objQuestion->correct_operation =  'continue';
        $objQuestion->error_operation = $inputData['error_operation'];
        $objQuestion->target_highlight_id = !empty($inputData['target_highlight_id']) ? $inputData['target_highlight_id'] : NULL;
        $objQuestion->tips_content = !empty($inputData['tips_content']) ? $inputData['tips_content'] : NULL;
        $objQuestion->save();
        return Response::json(array('msgCode'=>0,'message'=>'question modify success','data'=>$objQuestion->toArray()));
    }

    /**
     * #问题详情
     * @return mixed
     * @author zhuzhengqian
     */
    public function questionDetail(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }

        $inputData = Input::all();
        $rule = array(
            'question_id'=>'required'
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }
        $objQuestion = Question::find($inputData['question_id']);
        if(!$objQuestion){
            return Response::json(array('msgCode'=>-1,'message'=>'question is not found','data'=>NULL));
        }

        if( $highlightId = $objQuestion->target_highlight_id){
            $objQuestion->highlightDetail = Highlight::find($highlightId);
        }
        $objQuestion->answers = unserialize($objQuestion->answers);
        $objQuestion->correct_answers = unserialize($objQuestion->correct_answers);

        return Response::json(array('msgCode'=>0,'message'=>'question detail success','data'=>$objQuestion->toArray()));

    }
    /**
     *删除问题
     * @author zhuzhengqian
     */
    public function questionsDestroy(){
        if(\Auth::guest()){
            return Response::json(array('msgCode'=>-9999,'message'=>'permission denied','data'=>NULL));
        }

        $inputData = Input::all();
        $rule = array(
            'question_id'=>'required',
        );
        $validator = \Validator::make($inputData,$rule);
        if($validator->fails()){
            return Response::json(array('msgCode'=>-2,'message'=>$validator->messages()->first(),'data'=>NULL));
        }

        $objQuestion = \Question::find($inputData['question_id']);
        if( ! $objQuestion){
            return Response::json(array('msgCode'=>-1,'message'=>'the question is not found','data'=>NULL));
        }
        $objQuestion->delete();
        return Response::json(array('msgCode'=>0,'message'=>'question delete success','data'=>NULL));
    }


    /**
     *视频附录
     * @param $video_id
     */
    public function referenceCreate($video_id){
        $objReference = VideosReference::where('video_id',$video_id)->get();
        return View::make('admin.course.reference_create',compact('video_id','objReference'));
    }

    /**
     *添加视频附录post
     * @author zhuzhengqian
     */
    public function postReferenceCreate(){
        $inputData = Input::all();
        $videoId = $inputData['video_id'];
        $objVideo = Video::find($videoId);
        if(!$objVideo){
            return Response::json(array('msgCode'=>-1,'message'=>'video is not found'));
        }
        $objHighlight = new VideosReference();
        $objHighlight->video_id = $videoId;
        $objHighlight->content = $inputData['content'];
        $objHighlight->save();
        return Response::json(array('msgCode'=>0,'message'=>'create reference success'));
    }

    /**
     * #问题与重点
     * @param $videoId
     * @return mixed
     * @atuhor zhuzhengqian
     */
    public function questionsHighlights($videoId){
        $objVideo = Video::find($videoId);
        $videoGuid = $objVideo->guid;
        $language = $objVideo->language;
        return View::make('admin.course.questions_hlights',compact('videoGuid','language','videoId'));
    }

    /**
     * #注释与链接
     * @param $videoId
     * @return mixed
     * @atuhor zhuzhengqian
     */
    public function annotationsLinks($videoId){
        $objVideo = Video::find($videoId);
        $videoGuid = $objVideo->guid;
        $language = $objVideo->language;
        return View::make('admin.course.annotations_links',compact('videoGuid','language','videoId'));
    }


    /**
     * #添加附录
     * @param $video_id
     * @return mixed
     */
    public function appendixsCreate($video_id){
        $objAppendix = \VideosReference::where('video_id',$video_id)->first();
        return View::make('admin.course.appendixs',compact('video_id','objAppendix'));
    }

    /**
     * #post添加附录
     */
    public function postAppendixsCreate(){
        $inputData = Input::all();
        $videoId = $inputData['video_id'];
        if(!$videoId){
            echo '没有传递video_id';
            exit;
        }
        $obj = \VideosReference::where('video_id',$videoId)->first();
        if(!$obj){
            $objAppendix = new \VideosReference();
        }else{
            $objAppendix = \VideosReference::find($obj->id);
        }
        $objAppendix->video_id = $inputData['video_id'];
        $objAppendix->content = $inputData['appendix_content'];
        $objAppendix->save();
        return \Redirect::to('/admin/course/appendixs/create/'.$videoId)->with('success_tips','视频附录添加成功');
    }



}
