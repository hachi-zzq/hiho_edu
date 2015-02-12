<?php namespace HiHo\Search;

use HiHo\Model\Word;
use HiHo\Model\WordNexus;
use HiHo\Other\Pinyin;

/**
 * Created by PhpStorm.
 * User: Andy Lee
 * Date: 14-4-1
 * Time: 上午10:48
 */
class Suggestion
{

    public $param;
    public $result;
    public $bind;


    public function __construct($str, $bind = "")
    {
        $this->param = $str;
    }

    /**
     * 搜索关键词安全过滤
     * @param $str
     * @return bool
     */
    public function _checkKeyWord($str)
    {
        if (empty ($str)) {
            return false;
        }

        if (!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', $str)) {
            return false;
        }

        return $this->param = trim($str);
    }

    /**
     * 列出关键词搜索建议
     * @return string
     */
    public function queryWithParam()
    {

        $check = $this->_checkKeyWord($this->param);

        if ($check) {
            $result = Word::where("word", "like", "%" . $this->param . "%")->select("word")->
                OrderBy("use_count", "DESC")->first();

            if (!empty($result)) {
                $result = Word::where("word", "like", "%" . $this->param . "%")->select("word", "id")->
                    OrderBy("use_count", "desc")->take(10)->get();

                $this->result = $this->buildResult($result);
            } else {
                // 如果该关键词不存在，就存入
                $w = $this->addNewWord();
                $this->result = $this->buildResult($w);
            }
        } else {
            $this->result = $this->buildResult("");
        }

        return $this->result;
    }

    /**
     * 创建意见推荐结果
     * @param result
     * @return string
     */
    public function buildResult($result)
    {
        if (empty($result)) {

            $suggestion = array(
                "queryParam" => $this->param,
                "result" => ""
            );
            return $suggestion;

        } else {
            $res_arr = array();

            foreach ($result as $k) {
                $res_arr[$k->id] = $k->word;
            }
            $suggestion = array(
                "queryParam" => $this->param,
                "result" => $res_arr
            );

        }

        return $suggestion;
    }

    /**
     *  增加关键词使用量
     */
    public function addSearchResultCount()
    {
        $keyWord = Word::find($this->param);
        if (empty($keyWord)) {
            $keyWord = $this->addNewWord();
        } else {
            $keyWord->use_count = $keyWord->use_count + 1;
            $keyWord->save();
        }

        return $keyWord;
    }

    /***
     * 绑定关键词对应关系
     *
     **/
    public function bindKeywords($w, $p)
    {
        $argc = explode("|||", $p);

        if (count($argc) == 2 and is_numeric($w)) {

            $nexus = WordNexus::whereRaw("current_id = ? and bind_id = ?", array($w, $argc[1]))->first();

            if (!empty($nexus)) {
                $n = Nexus::find($nexus->id);
                $n->bind_count = $n->bind_count + 1;
                $n->save();

            } else {
                $nexus = new Nexus();
                $nexus->current_id = $w;
                $nexus->bind_id = $argc[1];
                $nexus->bind_count = 0;
                $nexus->save();
            }
        }
    }

    /**
     *  增加新的搜索关键词
     */
    public function addNewWord()
    {

        $check = $this->_checkKeyWord($this->param);
        if ($check) {

            $keyword = new Word();
            $keyword->word = $this->param;
            $keyword->pinyin = "";
            $keyword->use_count = 1;
            $keyword->type = 1;
            $keyword->language = "zh_CN";
            $keyword->save();

            // return object
            $k = new \stdClass();
            $k->id = $keyword->id;
            $k->word = $this->param;
            return array("0" => $k);

        } else {
            return "";
        }


    }

}
