<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Referer;

class RefererTest extends \PHPUnit_Framework_TestCase
{

    public function testRefererFromStringCreatesValidRefererHeader()
    {
        $refererHeader = Referer::fromString('Referer: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $refererHeader);
        $this->assertInstanceOf('Zend\Http\Header\Referer', $refererHeader);
    }

    public function testRefererGetFieldNameReturnsHeaderName()
    {
        $refererHeader = new Referer();
        $this->assertEquals('Referer', $refererHeader->getFieldName());
    }

    public function testRefererGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Referer needs to be completed');

        $refererHeader = new Referer();
        $this->assertEquals('xxx', $refererHeader->getFieldValue());
    }

    public function testRefererToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Referer needs to be completed');

        $refererHeader = new Referer();

        // @todo set some values, then test output
        $this->assertEmpty('Referer: xxx', $refererHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

