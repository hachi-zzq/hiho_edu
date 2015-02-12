<?php namespace HiHo\Edu\Controller\Rest;

/**
 * RestAPI 国家
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class CountryController extends BaseController
{

    /**
     * 国家列表
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string|void
     */
    public function getIndex()
    {
        // 返回此用户所有收藏的视频
        $countries = \Country::all();
        return $this->encodeResult('10601', 'Succeed', $countries->toArray());
    }

}