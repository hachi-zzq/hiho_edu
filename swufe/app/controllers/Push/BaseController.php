<?php namespace HiHo\Controller\Push;
/**
 * Class BaseController
 * @author ZhuJun<jun.zhu@autotiming.com>
 */
class BaseController extends \BaseController
{

    function __construct()
    {

    }

    protected function encodeResult($msgcode, $message = NULL, $response = NULL)
    {
        /**
         * 记录接口的 Requset 和返回值
         */
        $log = new \RestLog();

        $log->type = 20;
        $log->request = serialize(\Input::all());
        $log->request_route = \Route::currentRouteName();
        $log->response = serialize($response);
        $log->msgcode = $msgcode;
        $log->message = $message;
        $log->client_ip = \Request::getClientIp();
        $log->client_useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;

        $log->save();

        /**
         * 返回 JSON 的统一返回值结构
         */
        $result = array(
            'request_id' => $log->id,
            'msgcode' => $msgcode,
            'message' => $message,
            'response' => $response,
            'version' => $option = \Config::get('hiho.pushapi.version'),
            'servertime' => time()
        );

        return json_encode($result);
    }


}