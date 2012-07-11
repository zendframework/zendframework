<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

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

