<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection\TestAsset;

class TestSampleClass11
{
    public function doSomething() {
        return 'doSomething';
    }

    public function doSomethingElse($one, $two = 2, $three = 'three') { return 'doSomethingElse'; }
    
    public function doSomethingAgain()
    {
        $closure = function($foo) { return $foo; };
        return 'doSomethingAgain';
    }
    
    protected static function doStaticSomething()
    {
        return 'doStaticSomething';
    }
}
