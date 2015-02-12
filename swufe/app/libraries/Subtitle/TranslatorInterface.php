<?php namespace HiHo\Subtitle;

/**
 * User: luyu
 * Date: 13-11-14
 * Time: 上午9:43
 */

interface TranslatorInterface
{
    public function translate();

    public function mergeResult($result);

    public function clipOriginal($original);

}