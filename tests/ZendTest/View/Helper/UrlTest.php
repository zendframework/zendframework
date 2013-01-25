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

use Zend\View\Helper\Url as UrlHelper;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack as Router;

/**
 * Zend_View_Helper_UrlTest
 *
 * Tests formText helper, including some common functionality of all form helpers
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $router = new Router();
        $router->addRoute('home', array(
            'type' => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route' => '/',
            )
        ));
        $router->addRoute('default', array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/:controller[/:action]',
                )
        ));
        $this->router = $router;

        $this->url = new UrlHelper;
        $this->url->setRouter($router);
    }

    public function testHelperHasHardDependencyWithRouter()
    {
        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'No RouteStackInterface instance provided');
        $url = new UrlHelper;
        $url('home');
    }

    public function testHomeRoute()
    {
        $url = $this->url->__invoke('home');
        $this->assertEquals('/', $url);
    }

    public function testModuleRoute()
    {
        $url = $this->url->__invoke('default', array('controller' => 'ctrl', 'action' => 'act'));
        $this->assertEquals('/ctrl/act', $url);
    }

    public function testPluginWithoutRouteMatchesInEventRaisesExceptionWhenNoRouteProvided()
    {
        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'RouteMatch');
        $url = $this->url->__invoke();
    }

    public function testPluginWithRouteMatchesReturningNoMatchedRouteNameRaisesExceptionWhenNoRouteProvided()
    {
        $this->url->setRouteMatch(new RouteMatch(array()));
        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'matched');
        $url = $this->url->__invoke();
    }

    public function testPassingNoArgumentsWithValidRouteMatchGeneratesUrl()
    {
        $routeMatch = new RouteMatch(array());
        $routeMatch->setMatchedRouteName('home');
        $this->url->setRouteMatch($routeMatch);
        $url = $this->url->__invoke();
        $this->assertEquals('/', $url);
    }

    public function testCanReuseMatchedParameters()
    {
        $this->router->addRoute('replace', array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/:controller/:action',
                'defaults' => array(
                    'controller' => 'ZendTest\Mvc\Controller\TestAsset\SampleController',
                ),
            ),
        ));
        $routeMatch = new RouteMatch(array(
            'controller' => 'foo',
        ));
        $routeMatch->setMatchedRouteName('replace');
        $this->url->setRouteMatch($routeMatch);
        $url = $this->url->__invoke('replace', array('action' => 'bar'), array(), true);
        $this->assertEquals('/foo/bar', $url);
    }

    public function testCanPassBooleanValueForThirdArgumentToAllowReusingRouteMatches()
    {
        $this->router->addRoute('replace', array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/:controller/:action',
                'defaults' => array(
                    'controller' => 'ZendTest\Mvc\Controller\TestAsset\SampleController',
                ),
            ),
        ));
        $routeMatch = new RouteMatch(array(
            'controller' => 'foo',
        ));
        $routeMatch->setMatchedRouteName('replace');
        $this->url->setRouteMatch($routeMatch);
        $url = $this->url->__invoke('replace', array('action' => 'bar'), true);
        $this->assertEquals('/foo/bar', $url);
    }

    public function testRemovesModuleRouteListenerParamsWhenReusingMatchedParameters()
    {
        $router = new \Zend\Mvc\Router\Http\TreeRouteStack;
        $router->addRoute('default', array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/:controller/:action',
                'defaults' => array(
                    ModuleRouteListener::MODULE_NAMESPACE => 'ZendTest\Mvc\Controller\TestAsset',
                    'controller' => 'SampleController',
                    'action'     => 'Dash'
                )
            ),
            'child_routes' => array(
                'wildcard' => array(
                    'type'    => 'Zend\Mvc\Router\Http\Wildcard',
                    'options' => array(
                        'param_delimiter'     => '=',
                        'key_value_delimiter' => '%'
                    )
                )
            )
        ));

        $routeMatch = new RouteMatch(array(
            ModuleRouteListener::MODULE_NAMESPACE => 'ZendTest\Mvc\Controller\TestAsset',
            'controller' => 'Rainbow'
        ));
        $routeMatch->setMatchedRouteName('default/wildcard');

        $event = new MvcEvent();
        $event->setRouter($router)
              ->setRouteMatch($routeMatch);

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->onRoute($event);

        $helper = new UrlHelper();
        $helper->setRouter($router);
        $helper->setRouteMatch($routeMatch);

        $url = $helper->__invoke('default/wildcard', array('Twenty' => 'Cooler'), true);
        $this->assertEquals('/Rainbow/Dash=Twenty%Cooler', $url);
    }
}
