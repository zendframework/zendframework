<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace ZendTest\Session\Validator;

use Zend\Session\Validator\RemoteAddr;

/**
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @group      Zend_Session
 */
class RemoteAddrTest extends \PHPUnit_Framework_TestCase
{
    protected $backup;

    protected function backup()
    {
        $this->backup = $_SERVER;
        unset(
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_X_FORWARDED_FOR'],
            $_SERVER['HTTP_CLIENT_IP']
        );
        RemoteAddr::setUseProxy(false);
        RemoteAddr::setTrustedProxies(array());
        RemoteAddr::setProxyHeader();
    }

    protected function restore()
    {
        $_SERVER = $this->backup;
        RemoteAddr::setUseProxy(false);
        RemoteAddr::setTrustedProxies(array());
        RemoteAddr::setProxyHeader();
    }

    public function testGetData()
    {
        $validator = new RemoteAddr('0.1.2.3');
        $this->assertEquals('0.1.2.3', $validator->getData());
    }

    public function testDefaultUseProxy()
    {
        $this->assertFalse(RemoteAddr::getUseProxy());
    }

    public function testRemoteAddrWithoutProxy()
    {
        $this->backup();
        $_SERVER['REMOTE_ADDR'] = '0.1.2.3';
        $validator = new RemoteAddr();
        $this->assertEquals('0.1.2.3', $validator->getData());
        $this->restore();
    }

    public function testIsValid()
    {
        $this->backup();
        $_SERVER['REMOTE_ADDR'] = '0.1.2.3';
        $validator = new RemoteAddr();
        $_SERVER['REMOTE_ADDR'] = '1.1.2.3';
        $this->assertFalse($validator->isValid());
        $this->restore();
    }

    public function testIgnoreProxyByDefault()
    {
        $this->backup();
        $_SERVER['REMOTE_ADDR'] = '0.1.2.3';
        $_SERVER['HTTP_CLIENT_IP'] = '1.1.2.3';
        $validator = new RemoteAddr();
        $this->assertEquals('0.1.2.3', $validator->getData());
        $this->restore();
    }

    public function testHttpXForwardedFor()
    {
        $this->backup();
        $_SERVER['REMOTE_ADDR'] = '0.1.2.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.1.2.3';
        RemoteAddr::setUseProxy(true);
        $validator = new RemoteAddr();
        $this->assertEquals('1.1.2.3', $validator->getData());
        $this->restore();
    }

    public function testHttpClientIp()
    {
        $this->backup();
        $_SERVER['REMOTE_ADDR'] = '0.1.2.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.1.2.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '2.1.2.3';
        RemoteAddr::setUseProxy(true);
        $validator = new RemoteAddr();
        $this->assertEquals('2.1.2.3', $validator->getData());
        $this->restore();
    }

    public function testUsesRightMostAddressWhenMultipleHttpXForwardedForAddressesPresent()
    {
        $this->backup();
        $_SERVER['REMOTE_ADDR'] = '0.1.2.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '2.1.2.3, 1.1.2.3';
        RemoteAddr::setUseProxy(true);
        $validator = new RemoteAddr();
        $this->assertEquals('1.1.2.3', $validator->getData());
        $this->restore();
    }

    public function testShouldNotUseClientIpHeaderToTestProxyCapabilitiesByDefault()
    {
        $this->backup();
        $_SERVER['REMOTE_ADDR'] = '0.1.2.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '2.1.2.3, 1.1.2.3';
        $_SERVER['HTTP_CLIENT_IP'] = '0.1.2.4';
        RemoteAddr::setUseProxy(true);
        $validator = new RemoteAddr();
        $this->assertEquals('1.1.2.3', $validator->getData());
        $this->restore();
    }

    public function testWillOmitTrustedProxyIpsFromXForwardedForMatching()
    {
        $this->backup();
        $_SERVER['REMOTE_ADDR'] = '0.1.2.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '2.1.2.3, 1.1.2.3';
        RemoteAddr::setUseProxy(true);
        RemoteAddr::setTrustedProxies(array('1.1.2.3'));
        $validator = new RemoteAddr();
        $this->assertEquals('2.1.2.3', $validator->getData());
        $this->restore();
    }

    public function testCanSpecifyWhichHeaderToUseStatically()
    {
        $this->backup();
        $_SERVER['REMOTE_ADDR'] = '0.1.2.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '2.1.2.3, 1.1.2.3';
        $_SERVER['HTTP_CLIENT_IP'] = '0.1.2.4';
        RemoteAddr::setUseProxy(true);
        RemoteAddr::setProxyHeader('Client-Ip');
        $validator = new RemoteAddr();
        $this->assertEquals('0.1.2.4', $validator->getData());
        $this->restore();
    }
}
