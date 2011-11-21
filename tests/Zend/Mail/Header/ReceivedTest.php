<?php

namespace ZendTest\Mail\Header;

use Zend\Mail\Header\Received;

class ReceivedTest extends \PHPUnit_Framework_TestCase
{

    public function testFromStringCreatesValidReceivedHeader()
    {
        $receivedHeader = Received::fromString('Received: xxx');
        $this->assertInstanceOf('Zend\Mail\Header\HeaderDescription', $receivedHeader);
        $this->assertInstanceOf('Zend\Mail\Header\Received', $receivedHeader);
    }

    public function testGetFieldNameReturnsHeaderName()
    {
        $receivedHeader = new Received();
        $this->assertEquals('Received', $receivedHeader->getFieldName());
    }

    public function testReceivedGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Received needs to be completed');

        $receivedHeader = new Received();
        $this->assertEquals('xxx', $receivedHeader->getFieldValue());
    }

    public function testReceivedToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Received needs to be completed');

        $receivedHeader = new Received();

        // @todo set some values, then test output
        $this->assertEmpty('Received: xxx', $receivedHeader->toString());
    }

    /** Implementation specific tests here */
    
}

