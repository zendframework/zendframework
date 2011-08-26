<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\RetryAfter;

class RetryAfterTest extends \PHPUnit_Framework_TestCase
{

    public function testRetryAfterFromStringCreatesValidRetryAfterHeader()
    {
        $retryAfterHeader = RetryAfter::fromString('Retry-After: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $retryAfterHeader);
        $this->assertInstanceOf('Zend\Http\Header\RetryAfter', $retryAfterHeader);
    }

    public function testRetryAfterGetFieldNameReturnsHeaderName()
    {
        $retryAfterHeader = new RetryAfter();
        $this->assertEquals('Retry-After', $retryAfterHeader->getFieldName());
    }

    public function testRetryAfterGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('RetryAfter needs to be completed');

        $retryAfterHeader = new RetryAfter();
        $this->assertEquals('xxx', $retryAfterHeader->getFieldValue());
    }

    public function testRetryAfterToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('RetryAfter needs to be completed');

        $retryAfterHeader = new RetryAfter();

        // @todo set some values, then test output
        $this->assertEmpty('Retry-After: xxx', $retryAfterHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

