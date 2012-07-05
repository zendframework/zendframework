<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{

    public function testServerFromStringCreatesValidServerHeader()
    {
        $serverHeader = Server::fromString('Server: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $serverHeader);
        $this->assertInstanceOf('Zend\Http\Header\Server', $serverHeader);
    }

    public function testServerGetFieldNameReturnsHeaderName()
    {
        $serverHeader = new Server();
        $this->assertEquals('Server', $serverHeader->getFieldName());
    }

    public function testServerGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Server needs to be completed');

        $serverHeader = new Server();
        $this->assertEquals('xxx', $serverHeader->getFieldValue());
    }

    public function testServerToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Server needs to be completed');

        $serverHeader = new Server();

        // @todo set some values, then test output
        $this->assertEmpty('Server: xxx', $serverHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

