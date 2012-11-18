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

use Zend\EventManager\StaticEventManager;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Stdlib\Glob;
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
            include __DIR__ . '/../../_files/application.config.php'
        );
        parent::setUp();
    }

    public function testModuleCacheIsDisabled()
    {
        $config = $this->getApplicationConfig();
        $config = $config['module_listener_options']['cache_dir'];
        $this->assertEquals(0, count(glob($config . '/*.php')));
    }

    public function testCanNotDefineApplicationConfigWhenApplicationIsBuilt()
    {
        // cosntruct app
        $this->getApplication();

        $this->setExpectedException('Zend\Stdlib\Exception\LogicException');
        $this->setApplicationConfig(
            include __DIR__ . '/../../_files/application.config.php'
        );
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
        $this->assertModuleName('baz');
        $this->assertModuleName('Baz');
        $this->assertModuleName('BAz');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertModuleName('Application');
    }

    public function testAssertNotModuleName()
    {
        $this->dispatch('/tests');
        $this->assertNotModuleName('Application');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotModuleName('baz');
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
        $this->assertControllerName('baz_index');
        $this->assertControllerName('Baz_index');
        $this->assertControllerName('BAz_index');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertControllerName('baz');
    }

    public function testAssertNotControllerName()
    {
        $this->dispatch('/tests');
        $this->assertNotControllerName('baz');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotControllerName('baz_index');
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

    public function testAssertQueryContentContains()
    {
        $this->dispatch('/tests');
        $this->assertQueryContentContains('div#content', 'foo');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertQueryContentContains('div#content', 'bar');
    }

    public function testAssertNotQueryContentContains()
    {
        $this->dispatch('/tests');
        $this->assertNotQueryContentContains('div#content', 'bar');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotQueryContentContains('div#content', 'foo');
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

    public function testAssertQueryWithDynamicQueryParamsInDispatchMethod()
    {
        $this->dispatch('/tests', 'GET', array('num_get' => 5));
        $this->assertQueryCount('div.get', 5);
        $this->assertQueryCount('div.post', 0);
    }

    public function testAssertQueryWithDynamicQueryParamsInUrl()
    {
        $this->dispatch('/tests?foo=bar&num_get=5');
        $this->assertQueryCount('div.get', 5);
        $this->assertQueryCount('div.post', 0);
    }

    public function testAssertQueryWithDynamicQueryParamsInUrlAnsPostInParams()
    {
        $this->dispatch('/tests?foo=bar&num_get=5', 'POST', array('num_post' => 5));
        $this->assertQueryCount('div.get', 5);
        $this->assertQueryCount('div.post', 5);
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

    public function testAssertQueryWithDynamicPostParamsInDispatchMethod()
    {
        $this->dispatch('/tests', 'POST', array('num_post' => 5));
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

    public function testAssertWithMultiDispatch()
    {
        $this->dispatch('/tests');
        $this->assertQueryCount('div.get', 0);
        $this->assertQueryCount('div.post', 0);

        $this->reset();

        $this->dispatch('/tests?foo=bar&num_get=3');
        $this->assertQueryCount('div.get', 3);
        $this->assertQueryCount('div.post', 0);

        $this->reset();

        $this->dispatch('/tests');
        $this->assertQueryCount('div.get', 0);
        $this->assertQueryCount('div.post', 0);
    }

    public function testAssertWithEventShared()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../_files/application.config.with.shared.events.php'
        );
        $this->dispatch('/tests');
        $this->assertNotQuery('div#content');
        $this->assertEquals('<html></html>', $this->getResponse()->getContent());

        $this->assertEquals(true, StaticEventManager::hasInstance());
        $countListeners = count(StaticEventManager::getInstance()->getListeners(
            'Zend\Mvc\Application', MvcEvent::EVENT_FINISH));
        $this->assertEquals(1, $countListeners);

        $this->reset();

        $this->assertEquals(false, StaticEventManager::hasInstance());
        $countListeners = StaticEventManager::getInstance()->getListeners(
            'Zend\Mvc\Application', MvcEvent::EVENT_FINISH);
        $this->assertEquals(false, $countListeners);

        $this->dispatch('/tests-bis');
        $this->assertQuery('div#content');
        $this->assertNotEquals('<html></html>', $this->getResponse()->getContent());
    }

    public function testAssertExceptionInAction()
    {
        $this->dispatch('/exception');
        $this->assertResponseStatusCode(500);
        $this->assertApplicationException('RuntimeException', 'Foo error');

        $this->dispatch('/exception');
        $this->assertResponseStatusCode(500);
        $this->assertApplicationException('RuntimeException');
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
        $this->assertEquals($viewModel->getTemplate(), 'baz/index/unittests');

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
        $this->assertEquals($routeMatch->getParam('controller'), 'baz_index');

        $result = $this->triggerApplicationEvent(MvcEvent::EVENT_DISPATCH);
        $viewModel = $this->getApplication()->getMvcEvent()->getResult();
        $this->assertEquals(true, $viewModel instanceof ViewModel);
        $this->assertEquals($viewModel->getTemplate(), 'baz/index/unittests');
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
