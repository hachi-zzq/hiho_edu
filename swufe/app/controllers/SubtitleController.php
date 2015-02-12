<?php

use HiHo\Subtitle\Shear;
use HiHo\Sewise\LanguageCode;
use HiHo\Model\PlayID;
use HiHo\Subtitle\Xml2Json;

/**
 * 字幕
 * @package hiho.com
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class SubtitleController extends \BaseController
{

    /**
     * 获得字幕
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $guid
     * @param string $language
     * @param string $type
     * @return string
     */
    public function getSubtitle($video_guid, $language, $type)
    {
        $time = time();
        $interval = 3600 * 12; //12小时
        header('Last-Modified: ' . gmdate('r', $time));
        header('Expires: ' . gmdate('r', ($time + $interval)));
        header('Cache-Control: max-age=' . $interval, false);
        if (Cache::has(md5($video_guid))) {
            return Cache::get(md5($video_guid));
        }

        $video = Video::where('guid', '=', $video_guid)->first();
        if (!$video) {
            App::abort(404, 'Video does not exist');
        }

        // 转换语言代码查询
        $langCode = new LanguageCode($language);
        $langCode = $langCode->getTargetCode();


        if ($langCode) {
            $subtitle = Subtitle::where('video_id', '=', $video->video_id)
                ->where('type', '=', $type)
                ->where('language', '=', $langCode)
                ->first();
        } else {
            $subtitle = Subtitle::where('video_id', '=', $video->video_id)
                ->where('type', '=', $type)
                ->first();
        }

        if ($subtitle) {
            Cache::forever(md5($video_guid), $subtitle->content);
            return $subtitle->content;
        } else {
            return App::abort(404, 'Subtitles does not exist');
        }
    }


    public function getSubtitleV2()
    {
        $inputData = Input::all();
        $rule = array(
            'video_guid' => 'required',
            'language' => 'required',
            'type' => 'required'
        );
        $validator = Validator::make($inputData, $rule);
        if ($validator->fails()) {
            echo $validator->messages()->first();
            exit;
        }
        $video_guid = $inputData['video_guid'];
        $language = $inputData['language'];
        $type = $inputData['type'];
//        //新版getSubtitle
        if (isset($inputData['version']) && $inputData['version'] == '2') {
            return $this->getCourseSubtitle($video_guid, $language, $type);
        }
        return $this->getSubtitle($video_guid, $language, $type);
    }


    /**
     *返回前端的ajax字幕信息，包括问题等等
     * @author zhuzhengqian
     */
    public function getCourseSubtitle($video_guid = null, $language = 'en', $type = 'JSON')
    {
        if (!$video_guid) {
            return Response::json(array('msgCode' => -1, 'message' => 'param video_id is required', 'data' => null));
        }
        $objVideo = \Video::where('guid', $video_guid)->first();

        if (!$objVideo) {
            return Response::json(array('msgCode' => -2, 'message' => 'video is not found', 'data' => null));
        }

        $videoId = $objVideo->video_id;

        $objSubtitle = Subtitle::where('video_id', $videoId)->where('type', 'XML')->first();
        if (!$objSubtitle) {
            return Response::json(array('msgCode' => -3, 'message' => 'the subtitle of this video is not found', 'data' => null));
        }
        $xmlContent = $objSubtitle->content;
        $xml2json = new \HiHo\Subtitle\Xml2Json();
        $xml2json->loadFromString($xmlContent);
        $arrSubtitle = json_decode($xml2json->convert(), TRUE);
        //highlight

        $highlight = Highlight::where('video_id', $videoId)->orderBy('st', 'ASC')->get();

        $arrRowHight = array();
        if (count($highlight)) {
            foreach ($highlight as $h) {
                $arrHighlight = array();
                array_push($arrHighlight, round($h['st'], 3), round($h['et'], 3), $h['title'] ? $h['title'] : NULL, $h['description'] ? $h['description'] : NULL, $h['thumbnail'] ? $h['thumbnail'] : NULL);
                array_push($arrRowHight, $arrHighlight);
            }
        }
        $arrSubtitle['highlightSchema'] = array('st', 'et', 'title', 'brief', 'thumbnail');
        $arrSubtitle['highlights'] = $arrRowHight;
        //annotations

        $annotations = Annotation::where('video_id', $videoId)->orderBy('st', 'ASC')->get();

        $arrRowAnnoation = array();
        if (count($annotations)) {
            foreach ($annotations as $a) {
                $arrAnnoation = array();
                array_push($arrAnnoation, round($a['st'], 3), round($a['et'], 3), $a['content'] ? $a['content'] : NULL);
                array_push($arrRowAnnoation, $arrAnnoation);
            }
        }
        $arrSubtitle['annotationSchema'] = array('st', 'et', 'content');
        $arrSubtitle['annotations'] = $arrRowAnnoation;
        //question

        $arrSubtitle['questionSchema'] = array('id', 'time', 'question', 'answer', 'choices', 'operation', 'operationDetail');
        $arrSubtitle['choiceSchema'] = array('value', 'description');
        $question = Question::where('video_id', $videoId)->orderBy('time_point', 'ASC')->get();

        $arrRowQuestion = array();
        if (count($question)) {
            foreach ($question as $q) {
                $arrQuestion = array();
                if ($q['error_operation'] == 'goto') {
                    $operationDetail = array(round(Highlight::find($q['target_highlight_id'])->st, 3), Highlight::find($q['target_highlight_id'])->thumbnail);
                } elseif ($q['error_operation'] == 'tips') {
                    $operationDetail = $q['tips_content'];
                } else {
                    $operationDetail = '';
                }
                $arrAnswer = unserialize($q['answers']);
                $arrA = array();
                if ($arrAnswer) {
                    foreach ($arrAnswer as $ak => $an) {
                        array_push($arrA, array($ak, $an));
                    }
                }
                array_push($arrQuestion, $q['id'] ? $q['id'] : NULL, round($q['time_point'], 3), $q['title'] ? $q['title'] : NULL, unserialize($q['correct_answers']), $arrA, $q['error_operation'], $operationDetail);
                array_push($arrRowQuestion, $arrQuestion);
            }
        }
        $arrSubtitle['questions'] = $arrRowQuestion;

        //reference
        $objReference = VideosReference::where('video_id', $videoId)->first();
        if ($objReference) {
            $arrSubtitle['appendix'] = $objReference->content;
        } else {
            $arrSubtitle['appendix'] = NULL;
        }

        return Response::json($arrSubtitle);
    }

    /**
     * 获得碎片字幕
     * @author Luyu<luyu.zhang@autotiming.com>
     * @param $video_guid
     * @param string $st
     * @param string $et
     * @param string $language
     * @param string $returnType
     */
    public function getFragmentSubtitle($video_guid, $st = 'start', $et = 'end', $language = 'en', $returnType = 'JSON')
    {
        // 转换语言代码查询
        $langCode = new LanguageCode($language);
        $langCode = $langCode->getTargetCode();

        // 查找视频
        $video = Video::where('guid', '=', $video_guid)->first();

        if (!$video) {
            App::abort(404, 'Not found video.');
        }

        // 查找 JSON 字幕
        $subtitle = Subtitle::where('video_id', '=', $video->video_id)
            ->where('type', '=', 'JSON')
            ->where('language', '=', $langCode)
            ->first();
        if (!$subtitle) {
            App::abort(404, 'Not found subtitle.');
        }

        // 电锯杀人狂来了
        $shear = new Shear();
        $shear->loadVideo($video);
        $shear->loadSubtitle($subtitle);
        $shear->clip($st, $et);
        $result = $shear->output($returnType);
        unset($shear);

        /*
        $response = Response::make($result, $statusCode=200);
        $response->header("Accept-Ranges","bytes");

        if($returnType == "json"){
        	$response->header("Content-Type", "application/json");
        }elseif ($returnType == "srt"){
			$response->header("Content-Type", "application/x-subrip");
        }else{
        	$response->header("Content-Type", "application/xml");
        }
        return $response;
        */
        return $result;

    }

    /**
     * 获得碎片字幕V2
     * @author Hanxiang<hanxiang.qiu@autotiming.com> zhuzhengqian<....>
     * @param $video_guid
     * @param string $st
     * @param string $et
     * @param string $language
     * @param string $returnType
     */
    public function getFragmentSubtitleV2() {
        $inputData = Input::all();
        $rule = array(
            'video_playid' => 'required',
            'language' => 'required',
            'type' => 'required',
            'st' => 'required',
            'et' => 'required'
        );

        $validator = Validator::make($inputData, $rule);
        if ($validator->fails()) {
            return Response::json(array('msgCode'=>-1,'message'=>'没有传递必要参数','data'=>$validator->messages()->first()));
        }
        $playId = $inputData['video_playid'];
        $language = $inputData['language'];
        $type = $inputData['type'];
        $st = $inputData['st'];
        $et = $inputData['et'];

        //get videoguid
        $fragment = PlayID::where('entity_type','VIDEO')->where('play_id',$playId)->first();
        if( ! $fragment){
            return Response::json(array('msgCode'=>-2,'message'=>'entity_type不存在','data'=>NULL));
        }


        $objVideo = Video::find($fragment->entity_id);
        if(!$objVideo){
            return Response::json(array('msgCode'=>-4,'message'=>'视频不存在','data'=>NULL));
        }
        $subtitle = $this->getFragmentSubtitle($objVideo->guid,$st,$et,$language,$type);
        $arrSubtitle = json_decode($subtitle, true);
        $xml2json = new Xml2Json();
        $xml2json->loadFromArray($arrSubtitle);
        echo $xml2json->array2Json();
    }
}
