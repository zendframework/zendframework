<?php

namespace ZendTest\Stdlib\TestAsset;

use Zend\Stdlib\Options;

/**
 * Dummy TestOptions used to test Stdlib\Options
 */
class TestOptions extends Options
{
    protected $testField;
    
    public function setTestField($value) 
    {
        $this->testField = $value;
    }
    
    public function getTestField()
    {
        return $this->testField;
    }
}
