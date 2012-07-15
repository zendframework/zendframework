<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\TestAsset;

class testclass
{
    public function returnFile()
    {
        return fopen(__DIR__ . "/testdata", "r");
    }
    public function returnCtx()
    {
        $opts = array(
            'http'=>array(
            'method'=>"GET",
            'header'=>"Accept-language: en\r\n" .
                "Cookie: foo=bar\r\n"
            )
        );
        $context = stream_context_create($opts);
        return $context;
    }
}

