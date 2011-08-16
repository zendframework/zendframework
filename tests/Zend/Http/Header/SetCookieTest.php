<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\SetCookie;

class SetCookieTest extends \PHPUnit_Framework_TestCase
{

    public function testSetCookieFromStringCreatesValidSetCookieHeader()
    {
        $setCookieHeader = SetCookie::fromString('Set-Cookie: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $setCookieHeader);
        $this->assertInstanceOf('Zend\Http\Header\SetCookie', $setCookieHeader);
    }

    public function testSetCookieGetFieldNameReturnsHeaderName()
    {
        $setCookieHeader = new SetCookie();
        $this->assertEquals('Set-Cookie', $setCookieHeader->getFieldName());
    }

    public function testSetCookieGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('SetCookie needs to be completed');

        $setCookieHeader = new SetCookie();
        $this->assertEquals('xxx', $setCookieHeader->getFieldValue());
    }

    public function testSetCookieToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('SetCookie needs to be completed');

        $setCookieHeader = new SetCookie();

        // @todo set some values, then test output
        $this->assertEmpty('Set-Cookie: xxx', $setCookieHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

