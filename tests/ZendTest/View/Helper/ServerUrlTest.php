<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use Zend\View\Helper;

/**
 * Tests Zend_View_Helper_ServerUrl
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class ServerUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Back up of $_SERVER
     *
     * @var array
     */
    protected $serverBackup;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->serverBackup = $_SERVER;
        unset($_SERVER['HTTPS']);
        unset($_SERVER['SERVER_PORT']);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $_SERVER = $this->serverBackup;
    }

    public function testConstructorWithOnlyHost()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';

        $url = new Helper\ServerUrl();
        $this->assertEquals('http://example.com', $url->__invoke());
    }

    public function testConstructorWithOnlyHostIncludingPort()
    {
        $_SERVER['HTTP_HOST'] = 'example.com:8000';

        $url = new Helper\ServerUrl();
        $this->assertEquals('http://example.com:8000', $url->__invoke());
    }

    public function testConstructorWithHostAndHttpsOn()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTPS']     = 'on';

        $url = new Helper\ServerUrl();
        $this->assertEquals('https://example.com', $url->__invoke());
    }

    public function testConstructorWithHostAndHttpsTrue()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTPS'] = true;

        $url = new Helper\ServerUrl();
        $this->assertEquals('https://example.com', $url->__invoke());
    }

    public function testConstructorWithHostIncludingPortAndHttpsTrue()
    {
        $_SERVER['HTTP_HOST'] = 'example.com:8181';
        $_SERVER['HTTPS'] = true;

        $url = new Helper\ServerUrl();
        $this->assertEquals('https://example.com:8181', $url->__invoke());
    }

    public function testConstructorWithHttpHostIncludingPortAndPortSet()
    {
        $_SERVER['HTTP_HOST'] = 'example.com:8181';
        $_SERVER['SERVER_PORT'] = 8181;

        $url = new Helper\ServerUrl();
        $this->assertEquals('http://example.com:8181', $url->__invoke());
    }

    public function testConstructorWithHttpHostAndServerNameAndPortSet()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['SERVER_NAME'] = 'example.org';
        $_SERVER['SERVER_PORT'] = 8080;

        $url = new Helper\ServerUrl();
        $this->assertEquals('http://example.com:8080', $url->__invoke());
    }

    public function testConstructorWithNoHttpHostButServerNameAndPortSet()
    {
        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.org';
        $_SERVER['SERVER_PORT'] = 8080;

        $url = new Helper\ServerUrl();
        $this->assertEquals('http://example.org:8080', $url->__invoke());
    }

    public function testServerUrlWithTrueParam()
    {
        $_SERVER['HTTPS']       = 'off';
        $_SERVER['HTTP_HOST']   = 'example.com';
        $_SERVER['REQUEST_URI'] = '/foo.html';

        $url = new Helper\ServerUrl();
        $this->assertEquals('http://example.com/foo.html', $url->__invoke(true));
    }

    public function testServerUrlWithInteger()
    {
        $_SERVER['HTTPS']     = 'off';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/foo.html';

        $url = new Helper\ServerUrl();
        $this->assertEquals('http://example.com', $url->__invoke(1337));
    }

    public function testServerUrlWithObject()
    {
        $_SERVER['HTTPS']     = 'off';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/foo.html';

        $url = new Helper\ServerUrl();
        $this->assertEquals('http://example.com', $url->__invoke(new \stdClass()));
    }

    /**
     * @group ZF-9919
     */
    public function testServerUrlWithScheme()
    {
        $_SERVER['HTTP_SCHEME'] = 'https';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $url = new Helper\ServerUrl();
        $this->assertEquals('https://example.com', $url->__invoke());
    }

    /**
     * @group ZF-9919
     */
    public function testServerUrlWithPort()
    {
        $_SERVER['SERVER_PORT'] = 443;
        $_SERVER['HTTP_HOST'] = 'example.com';
        $url = new Helper\ServerUrl();
        $this->assertEquals('https://example.com', $url->__invoke());
    }

    /**
     * @group ZF2-508
     */
    public function testServerUrlWithProxy()
    {
        $_SERVER['HTTP_HOST'] = 'proxyserver.com';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'www.firsthost.org';
        $url = new Helper\ServerUrl();
        $url->setUseProxy(true);
        $this->assertEquals('http://www.firsthost.org', $url->__invoke());
    }

    /**
     * @group ZF2-508
     */
    public function testServerUrlWithMultipleProxies()
    {
        $_SERVER['HTTP_HOST'] = 'proxyserver.com';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'www.firsthost.org, www.secondhost.org';
        $url = new Helper\ServerUrl();
        $url->setUseProxy(true);
        $this->assertEquals('http://www.secondhost.org', $url->__invoke());
    }

    public function testDoesNotUseProxyByDefault()
    {
        $_SERVER['HTTP_HOST'] = 'proxyserver.com';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'www.firsthost.org, www.secondhost.org';
        $url = new Helper\ServerUrl();
        $this->assertEquals('http://proxyserver.com', $url->__invoke());
    }

    public function testCanUseXForwardedPortIfProvided()
    {
        $_SERVER['HTTP_HOST'] = 'proxyserver.com';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'www.firsthost.org, www.secondhost.org';
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '8888';
        $url = new Helper\ServerUrl();
        $url->setUseProxy(true);
        $this->assertEquals('http://www.secondhost.org:8888', $url->__invoke());
    }
}
