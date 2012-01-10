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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Controller\Action\Helper;
use Zend\Controller\Router\Route;

/**
 * Test class for Zend_Controller_Action_Helper_Url.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->front = \Zend\Controller\Front::getInstance();
        $this->front->resetInstance();
        $this->front->setRequest(new \Zend\Controller\Request\Http());
        $this->helper = new \Zend\Controller\Action\Helper\Url();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function testSimpleWithAllParamsProducesAppropriateURL()
    {
        $url = $this->helper->simple('baz', 'bar', 'foo', array('bat' => 'foo', 'ho' => 'hum'));
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertContains('/bat/foo', $url);
        $this->assertContains('/ho/hum', $url);
    }

    public function testSimpleWithMissingControllerAndModuleProducesAppropriateURL()
    {
        $request = $this->front->getRequest();
        $request->setModuleName('foo')
                ->setControllerName('bar');
        $url = $this->helper->simple('baz', null, null, array('bat' => 'foo', 'ho' => 'hum'));
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertContains('/bat/foo', $url);
        $this->assertContains('/ho/hum', $url);
    }

    public function testSimpleWithDefaultModuleProducesURLWithoutModuleSegment()
    {
        $url = $this->helper->simple('baz', 'bar', 'application', array('bat' => 'foo', 'ho' => 'hum'));
        $this->assertEquals('/bar/baz', substr($url, 0, 8));
    }

    public function testURLMethodCreatesURLBasedOnNamedRouteAndPassedParameters()
    {
        $router = $this->front->getRouter();
        $route  = new Route\Route(
            'foo/:action/:page',
            array(
                'module'     => 'default',
                'controller' => 'foobar',
                'action'     => 'bazbat',
                'page'       => 1
            )
        );
        $router->addRoute('foo', $route);
        $url = $this->helper->__invoke(array('action' => 'bar', 'page' => 3), 'foo');
        $this->assertEquals('/foo/bar/3', $url);
    }

    public function testURLMethodCreatesURLBasedOnNamedRouteAndDefaultParameters()
    {
        $router = $this->front->getRouter();
        $route  = new Route\Route(
            'foo/:action/:page',
            array(
                'module'     => 'default',
                'controller' => 'foobar',
                'action'     => 'bazbat',
                'page'       => 1
            )
        );
        $router->addRoute('foo', $route);
        $url = $this->helper->__invoke(array(), 'foo');
        $this->assertEquals('/foo', $url);
    }

    public function testURLMethodCreatesURLBasedOnPassedParametersUsingDefaultRouteWhenNoNamedRoutePassed()
    {
        $this->front->getRouter()->addDefaultRoutes();
        $this->front->addModuleDirectory(__DIR__ . '/../../_files/modules');
        $url = $this->helper->__invoke(array(
            'module'     => 'foo',
            'controller' => 'bar',
            'action'     => 'baz',
            'bat'        => 'foo',
            'ho'         => 'hum'
        ));
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertContains('/bat/foo', $url);
        $this->assertContains('/ho/hum', $url);
    }

    public function testDirectProxiesToSimple()
    {
        $url = $this->helper->direct('baz', 'bar', 'foo', array('bat' => 'foo', 'ho' => 'hum'));
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertContains('/bat/foo', $url);
        $this->assertContains('/ho/hum', $url);
    }

    /**
     * @group ZF-2822
     */
    public function testBaseURLIsAssembledIntoURL()
    {
        $this->front->setBaseURL('baseurl');

        $request = $this->front->getRequest();
        $request->setModuleName('module')
                ->setControllerName('controller');

        $url = $this->helper->simple('action', null, null, array('foo' => 'bar'));
        $this->assertEquals('/baseurl/module/controller/action/foo/bar', $url);
    }
}

