<?php namespace HiHo\Subtitle;
use Whoops\Example\Exception;
/**
 *xml字幕转json
 * DateTime: 14-8-8 下午1:46
 * author zhengqian.zhu <zhengqian.zhu@autotiming.com>
 */

/*
|--------------------------------------------------------------------------
| demo
|--------------------------------------------------------------------------
|     ## load xml string
|     $objXml = new HiHo\Subtitle\Xml2Json();
      $objXml->loadFromString($xml);
      echo  $objXml->convert();
|
      ## load xml file
||    $objXml = new HiHo\Subtitle\Xml2Json();
      $objXml->loadFromFile(public_path().'/subtitle.xml');
      echo  $objXml->convert();
|
*/



    class Xml2Json{

        /**
         *xml对象
         * @var obj
         */
        public $objXml;

        /**
         *转换后json数据内容
         * @var string
         */
        public $json = '';


        public $arr;
        /**
         * constrcut
         */
        public function __construct(){

        }

        /**
         *转换程序（从文件转换）
         * @author zhuzhengqian
         * @return string
         */
        public function loadFromFile($filePath){
            $this->objXml = simplexml_load_file($filePath);

        }

        /**
         *从xml字符串转换
         * @param $fileString
         * @author zhuzhengqian
         */
        public function loadFromString($fileString){
            $this->objXml = simplexml_load_string($fileString);
        }


        public function loadFromArray($arr){
            $this->arr = $arr;
        }
        /**
         *开始转换
         * @author zhuzhengqian
         * @return json string
         */
        public function convert(){
            if( ! $this->objXml) \App::abort(400,'can not load xml object');
            $xml = $this->objXml;
            //数据保存数组
            $arrData = array();
            $arrSubtitle = array();

            //attributes
            $attributes = $xml->attributes();
            foreach($attributes as $k=>$v){
                $arrData[$k] = (string)$v;
                unset($k);
                unset($v);
            }
            $arrData['url'] = (string)$xml->d->url;
            $arrData['title'] = (string)$xml->d->title;
            $arrData['date'] = (string)$xml->d->date;
            //st ed token
            foreach($xml->d->p as $v){
                $arrP = array();
                foreach($v->u->w as $w){
                    array_push($arrP,array((string)$w->token,(float)$w->st,(float)$w->et,(string)$w->score));
                }
                array_push($arrSubtitle,$arrP);
            }
            $arrData['subtitleSchema'] = array('token','st','et','score');
            $arrData['subtitles'] = $arrSubtitle;

            $this->json = json_encode($arrData);

            return $this->json;
        }


        /**
         * #数组转为新的json格式
         * @author zhuzhengqian
         */
        public function array2Json(){
            $arrOutput = array();
            $arr = $this->arr['srt'];
            $arrOutput['subtitleSchema'] = array("token", "st", "et", "score");
            $arrSubtitle = array();
            foreach($arr as $row){
                $arrp = array();
                foreach($row as $word){
                    array_push($arrp,array($word['token'],$word['st'],$word['et'],(float)$word['score']));
                }
                array_push($arrSubtitle,$arrp);
            }
            $arrOutput['subtitles'] = $arrSubtitle;
            return json_encode($arrOutput);

        }


    }