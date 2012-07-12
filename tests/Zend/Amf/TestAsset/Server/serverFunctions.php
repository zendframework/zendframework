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
 * testFunction
 *
 * Function for use with Amf server unit tests
 *
 * @param array $var1
 * @param string $var2
 * @return string
 */
function testFunction($var1, $var2 = 'optional')
{
    return $var2 . ': ' . implode(',', (array) $var1);
}

/**
 * testFunction2
 *
 * Function for use with Amf server unit tests
 *
 * @return string
 */
function testFunction2()
{
    return 'function2';
}

