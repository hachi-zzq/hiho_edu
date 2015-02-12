<?php namespace HihoEdu\Controller\Admin;

use HiHo\Edu\Sewise\VideoStorage;
use Whoops\Example\Exception;
use HiHo\Subtitle\Srt2Txt;
use \Input;
use \Validator;
use \Subtitle;
use \Highlight;
use \Annotation;
use \Question;
use \VideosReference;
use \Response;
/**
 * Created with JetBrains PhpStorm.
 * User: zhuzhengqian
 * DateTime: 14-6-10 下午5:19
 * Email:www321www@126.com
 */
class SubtitleController extends AdminBaseController
{

    /**
     *为视频添加字幕
     * @param int $videoId
     * @return mixed
     * @author zhuzhengqian
     */
    public function add($videoId = NULL)
    {
        empty($videoId) and \App::abort(400, 'videoid is required');
        $objVideo = \SewiseVideosInfo::find($videoId);
        empty($objVideo) and \App::abort(404, 'video is not found');
        return \View::make('admin.subtitle.bs3_add_subtitle', compact('objVideo'));
    }


    /**
     *添加字幕post
     * @param int $videoId
     * @return mixed
     * @author zhuzhengqian
     */
    public function postAdd($videoId = NULL)
    {
        $postData = \Input::only('title', 'language', 'video_language', 'video_id', 'subtitle');
        $videoId = !empty($postData['video_id']) ? intval($postData['video_id']) : $videoId;
        $rules = array(
            'title' => 'required',
            'subtitle' => 'required'
        );
        $validator = \Validator::make($postData, $rules);
        if ($validator->fails()) {
            $message = $validator->messages();
            return \Redirect::to('/admin/video/addSubtitle/' . $videoId)->with('error_tips', $message->first());
        }

        ##save title
        $objVideo = \SewiseVideosInfo::find($videoId);
        $objVideo->title = $postData['title'];
        $objVideo->language = $postData['video_language'];
        $objVideo->save();

        ## save subtitle
        $objSubtitle = new \SewiseVideosSubtitle();
        $objSubtitle->video_id = $videoId;
        $objSubtitle->subtitle = $postData['subtitle'];
        $objSubtitle->language = $postData['language'];
        $objSubtitle->save();

        ##push data
        $sourceId = $this->postData($objVideo->id, $objVideo->id);

        ##save source id
        $objVideo->source_id = $sourceId;
        $objVideo->save();
        return \Redirect::to('/admin/videos/uploadList')->with('success_tips', '字幕添加成功');

    }

    /**
     *ajax文件上传srt转txt
     * @author zhuzhengqian
     */
    public function srt2Txt(){

        if($fileData=\Input::file('file_subtitle')){
            //check ext
            $allowExt = array('srt','txt');
            //check extension
            $ext = $fileData->guessExtension();
            if (!in_array($ext, $allowExt)) {
                return json_encode(array('msgCode'=>'-1','message'=>'文件格式不合法'));
            }

            $srt2txt = new Srt2Txt($fileData->getRealPath());
            $txtContent = $srt2txt->convert();
            return json_encode(array('msgCode'=>'0','message'=>'success','data'=>$txtContent));
        }else{
            return json_encode(array('msgCode'=>'-2','message'=>'请上传文件'));
        }
    }


    /**
     *push到媒资系统
     * @param $fid
     * @param int $videoId
     * @return mixed
     * @author zhuzhengqian
     */
    public function postData($fid, $videoId = NULL)
    {
        empty($videoId) and \App::abort(403, 'id is required');
        $objVideo = \SewiseVideosInfo::find($videoId);
        empty($objVideo) and \App::abort(404, 'video is not found');
        $title = $objVideo->title;
        $audiolanguage = $objVideo->language;
        $length = $objVideo->length;
        $objSubtitle = \SewiseVideosSubtitle::where('video_id', '=', $videoId)->first();
        $subtitleLanguage = $objSubtitle->language;
        $subtitleContent = $objSubtitle->subtitle;
        $config = array(
            'url' => 'local://' . $objVideo->resource_path,
            'title' => $title,
//            'duration'=>'1000',
//            'fid' => -$fid,
            'caption' => $subtitleContent,
            'captionlanguage' => $subtitleLanguage,
            'audiolanguage' => $audiolanguage,
            'autoaudit' => 0
        );

        ##post
        $push = new VideoStorage($config);
        $ret = $push->push();
        ##push success
        if ($ret) {
            $ret = json_decode($ret, TRUE);
            return $ret['sourceid'];
        }
    }

    /**
     *获取字幕
     * @return mixed
     * @author zhuzhengqian
     */
    public function getCourseSubtitle(){
        $inputData = Input::all();
        $rule = array(
            'video_guid'=>'required',
            'language'=>'required',
            'type'=>'required'
        );
        $validator = Validator::make($inputData,$rule);
        if($validator->fails()){
            echo $validator->messages()->first();
            exit;
        }
        $video_guid = $inputData['video_guid'];
        $language = $inputData['language'];
        $type = $inputData['type'];

        if( ! $video_guid){
            return Response::json(array('msgCode'=>-1,'message'=>'param video_id is required','data'=>null));
        }
        $objVideo = \Video::where('guid',$video_guid)->first();

        if( ! $objVideo){
            return Response::json(array('msgCode'=>-2,'message'=>'video is not found','data'=>null));
        }

        $videoId = $objVideo->video_id;

        $objSubtitle = Subtitle::where('video_id',$videoId)->where('type','XML')->first();
        if( ! $objSubtitle){
            return Response::json(array('msgCode'=>-3,'message'=>'the subtitle of this video is not found','data'=>null));
        }
        $xmlContent = $objSubtitle->content;
        $xml2json = new \HiHo\Subtitle\Xml2Json();
        $xml2json->loadFromString($xmlContent);
        $arrSubtitle = json_decode($xml2json->convert(),TRUE);
        //highlight
        $highlight = Highlight::where('video_id',$videoId)->orderBy('st','ASC')->get();
        $arrRowHight = array();
        if(count($highlight)){
            foreach($highlight as $h){
                $arrHighlight = array();
                array_push($arrHighlight,$h['id'],round($h['st'],3),round($h['et'],3),$h['title'] ? $h['title'] : NULL,$h['description'] ? $h['description'] : NULL,$h['thumbnail'] ? $h['thumbnail'] : NULL);
                array_push($arrRowHight,$arrHighlight);
            }
        }
        $arrSubtitle['highlightSchema'] = array('id','st','et','title','brief','thumbnail');
        $arrSubtitle['highlights'] = $arrRowHight;
        //annotations
        $annotations = Annotation::where('video_id',$videoId)->orderBy('st','ASC')->get();
        $arrRowAnnoation = array();
        if(count($annotations)){
            foreach($annotations as $a){
                $arrAnnoation = array();
                array_push($arrAnnoation,$a['id'],round($a['st'],3),round($a['et'],3),$a['content'] ? $a['content'] : NULL);
                array_push($arrRowAnnoation,$arrAnnoation);
            }
        }
        $arrSubtitle['annotationSchema'] = array('id','st','et','content');
        $arrSubtitle['annotations'] = $arrRowAnnoation;
        //question
        $arrSubtitle['questionSchema'] = array('id','time','question','answer','choices','operation','operationDetail','gotoHighlightId');
        $arrSubtitle['choiceSchema'] = array('value','description');
        $question = Question::where('video_id',$videoId)->orderBy('time_point','ASC')->get();
        $arrRowQuestion = array();
        if(count($question)){
            foreach($question as $q){
                $arrQuestion = array();
                $highlightId = null;
                if($q['error_operation'] == 'goto'){
                    $operationDetail = array(round(Highlight::find($q['target_highlight_id'])->st,3),Highlight::find($q['target_highlight_id'])->thumbnail);
                    $highlightId = $q['target_highlight_id'];
                }elseif($q['error_operation'] == 'tips'){
                    $operationDetail = $q['tips_content'];
                }else{
                    $operationDetail = '';
                }
                $arrAnswer = unserialize($q['answers']);
                $arrA = array();
                if($arrAnswer){
                    foreach($arrAnswer as $ak=>$an){
                        array_push($arrA,array($ak,$an));
                    }
                }
                array_push($arrQuestion,$q['id'] ? $q['id'] : NULL,round($q['time_point'],3),$q['title'] ? $q['title'] : NULL,unserialize($q['correct_answers']),$arrA,$q['error_operation'],$operationDetail,$highlightId);
                array_push($arrRowQuestion,$arrQuestion);
            }
        }
        $arrSubtitle['questions'] = $arrRowQuestion;

        //reference
        $objReference = VideosReference::where('video_id',$videoId)->first();
        if($objReference){
            $arrSubtitle['appendix'] = $objReference->content;
        }else{
            $arrSubtitle['appendix'] = NULL;
        }

        return Response::json($arrSubtitle);
    }
}
