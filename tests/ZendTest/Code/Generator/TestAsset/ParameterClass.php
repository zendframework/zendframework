<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator\TestAsset;

class ParameterClass
{
    public function name($param)
    {

    }

    public function type(\stdClass $bar)
    {

    }

    public function reference(&$baz)
    {

    }

    public function defaultValue($value = "foo")
    {
    }

    public function defaultNull($value = null)
    {

    }

    public function fromArray(array $array)
    {

    }

    public function defaultArray($array = array())
    {

    }

    public function defaultFalse($val = false)
    {

    }

    public function defaultTrue($val = true)
    {

    }

    public function defaultZero($number = 0)
    {

    }

    public function defaultNumber($number = 1234)
    {

    }

    public function defaultFloat($float = 1.34)
    {

    }

    public function defaultArrayWithValues($array = array(0 => 1, 1 => 2, 2 => 3))
    {

    }

    const FOO = "foo";

    public function defaultConstant($con = self::FOO)
    {

    }

    /**
     * @param int $integer
     */
    public function hasNativeDocTypes($integer)
    {

    }
}
