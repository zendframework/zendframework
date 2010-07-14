<?php

namespace ZendTest\Amf\TestAsset\Server;

/**
 * Class with private constructor
 */
class testclassPrivate
{
    private function __construct()
    {
    }

     /**
     * Test1
     *
     * Returns 'String: ' . $string
     *
     * @param string $string
     * @return string
     */
    public function test1($string = '')
    {
        return 'String: '. (string) $string;
    }

    public function hello()
    {
        return "hello";
    }
}

