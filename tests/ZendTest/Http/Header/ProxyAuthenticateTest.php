<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\ProxyAuthenticate;

class ProxyAuthenticateTest extends \PHPUnit_Framework_TestCase
{
    public function testProxyAuthenticateFromStringCreatesValidProxyAuthenticateHeader()
    {
        $proxyAuthenticateHeader = ProxyAuthenticate::fromString('Proxy-Authenticate: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $proxyAuthenticateHeader);
        $this->assertInstanceOf('Zend\Http\Header\ProxyAuthenticate', $proxyAuthenticateHeader);
    }

    public function testProxyAuthenticateGetFieldNameReturnsHeaderName()
    {
        $proxyAuthenticateHeader = new ProxyAuthenticate();
        $this->assertEquals('Proxy-Authenticate', $proxyAuthenticateHeader->getFieldName());
    }

    public function testProxyAuthenticateGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ProxyAuthenticate needs to be completed');

        $proxyAuthenticateHeader = new ProxyAuthenticate();
        $this->assertEquals('xxx', $proxyAuthenticateHeader->getFieldValue());
    }

    public function testProxyAuthenticateToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ProxyAuthenticate needs to be completed');

        $proxyAuthenticateHeader = new ProxyAuthenticate();

        // @todo set some values, then test output
        $this->assertEmpty('Proxy-Authenticate: xxx', $proxyAuthenticateHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = ProxyAuthenticate::fromString("Proxy-Authenticate: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new ProxyAuthenticate("xxx\r\n\r\nevilContent");
    }
}
