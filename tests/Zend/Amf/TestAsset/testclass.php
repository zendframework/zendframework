<?php

namespace ZendTest\Amf\TestAsset;

class testclass {
    function returnFile()
    {
        return fopen(__DIR__ . "/testdata", "r");
    }
    function returnCtx()
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

