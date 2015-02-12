<?php namespace HiHo\Edu\Controller\Rest;

/**
 * RestAPI 分类
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class CategoryController extends BaseController
{

    /**
     * 分类列表
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string|void
     */
    public function getIndex()
    {
        // 表单验证规则
        $input = \Input::only('token', 'parent_id');
        $rules = array(
            'parent_id' => array(),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        // 返回此用户所有收藏的视频
        $categories = \Category::all();
        return $this->encodeResult('10301', 'Succeed', $categories->toArray());
    }

    /**
     * 分类详情
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function getShow()
    {
        // 表单验证规则
        $input = \Input::only('id');
        $rules = array(
            'id' => array('required'),
        );
        $v = \Validator::make($input, $rules);

        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        $category = Video::find($input['id']);

        if (!$category) {
            return $this->encodeResult('20301', 'Not found');
        }

        return $this->encodeResult('10302', 'Succeed', $category->toArray());

    }

}