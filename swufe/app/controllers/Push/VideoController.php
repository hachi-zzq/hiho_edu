<?php

namespace HiHo\Controller\Push;

use HiHo\Sewise\Pusher;

/**
 * Class BaseController
 * @author ZhuJun<jun.zhu@autotiming.com>
 */
class VideoController extends BaseController
{

    /**
     * Push Create
     * @return type
     */
    function create()
    {
        // 接收参数
        $input = \Input::only('data');

        $pusher = new Pusher();
        $result = $pusher->create($input['data']);

        return $result;
    }

    function delete()
    {
        $input = \Input::only('id', 'lang');

        $pusher = new Pusher();
        $result = $pusher->delete($input);
        return $result;
    }

    function modify()
    {
        $input = \Input::only('method', 'id', 'lang', 'data');
        $pusher = new Pusher();
        $result = $pusher->modify($input);
        return $result;
    }

    /**
     * 填充关键帧方法，一次性，勿调用！！
     */
    public function dojson(){

        $subtitle = \Subtitle::where('type','JSON')->whereIn('accuracy',array(1,2))->get();

        foreach($subtitle as $item){

            $json = @file_get_contents($item->url);

            \Subtitle::where('id', $item->id)->update(array('content' => $json));

        }

    }

}
