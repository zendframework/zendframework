<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\KeepAlive;

class KeepAliveTest extends \PHPUnit_Framework_TestCase
{

    public function testKeepAliveFromStringCreatesValidKeepAliveHeader()
    {
        $keepAliveHeader = KeepAlive::fromString('Keep-Alive: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $keepAliveHeader);
        $this->assertInstanceOf('Zend\Http\Header\KeepAlive', $keepAliveHeader);
    }

    public function testKeepAliveGetFieldNameReturnsHeaderName()
    {
        $keepAliveHeader = new KeepAlive();
        $this->assertEquals('Keep-Alive', $keepAliveHeader->getFieldName());
    }

    public function testKeepAliveGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('KeepAlive needs to be completed');

        $keepAliveHeader = new KeepAlive();
        $this->assertEquals('xxx', $keepAliveHeader->getFieldValue());
    }

    public function testKeepAliveToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('KeepAlive needs to be completed');

        $keepAliveHeader = new KeepAlive();

        // @todo set some values, then test output
        $this->assertEmpty('Keep-Alive: xxx', $keepAliveHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

