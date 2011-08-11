<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Vary;

class VaryTest extends \PHPUnit_Framework_TestCase
{

    public function testVaryFromStringCreatesValidVaryHeader()
    {
        $varyHeader = Vary::fromString('Vary: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $varyHeader);
        $this->assertInstanceOf('Zend\Http\Header\Vary', $varyHeader);
    }

    public function testVaryGetFieldNameReturnsHeaderName()
    {
        $varyHeader = new Vary();
        $this->assertEquals('Vary', $varyHeader->getFieldName());
    }

    public function testVaryGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Vary needs to be completed');

        $varyHeader = new Vary();
        $this->assertEquals('xxx', $varyHeader->getFieldValue());
    }

    public function testVaryToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Vary needs to be completed');

        $varyHeader = new Vary();

        // @todo set some values, then test output
        $this->assertEmpty('Vary: xxx', $varyHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

