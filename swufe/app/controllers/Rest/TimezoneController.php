<?php namespace HiHo\Edu\Controller\Rest;

use \DateTimeZone;

/**
 * RestAPI 时区
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class TimezoneController extends BaseController
{

    /**
     * 获得时区列表
     * @author Luyu<luyu.zhang@autotiming.com>
     * @return string
     */
    public function getIndex()
    {
        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        return $this->encodeResult('10603', 'Succeed', array('tzlist' => $tzlist));
    }

}