<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection\TestAsset;

class TestSampleClass12
{
    /**
     *
     * @param  int    $one
     * @param  int    $two
     * @return string
     */
    protected function doSomething(&$one, $two)
    {
        return 'mixedValue';
    }
}
