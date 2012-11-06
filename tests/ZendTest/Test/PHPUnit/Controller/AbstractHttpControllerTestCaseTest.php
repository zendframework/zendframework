<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace ZendTest\Test\PHPUnit\Controller;

use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\View\Model\ViewModel;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @group      Zend_Test
 */
class AbstractHttpControllerTestCaseTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/_files/application.config.php'
        );
        parent::setUp();
    }

    public function testUseOfRouter()
    {
       $this->assertEquals(false, $this->useConsoleRequest);
    }

    public function testApplicationClass()
    {
        $applicationClass = get_class($this->getApplication());
        $this->assertEquals($applicationClass, 'Zend\Mvc\Application');
    }

    public function testApplicationServiceLocatorClass()
    {
        $smClass = get_class($this->getApplicationServiceLocator());
        $this->assertEquals($smClass, 'Zend\ServiceManager\ServiceManager');
    }

    public function testAssertApplicationRequest()
    {
        $this->assertEquals(true, $this->getRequest() instanceof RequestInterface);
    }

    public function testAssertApplicationResponse()
    {
        $this->assertEquals(true, $this->getResponse() instanceof ResponseInterface);
    }

    public function testAssertResponseStatusCode()
    {
        $this->dispatch('/tests');
        $this->assertResponseStatusCode(200);

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertResponseStatusCode(302);
    }

    public function testAssertNotResponseStatusCode()
    {
        $this->dispatch('/tests');
        $this->assertNotResponseStatusCode(302);

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotResponseStatusCode(200);
    }

    public function testAssertModuleName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertModule('mock');
        $this->assertModule('Mock');
        $this->assertModule('MoCk');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertModule('Application');
    }

    public function testAssertNotModuleName()
    {
        $this->dispatch('/tests');
        $this->assertNotModule('Application');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotModule('mock');
    }

    public function testAssertControllerClass()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertControllerClass('IndexController');
        $this->assertControllerClass('Indexcontroller');
        $this->assertControllerClass('indexcontroller');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertControllerClass('Index');
    }

    public function testAssertNotControllerClass()
    {
        $this->dispatch('/tests');
        $this->assertNotControllerClass('Index');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotControllerClass('IndexController');
    }

    public function testAssertControllerName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertControllerName('mock_index');
        $this->assertControllerName('Mock_index');
        $this->assertControllerName('MoCk_index');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertControllerName('mock');
    }

    public function testAssertNotControllerName()
    {
        $this->dispatch('/tests');
        $this->assertNotControllerName('mock');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotControllerName('mock_index');
    }

    public function testAssertActionName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertActionName('unittests');
        $this->assertActionName('unitTests');
        $this->assertActionName('UnitTests');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertActionName('unit');
    }

    public function testAssertNotActionName()
    {
        $this->dispatch('/tests');
        $this->assertNotActionName('unit');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotActionName('unittests');
    }

    public function testAssertMatchedRouteName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertMatchedRouteName('myroute');
        $this->assertMatchedRouteName('myRoute');
        $this->assertMatchedRouteName('MyRoute');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertMatchedRouteName('route');
    }

    public function testAssertNotMatchedRouteName()
    {
        $this->dispatch('/tests');
        $this->assertNotMatchedRouteName('route');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotMatchedRouteName('myroute');
    }

    public function testAssertQuery()
    {
        $this->dispatch('/tests');
        $this->assertQuery('form#myform');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertQuery('form#id');
    }

    public function testAssertNotQuery()
    {
        $this->dispatch('/tests');
        $this->assertNotQuery('form#id');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotQuery('form#myform');
    }

    public function testAssertQueryCount()
    {
        $this->dispatch('/tests');
        $this->assertQueryCount('div.top', 3);

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertQueryCount('div.top', 2);
    }

    public function testAssertNotQueryCount()
    {
        $this->dispatch('/tests');
        $this->assertNotQueryCount('div.top', 1);
        $this->assertNotQueryCount('div.top', 2);

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotQueryCount('div.top', 3);
    }

    public function testAssertQueryCountMin()
    {
        $this->dispatch('/tests');
        $this->assertQueryCountMin('div.top', 1);
        $this->assertQueryCountMin('div.top', 2);
        $this->assertQueryCountMin('div.top', 3);

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertQueryCountMin('div.top', 4);
    }

    public function testAssertQueryCountMax()
    {
        $this->dispatch('/tests');
        $this->assertQueryCountMax('div.top', 5);
        $this->assertQueryCountMax('div.top', 4);
        $this->assertQueryCountMax('div.top', 3);

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertQueryCountMax('div.top', 2);
    }

    public function testAssertQueryWithDynamicQueryParams()
    {
        $this->getRequest()
            ->setMethod('GET')
            ->setQuery(new Parameters(array('num_get' => 5)));
        $this->dispatch('/tests');
        $this->assertQueryCount('div.get', 5);
        $this->assertQueryCount('div.post', 0);
    }

    public function testAssertQueryWithDynamicPostParams()
    {
        $this->getRequest()
            ->setMethod('POST')
            ->setPost(new Parameters(array('num_post' => 5)));
        $this->dispatch('/tests');
        $this->assertQueryCount('div.post', 5);
        $this->assertQueryCount('div.get', 0);
    }

    public function testAssertUriWithHostname()
    {
        $this->dispatch('http://my.domain.tld:443');
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $this->assertEquals($routeMatch->getParam('subdomain'), 'my');
        $this->assertEquals($this->getRequest()->getUri()->getPort(), 443);
    }

    /**
     * Sample tests on MvcEvent
     */
    public function testAssertApplicationMvcEvent()
    {
        $this->dispatch('/tests');

        // get and assert mvc event
        $mvcEvent = $this->getApplication()->getMvcEvent();
        $this->assertEquals(true, $mvcEvent instanceof MvcEvent);
        $this->assertEquals($mvcEvent->getApplication(), $this->getApplication());

        // get and assert view controller
        $viewModel = $mvcEvent->getResult();
        $this->assertEquals(true, $viewModel instanceof ViewModel);
        $this->assertEquals($viewModel->getTemplate(), 'mock/index/unittests');

        // get and assert view manager layout
        $layout = $mvcEvent->getViewModel();
        $this->assertEquals(true, $layout instanceof ViewModel);
        $this->assertEquals($layout->getTemplate(), 'layout/layout');

        // children layout must be the controller view
        $this->assertEquals($viewModel, current($layout->getChildren()));
    }

    /**
     * Sample tests on Application events
     */
    public function testAssertApplicationEvents()
    {
        $this->url('/tests');

        $result = $this->triggerApplicationEvent(MvcEvent::EVENT_ROUTE);
        $routeMatch = $result->last();
        $this->assertEquals(false, $result->stopped());
        $this->assertEquals(false, $this->getApplication()->getMvcEvent()->getError());
        $this->assertEquals(true, $routeMatch instanceof RouteMatch);
        $this->assertEquals($routeMatch->getParam('controller'), 'mock_index');

        $result = $this->triggerApplicationEvent(MvcEvent::EVENT_DISPATCH);
        $viewModel = $this->getApplication()->getMvcEvent()->getResult();
        $this->assertEquals(true, $viewModel instanceof ViewModel);
        $this->assertEquals($viewModel->getTemplate(), 'mock/index/unittests');
    }

    /**
     * Sample tests on Application errors events
     */
    public function testAssertApplicationErrorsEvents()
    {
        $this->url('/bad-url');
        $result = $this->triggerApplicationEvent(MvcEvent::EVENT_ROUTE);
        $this->assertEquals(true, $result->stopped());
        $this->assertEquals(Application::ERROR_ROUTER_NO_MATCH, $this->getApplication()->getMvcEvent()->getError());
    }
}
