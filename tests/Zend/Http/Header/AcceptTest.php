<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Accept;

class AcceptTest extends \PHPUnit_Framework_TestCase
{

    public function testAcceptFromStringCreatesValidAcceptHeader()
    {
        $acceptHeader = Accept::fromString('Accept: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $acceptHeader);
        $this->assertInstanceOf('Zend\Http\Header\Accept', $acceptHeader);
    }

    public function testAcceptGetFieldNameReturnsHeaderName()
    {
        $acceptHeader = new Accept();
        $this->assertEquals('Accept', $acceptHeader->getFieldName());
    }

    public function testAcceptGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Accept needs to be completed');

        $acceptHeader = new Accept();
        $this->assertEquals('xxx', $acceptHeader->getFieldValue());
    }

    public function testAcceptToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Accept needs to be completed');

        $acceptHeader = new Accept();

        // @todo set some values, then test output
        $this->assertEmpty('Accept: xxx', $acceptHeader->toString());
    }

    /** Implmentation specific tests here */
    
}
