<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Allow;

class AllowTest extends \PHPUnit_Framework_TestCase
{

    public function testAllowFromStringCreatesValidAllowHeader()
    {
        $allowHeader = Allow::fromString('Allow: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $allowHeader);
        $this->assertInstanceOf('Zend\Http\Header\Allow', $allowHeader);
    }

    public function testAllowGetFieldNameReturnsHeaderName()
    {
        $allowHeader = new Allow();
        $this->assertEquals('Allow', $allowHeader->getFieldName());
    }

    public function testAllowGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Allow needs to be completed');

        $allowHeader = new Allow();
        $this->assertEquals('xxx', $allowHeader->getFieldValue());
    }

    public function testAllowToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Allow needs to be completed');

        $allowHeader = new Allow();

        // @todo set some values, then test output
        $this->assertEmpty('Allow: xxx', $allowHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

