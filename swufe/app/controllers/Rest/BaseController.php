<?php namespace HiHo\Edu\Controller\Rest;

use HiHo\Model\RestLog;
use \log;

/**
 * RestAPI 基类
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class BaseController extends \BaseController
{

    //protected $layout = 'layouts.rest';

    const WEIBO_APP_KEY = '2166219604';
    const PAGE_SIZE     = 15;

    /**
     * 接口首页
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function index()
    {
        $input = \Input::only('token');

        if (isset($input['token'])) {
            // Token 测试
            $user_id = $this->verifyToken($input['token']);
            return $this->encodeResult('10000', 'This is the default function, Hello' . $user_id . '!');
        } else {
            return $this->encodeResult('10000', 'This is the default function.');
        }

    }

    /**
     * 编码统一返回格式
     * @author Luyu<luyu.zhang@autotiming.com> Zhengqian Zhu<Zhengqian Zhu@autotiming.com>
     * @param $msgcode
     * @param $message
     * @param $response
     */
    protected function encodeResult($msgcode, $message = NULL, $response = NULL)
    {
        /**
         * 记录接口的 Requset 和返回值
         *取消数据库记录的方式，直接采用日志文件记载
         * @author zhuzhengqian
         */
        $logFilePath = storage_path().'/logs/restApi.log';
        if(is_file($logFilePath) && filesize($logFilePath) > 1024*1024*10) //大于10M
        {
        rename($logFilePath,storage_path().'/logs/restApi_'.date('YmdHis').'.log');
        $logFilePath = storage_path().'/logs/restApi.log';
        }
        Log::useFiles($logFilePath);
        $restData = array();
        $restData['id'] = \Uuid::v4();
        $restData['request'] = serialize(\Input::all());
        $restData['request_route'] = \Route::currentRouteName();
        $restData['user_id'] = \Auth::guest() ? '':\Auth::user()->user_id;
        $restData['client_useragent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
        $restData['client_ip'] = \Request::getClientIp();
        $restData['msgcode'] = $msgcode;
        $restData['message'] = $message;
        Log::info('RestApi info',$restData);
//        $log = new RestLog();
//
//        // TODO:Serialization of 'Closure' is not allowed
//
//        $log->request = serialize(\Input::all());
//        $log->request_route = \Route::currentRouteName();
//        // $log->response = serialize($response);
//        $log->msgcode = $msgcode;
//        $log->message = $message;
//        $log->client_ip = \Request::getClientIp();
//        $log->client_useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
//
//        if (\Auth::check()) {
//            $log->user_id = \Auth::user()->user_id;
//        }
//
//        $log->save();

        /**
         * 返回 JSON 的统一返回值结构
         */

        $result = array(
            'request_id' => $restData['id'],
            'msgcode' => $msgcode,
            'message' => $message,
            'response' => $response,
            'version' => $option = \Config::get('hiho_news.restapi.version'),
            'servertime' => time()
        );

        return json_encode($result);
    }

    /**
     * 验证 Token 并使其登录
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    protected function verifyToken($tokenStr)
    {
        // TODO: Redis 缓存 Token

        // 判断 Token 有效性
        $token = \UserToken::where('token', '=', $tokenStr)->first();
        if (!$token) {
            return FALSE;
        }

        // 延长 Token 的有效期
        $today = new \Datetime();
        $modifier = '+3 days';
        $token->caycle_at = $today->modify($modifier);
        $token->save();

        // 登录身份
        $user = \User::find($token->user_id);
        if (!$user) {
            return FALSE;
        }

        \Auth::login($user);

        return $user->user_id;
    }

    /**
     * 发起API请求
     * @author Hanxiang<hanxiang.qiu@autotiming.com>
     */
    protected function getCurlResponse($url, $is_POST = true, $data = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($is_POST) {
            curl_setopt($ch, CURLOPT_POST, $is_POST);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $response = curl_exec($ch);

        if(curl_errno($ch)){//出错则显示错误信息
            print curl_error($ch);
        }

        curl_close($ch);

        $res_data = json_decode($response, true);
        return $res_data;
    }

}