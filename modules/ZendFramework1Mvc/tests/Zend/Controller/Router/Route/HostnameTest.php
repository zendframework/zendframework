<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Controller\Router\Route;
use Zend\Controller\Router\Route;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Router
 */
class HostnameTest extends \PHPUnit_Framework_TestCase
{

    public function testCorrectStaticHostMatch()
    {
        $route = $this->_getStaticHostRoute();

        $values = $route->match($this->_getRequest('www.zend.com'));
        $this->assertEquals('ctrl', $values['controller']);
    }

    public function testHostMatchWithPort()
    {
        $route = $this->_getStaticHostRoute();

        $values = $route->match($this->_getRequest('www.zend.com:666'));
        $this->assertEquals('ctrl', $values['controller']);
    }

    public function testWrongStaticHostMatch()
    {
        $route = $this->_getStaticHostRoute();

        $values = $route->match($this->_getRequest('foo.zend.com'));
        $this->assertFalse($values);
    }

    public function testCorrectHostMatch()
    {
        $route = $this->_getHostRoute();

        $values = $route->match($this->_getRequest('foo.zend.com'));
        $this->assertEquals('ctrl', $values['controller']);
    }

    public function testWrongHostMatch()
    {
        $route = $this->_getHostRoute();

        $values = $route->match($this->_getRequest('www.zend.com'));
        $this->assertFalse($values);
    }

    public function testAssembleStaticHost()
    {
        $route = $this->_getStaticHostRoute();

        $this->assertRegexp('/[^a-z0-9]?www\.zend\.com$/i', $route->assemble());
    }

    public function testAssembleHost()
    {
        $route = $this->_getHostRoute();

        $this->assertRegexp('/[^a-z0-9]?foo\.zend\.com$/i', $route->assemble(array('subdomain' => 'foo')));
    }

    public function testAssembleHostWithMissingParam()
    {
        $route = $this->_getHostRoute();

        try {
            $route->assemble();
            $this->fail('An expected Zend_Controller_Router_Exception has not been raised');
        } catch (\Zend\Controller\Router\Exception $expected) {
            $this->assertContains('subdomain is not specified', $expected->getMessage());
        }
    }

    public function testAssembleHostWithDefaultParam()
    {
        $route = $this->_getHostRouteWithDefault();

        $this->assertRegexp('/[^a-z0-9]?bar\.zend\.com$/i', $route->assemble());
    }

    public function testHostGetDefault()
    {
        $route = $this->_getHostRouteWithDefault();

        $this->assertEquals('bar', $route->getDefault('subdomain'));
    }

    public function testHostGetNonExistentDefault()
    {
        $route = $this->_getHostRouteWithDefault();

        $this->assertEquals(null, $route->getDefault('blah'));
    }

    public function testHostGetDefaults()
    {
        $route    = $this->_getHostRouteWithDefault();
        $defaults = $route->getDefaults();

        $this->assertEquals('bar', $defaults['subdomain']);
    }

    public function testRouteWithHostname()
    {
        $request = new Stub('www.zend.com');

        $route = new Route\Hostname('www.zend.com', array('controller' => 'host-foo', 'action' => 'host-bar'));

        $values = $route->match($request);

        $this->assertEquals('host-foo', $values['controller']);
        $this->assertEquals('host-bar', $values['action']);
    }

    public function testSchemeMatch()
    {
        $request = new Stub('www.zend.com', 'https');

        $route = new Route\Hostname('www.zend.com', array('controller' => 'host-foo', 'action' => 'host-bar'), array(), 'https');

        $values = $route->match($request);

        $this->assertEquals('host-foo', $values['controller']);
        $this->assertEquals('host-bar', $values['action']);
    }

    public function testSchemeNoMatch()
    {
        $request = new Stub('www.zend.com', 'http');

        $route = new Route\Hostname('www.zend.com', array('controller' => 'host-foo', 'action' => 'host-bar'), array(), 'https');

        $values = $route->match($request);

        $this->assertFalse($values);
    }

    public function testAutomaticSchemeAssembling()
    {
        $route   = new Route\Hostname('www.zend.com', array('controller' => 'host-foo', 'action' => 'host-bar'), array());

        $url = $route->assemble();

        $this->assertEquals('http://www.zend.com', $url);
    }

    public function testForcedSchemeAssembling()
    {
        $request = new Stub('www.zend.com');
        $route   = new Route\Hostname('www.zend.com', array('controller' => 'host-foo', 'action' => 'host-bar'), array(), 'https');
        $route->setRequest($request);

        $url = $route->assemble();

        $this->assertEquals('https://www.zend.com', $url);
    }

    protected function _getStaticHostRoute()
    {
        $route = new Route\Hostname('www.zend.com',
                                                            array('controller' => 'ctrl',
                                                                  'action' => 'act'));

        return $route;
    }

    protected function _getHostRoute()
    {
        $route = new Route\Hostname(':subdomain.zend.com',
                                                            array('controller' => 'ctrl',
                                                                  'action' => 'act'),
                                                            array('subdomain' => '(foo|bar)'));

        return $route;
    }

    protected function _getHostRouteWithDefault()
    {
        $route = new Route\Hostname(':subdomain.zend.com',
                                                            array('controller' => 'ctrl',
                                                                  'action' => 'act',
                                                                  'subdomain' => 'bar'),
                                                            array('subdomain' => '(foo|bar)'));

        return $route;
    }

    protected function _getRequest($host) {
        return new Stub($host);
    }

}

/**
 * Zend_Controller_RouterTest_Request_Stub - request object for route testing
 */
class Stub extends \Zend\Controller\Request\AbstractRequest
{
    protected $_host;

    protected $_scheme;

    public function __construct($host, $scheme = 'http') {
        $this->_host   = $host;
        $this->_scheme = $scheme;
    }

    public function getHttpHost() {
        return $this->_host;
    }

    public function getScheme() {
        return $this->_scheme;
    }
}
