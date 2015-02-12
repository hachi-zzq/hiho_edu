<?php
/**
 * User: luyu
 * Date: 13-11-18
 * Time: 下午11:53
 */

namespace HiHo\Subtitle;

interface SubtitleInterface
{
    public function video();

    public function getContentJson();

    public function getLanguage();

} 