<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection\TestAsset;

/**
 * /!\ Don't fix this file with the coding style.
 * The class Zend\Code\Reflection\FunctionReflection must parse a lot of closure formats
 */
class TestSampleClass11
{
    /**
     * Doc block doSomething
     * @return string
     */
    public function doSomething()
    {
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

    public function inline1() { return 'inline1'; } public function inline2() { return 'inline2'; } public function inline3() { return 'inline3'; }

    /**
     * Awesome doc block
     */
    public function emptyFunction() {}

    public function visibility()
    {
        return 'visibility';
    }

    function getCacheKey() {
        $args = func_get_args();
 
        $cacheKey = '';
 
        foreach($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $argElement) {
                    $cacheKey = hash("sha256", $cacheKey.$argElement);
                }
            }
            else {
                $cacheKey = hash("sha256", $cacheKey.$arg);
            }
            //blah
        }
 
        return $cacheKey;
    }
 
//    //TODO - would it be better to define the binding like this?
//    function __prototype() {
//        $cacheKey = $this->getCacheKey($queryString);
//        $cachedValue = $this->cache->get($cacheKey);
//
//        if ($cachedValue) {
//            return $cachedValue;
//        }
//        $result = parent::__prototype();
//        $this->cache->put($cacheKey, $result);
//        return $result;
//    }    
}
