<?php namespace HiHo\Edu\Controller\Rest;

/**
 * RestAPI 系统
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class SystemController extends BaseController
{

    /**
     * 获得系统状态
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string
     */
    public function getStatus()
    {
        // 返回此用户所有收藏的视频
        $status = array(
            'if_subscribe_need_pay' => false
        );
        return $this->encodeResult('10000', 'Succeed', $status);
    }

}