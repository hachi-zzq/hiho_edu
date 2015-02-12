<?php namespace HiHo\Edu\Controller\Rest;

/**
 * RestAPI 语言
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class LanguageController extends BaseController
{

    /**
     * 分类列表
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string|void
     */
    public function getIndex()
    {
        // 返回此用户所有收藏的视频
        $languages = \Language::all();
        return $this->encodeResult('10602', 'Succeed', $languages->toArray());
    }

}