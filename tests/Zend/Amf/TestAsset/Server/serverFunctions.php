<?php

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

