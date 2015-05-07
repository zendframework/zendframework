<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = ProxyAuthorization::fromString("Proxy-Authorization: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new ProxyAuthorization("xxx\r\n\r\nevilContent");
    }
}
