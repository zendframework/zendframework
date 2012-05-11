<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\UserAgent;

class UserAgentTest extends \PHPUnit_Framework_TestCase
{

    public function testUserAgentFromStringCreatesValidUserAgentHeader()
    {
        $userAgentHeader = UserAgent::fromString('User-Agent: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $userAgentHeader);
        $this->assertInstanceOf('Zend\Http\Header\UserAgent', $userAgentHeader);
    }

    public function testUserAgentGetFieldNameReturnsHeaderName()
    {
        $userAgentHeader = new UserAgent();
        $this->assertEquals('User-Agent', $userAgentHeader->getFieldName());
    }

    public function testUserAgentGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('UserAgent needs to be completed');

        $userAgentHeader = new UserAgent();
        $this->assertEquals('xxx', $userAgentHeader->getFieldValue());
    }

    public function testUserAgentToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('UserAgent needs to be completed');

        $userAgentHeader = new UserAgent();

        // @todo set some values, then test output
        $this->assertEmpty('User-Agent: xxx', $userAgentHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

