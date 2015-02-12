<?php namespace HiHo\Subtitle;
use Whoops\Example\Exception;

/**
 *srt->txt
 * @param srt
 * @author zhuzhengqian
 */
class Srt2Txt{

    /**
     *srt path
     */
    private $srtPath = '';

    /**
     * srt内容
     *srt content
     */
     private $srtContent = '';

    /**
     *字幕内容
     *
     */
    private $TxtContent = '';


    /**
     * contruct function
     */
    public function __construct($srtPath){
        $this->srtPath = $srtPath;
        try{
            $this->srtContent = file_get_contents($srtPath);
        }catch (Exception $e){
            echo $e->getFile().'--'.$e->getMessage();
            exit;
        }
    }

    /**
     *返回srt字幕文本内容
     */
    public function getSrtContent(){
        return $this->srtContent;
    }

    /**
     *转换函数
     * srt -> txt
     */
    public function convert(){
        $arrFile = file($this->srtPath);
        if($arrFile){
            foreach($arrFile as $k=>&$v){
                $v = rtrim($v,"\r\n");
                if(preg_match('/^[\d]{1,5}$/',$v) || preg_match('/([\d]{2}:){2}[\d]{2},[\d]{2,}\s*-->\s*([\d]{2}:){2}[\d]{2},[\d]{2,}/',$v) || empty($v)){
                    unset($arrFile[$k]);
                }
            }
        }
        if($arrFile){
            $this->TxtContent = implode("\n",$arrFile);
        }
        return $this->TxtContent;
    }

    /**
     *取得匹配后的txt
     */
    public function getTxtContent(){
        return $this->TxtContent;
    }

    /**
     *保存txt
     */
    public function saveTxt($filePath){
        if( ! is_dir(dirname($filePath))){
            mkdir($filePath);
        }
        file_put_contents($filePath,$this->TxtContent);
        return true;
    }

}

