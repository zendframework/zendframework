<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Host;

class HostTest extends \PHPUnit_Framework_TestCase
{

    public function testHostFromStringCreatesValidHostHeader()
    {
        $hostHeader = Host::fromString('Host: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $hostHeader);
        $this->assertInstanceOf('Zend\Http\Header\Host', $hostHeader);
    }

    public function testHostGetFieldNameReturnsHeaderName()
    {
        $hostHeader = new Host();
        $this->assertEquals('Host', $hostHeader->getFieldName());
    }

    public function testHostGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Host needs to be completed');

        $hostHeader = new Host();
        $this->assertEquals('xxx', $hostHeader->getFieldValue());
    }

    public function testHostToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Host needs to be completed');

        $hostHeader = new Host();

        // @todo set some values, then test output
        $this->assertEmpty('Host: xxx', $hostHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

