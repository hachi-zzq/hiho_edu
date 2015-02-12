<?php namespace HiHo\Edu\Controller\Rest;

/**
 * 关键词相关
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class KeywordController extends BaseController
{

    /**
     * 关键词列表, 按搜索次数排序
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string|void
     */
    public function getIndex()
    {
        // 表单验证规则
        $input = \Input::only('order_by');
        $rules = array(
            'order_by' => array(''),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // `search` 为按搜索热度倒叙, `time` 为按时间倒叙

        if($input['order_by'] == 'search'){
            $keywords = \SearchHotKeyword::orderBy('rank','desc')->get();
        }elseif($input['order_by'] == 'time'){
            $keywords = \SearchHotKeyword::orderBy('created_at','desc')->get();
        }else{
            $keywords = \SearchHotKeyword::all();
        }

        return $this->encodeResult('11001', 'Succeed', $keywords->toArray());
    }

    /**
     * 获得单个关键词信息, 搜索次数、订阅价格等
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string|void
     */
    public function getShow()
    {
        // 表单验证规则
        $input = \Input::only('key');
        $rules = array(
            'key' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 不存在则创建
        $keyword = \Keyword::existOrCreateByKey($input['key']);
        $keyword->subcribed = 1; // TODO: 已订阅数量

        if (!$keyword) {
            return $this->encodeResult('21001', 'Not found');
        }

        return $this->encodeResult('11002', 'Succeed', $keyword->toArray());
    }

    /**
     * TODO: 搜索自动完成
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getSuggestion()
    {
        // 表单验证规则
        $input = \Input::only('key');
        $rules = array(
            'inputting' => array('required'),
            'language' => array(''),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

    }

}