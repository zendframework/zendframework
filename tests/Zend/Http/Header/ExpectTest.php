<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Expect;

class ExpectTest extends \PHPUnit_Framework_TestCase
{

    public function testExpectFromStringCreatesValidExpectHeader()
    {
        $expectHeader = Expect::fromString('Expect: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $expectHeader);
        $this->assertInstanceOf('Zend\Http\Header\Expect', $expectHeader);
    }

    public function testExpectGetFieldNameReturnsHeaderName()
    {
        $expectHeader = new Expect();
        $this->assertEquals('Expect', $expectHeader->getFieldName());
    }

    public function testExpectGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Expect needs to be completed');

        $expectHeader = new Expect();
        $this->assertEquals('xxx', $expectHeader->getFieldValue());
    }

    public function testExpectToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Expect needs to be completed');

        $expectHeader = new Expect();

        // @todo set some values, then test output
        $this->assertEmpty('Expect: xxx', $expectHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

