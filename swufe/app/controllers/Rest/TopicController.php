<?php namespace HiHo\Edu\Controller\Rest;
/**
 * Created with JetBrains PhpStorm.
 * User: zhu
 * DateTime: 14-7-2 下午4:19
 * Email:www321www@126.com
 */
class TopicController extends BaseController
{

    /**
     * 主题索引
     * @param null
     * @return mixed
     */
    public function getIndex()
    {
        $objTopics = \Topic::all();
        return $this->encodeResult('12600', 'success', $objTopics->toArray());
    }
}
