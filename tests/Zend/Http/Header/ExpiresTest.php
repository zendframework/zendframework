<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Expires;

class ExpiresTest extends \PHPUnit_Framework_TestCase
{

    public function testExpiresFromStringCreatesValidExpiresHeader()
    {
        $expiresHeader = Expires::fromString('Expires: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $expiresHeader);
        $this->assertInstanceOf('Zend\Http\Header\Expires', $expiresHeader);
    }

    public function testExpiresGetFieldNameReturnsHeaderName()
    {
        $expiresHeader = new Expires();
        $this->assertEquals('Expires', $expiresHeader->getFieldName());
    }

    public function testExpiresGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Expires needs to be completed');

        $expiresHeader = new Expires();
        $this->assertEquals('xxx', $expiresHeader->getFieldValue());
    }

    public function testExpiresToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Expires needs to be completed');

        $expiresHeader = new Expires();

        // @todo set some values, then test output
        $this->assertEmpty('Expires: xxx', $expiresHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

