<?php
/**
 * User: luyu
 * Date: 13-11-18
 * Time: 下午4:30
 */

namespace HiHo\Sewise;

/**
 * 语言代码转换
 * Class LanguageCode
 * @package HiHo\Sewise
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class LanguageCode
{

    private $originalCode;

    private $targetCode;

    function __construct($originalCode)
    {
        $this->originalCode = $originalCode;
        $this->switchCode();
    }

    /**
     * @return mixed
     */
    public function getTargetCode()
    {
        return $this->targetCode;
    }

    /**
     * 转换语言代码
     * 媒资的语言代码是使用的 zh_cn 这种格式的，而非 ISO 标准格式
     * @author Luyu<luyu.zhang@autotiming.com>
     */
    public function switchCode()
    {
        $code = str_replace('-', '_', $this->originalCode); // zh-CN -> zh_CN
        $code = strtolower($code);
        $this->targetCode = str_replace('en_US', 'en', $code); // en_US -> en
    }

} 