<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\PhpEnvironment;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\PhpEnvironment\RemoteAddress as RemoteAddr;

class RemoteAddressTest extends TestCase
{
    /**
     * Original environemnt
     *
     * @var array
     */
    protected $originalEnvironment;

    /**
     * Save the original environment and set up a clean one.
     */
    public function setUp()
    {
        $this->originalEnvironment = array(
            'post'   => $_POST,
            'get'    => $_GET,
            'cookie' => $_COOKIE,
            'server' => $_SERVER,
            'env'    => $_ENV,
            'files'  => $_FILES,
        );

        $_POST   = array();
        $_GET    = array();
        $_COOKIE = array();
        $_SERVER = array();
        $_ENV    = array();
        $_FILES  = array();

        $this->remoteAddress = new RemoteAddr();
    }

    /**
     * Restore the original environment
     */
    public function tearDown()
    {
        $_POST   = $this->originalEnvironment['post'];
        $_GET    = $this->originalEnvironment['get'];
        $_COOKIE = $this->originalEnvironment['cookie'];
        $_SERVER = $this->originalEnvironment['server'];
        $_ENV    = $this->originalEnvironment['env'];
        $_FILES  = $this->originalEnvironment['files'];
    }

    public function testSetGetUseProxy()
    {
        $this->remoteAddress->setUseProxy(false);
        $this->assertFalse($this->remoteAddress->getUseProxy());
    }

    public function testSetGetDefaultUseProxy()
    {
        $this->remoteAddress->setUseProxy();
        $this->assertTrue($this->remoteAddress->getUseProxy());
    }

    public function testSetTrustedProxies()
    {
        $result = $this->remoteAddress->setTrustedProxies(array(
            '192.168.0.10', '192.168.0.1'
        ));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\RemoteAddress', $result);
    }

    public function testGetIpAddress()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $this->assertEquals('127.0.0.1', $this->remoteAddress->getIpAddress());
    }

    public function testGetIpAddressFromProxy()
    {
        $this->remoteAddress->setUseProxy(true);
        $this->remoteAddress->setTrustedProxies(array(
            '192.168.0.10', '10.0.0.1'
        ));
        $_SERVER['REMOTE_ADDR'] = '192.168.0.10';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '8.8.8.8, 10.0.0.1';
        $this->assertEquals('8.8.8.8', $this->remoteAddress->getIpAddress());
    }

    public function testGetIpAddressFromProxyRemoteAddressNotTrusted()
    {
        $this->remoteAddress->setUseProxy(true);
        $this->remoteAddress->setTrustedProxies(array(
            '10.0.0.1'
        ));
        // the REMOTE_ADDR is not in the trusted IPs, possible attack here
        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '8.8.8.8, 10.0.0.1';
        $this->assertEquals('1.1.1.1', $this->remoteAddress->getIpAddress());
    }

    /**
     * Test to prevent attack on the HTTP_X_FORWARDED_FOR header
     * The client IP is always the first on the left
     *
     * @see http://tools.ietf.org/html/draft-ietf-appsawg-http-forwarded-10#section-5.2
     */
    public function testGetIpAddressFromProxyFakeData()
    {
        $this->remoteAddress->setUseProxy(true);
        $this->remoteAddress->setTrustedProxies(array(
            '192.168.0.10', '10.0.0.1', '10.0.0.2'
        ));
        $_SERVER['REMOTE_ADDR'] = '192.168.0.10';
        // 1.1.1.1 is the first IP address from the right not representing a known proxy server; as such, we
        // must treat it as a client IP.
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '8.8.8.8, 10.0.0.2, 1.1.1.1, 10.0.0.1';
        $this->assertEquals('1.1.1.1', $this->remoteAddress->getIpAddress());
    }

    /**
     * Tests if an empty string is returned if the server variable
     * REMOTE_ADDR is not set.
     *
     * This happens when you run a local unit test, or a PHP script with
     * PHP from the command line.
     */
    public function testGetIpAddressReturnsEmptyStringOnNoRemoteAddr()
    {
        // Store the set IP address for later use
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            unset($_SERVER['REMOTE_ADDR']);
        }

        $this->remoteAddress->setUseProxy(true);
        $this->assertEquals('', $this->remoteAddress->getIpAddress());

        if (isset($ipAddress)) {
            $_SERVER['REMOTE_ADDR'] = $ipAddress;
        }
    }
}
