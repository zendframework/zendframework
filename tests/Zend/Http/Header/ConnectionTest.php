<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Connection;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{

    public function testConnectionFromStringCreatesValidConnectionHeader()
    {
        $connectionHeader = Connection::fromString('Connection: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $connectionHeader);
        $this->assertInstanceOf('Zend\Http\Header\Connection', $connectionHeader);
    }

    public function testConnectionGetFieldNameReturnsHeaderName()
    {
        $connectionHeader = new Connection();
        $this->assertEquals('Connection', $connectionHeader->getFieldName());
    }

    public function testConnectionGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Connection needs to be completed');

        $connectionHeader = new Connection();
        $this->assertEquals('xxx', $connectionHeader->getFieldValue());
    }

    public function testConnectionToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Connection needs to be completed');

        $connectionHeader = new Connection();

        // @todo set some values, then test output
        $this->assertEmpty('Connection: xxx', $connectionHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

