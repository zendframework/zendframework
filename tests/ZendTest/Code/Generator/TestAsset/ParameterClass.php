<?php

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
