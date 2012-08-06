<?php

namespace ZendTest\Code\Reflection\TestAsset;

use ZendTest\Code\Reflection\TestAsset\SampleAnnotation as Sample;

class TestSampleClass2 implements \IteratorAggregate
{
    protected $_prop1 = null;

    /**
     * @Sample({"foo":"bar"})
     */
    protected $_prop2 = null;

    public function getProp1()
    {
        return $this->_prop1;
    }

    public function getProp2($param1, TestSampleClass $param2)
    {
        return $this->_prop2;
    }

    public function getIterator()
    {
        return array();
    }

}
