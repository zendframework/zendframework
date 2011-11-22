<?php

namespace ZendTest\Stdlib;

use ArrayObject,
    ZendTest\Stdlib\TestAsset\TestOptions,
    ZendTest\Stdlib\TestAsset\TestTraversable,
    Zend\Stdlib\Exception\InvalidArgumentException;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructionWithArray()
    {
        $options = new TestOptions(array('test_field' => 1));
        
        $this->assertEquals(1, $options->test_field);
    }
    
    public function testConstructionWithTraversable()
    {
        $config = new ArrayObject(array('test_field' => 1));
        $options = new TestOptions($config);
        
        $this->assertEquals(1, $options->test_field);
    }
    
    public function testConstructionWithNull()
    {
        try {
            $options = new TestOptions(null);
        } catch(InvalidArgumentException $e) {
            $this->fail("Unexpected InvalidArgumentException raised");
        }
    }
    
    public function testUnsetting()
    {
        $options = new TestOptions(array('test_field' => 1));
        
        $this->assertEquals(true, isset($options->test_field));
        unset($options->testField);
        $this->assertEquals(false, isset($options->test_field));
        
    }
}
