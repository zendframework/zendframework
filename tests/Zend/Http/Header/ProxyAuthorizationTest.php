<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\ProxyAuthorization;

class ProxyAuthorizationTest extends \PHPUnit_Framework_TestCase
{

    public function testProxyAuthorizationFromStringCreatesValidProxyAuthorizationHeader()
    {
        $proxyAuthorizationHeader = ProxyAuthorization::fromString('Proxy-Authorization: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $proxyAuthorizationHeader);
        $this->assertInstanceOf('Zend\Http\Header\ProxyAuthorization', $proxyAuthorizationHeader);
    }

    public function testProxyAuthorizationGetFieldNameReturnsHeaderName()
    {
        $proxyAuthorizationHeader = new ProxyAuthorization();
        $this->assertEquals('Proxy-Authorization', $proxyAuthorizationHeader->getFieldName());
    }

    public function testProxyAuthorizationGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ProxyAuthorization needs to be completed');

        $proxyAuthorizationHeader = new ProxyAuthorization();
        $this->assertEquals('xxx', $proxyAuthorizationHeader->getFieldValue());
    }

    public function testProxyAuthorizationToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ProxyAuthorization needs to be completed');

        $proxyAuthorizationHeader = new ProxyAuthorization();

        // @todo set some values, then test output
        $this->assertEmpty('Proxy-Authorization: xxx', $proxyAuthorizationHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

