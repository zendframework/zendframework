<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace ZendTest\Test\PHPUnit\Controller;

use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\Parameters;
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

    public function testUseOfRouter()
    {
       $this->assertEquals(false, $this->useConsoleRequest);
    }

    public function testAssertResponseStatusCode()
    {
        $this->dispatch('/tests');
        $this->assertResponseStatusCode(200);

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual status code is "200"' // check actual code is display
        );
        $this->assertResponseStatusCode(302);
    }

    public function testAssertNotResponseStatusCode()
    {
        $this->dispatch('/tests');
        $this->assertNotResponseStatusCode(302);

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotResponseStatusCode(200);
    }

    public function testAssertHasResponseHeader()
    {
        $this->dispatch('/tests');
        $this->assertHasResponseHeader('Content-Type');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertHasResponseHeader('Unknow-header');
    }

    public function testAssertNotHasResponseHeader()
    {
        $this->dispatch('/tests');
        $this->assertNotHasResponseHeader('Unknow-header');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotHasResponseHeader('Content-Type');
    }

    public function testAssertResponseHeaderContains()
    {
        $this->dispatch('/tests');
        $this->assertResponseHeaderContains('Content-Type', 'text/html');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual content is "text/html"' // check actual content is display
        );
        $this->assertResponseHeaderContains('Content-Type', 'text/json');
    }

    public function testAssertNotResponseHeaderContains()
    {
        $this->dispatch('/tests');
        $this->assertNotResponseHeaderContains('Content-Type', 'text/json');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotResponseHeaderContains('Content-Type', 'text/html');
    }

    public function testAssertResponseHeaderRegex()
    {
        $this->dispatch('/tests');
        $this->assertResponseHeaderRegex('Content-Type', '#html$#');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual content is "text/html"' // check actual content is display
        );
        $this->assertResponseHeaderRegex('Content-Type', '#json#');
    }

    public function testAssertNotResponseHeaderRegex()
    {
        $this->dispatch('/tests');
        $this->assertNotResponseHeaderRegex('Content-Type', '#json#');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotResponseHeaderRegex('Content-Type', '#html$#');
    }

    public function testAssertRedirect()
    {
        $this->dispatch('/redirect');
        $this->assertRedirect();

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual redirection is "http://www.zend.com"' // check actual redirection is display
        );
        $this->assertNotRedirect();
    }

    public function testAssertNotRedirect()
    {
        $this->dispatch('/test');
        $this->assertNotRedirect();

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertRedirect();
    }

    public function testAssertRedirectTo()
    {
        $this->dispatch('/redirect');
        $this->assertRedirectTo('http://www.zend.com');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual redirection is "http://www.zend.com"' // check actual redirection is display
        );
        $this->assertRedirectTo('http://www.zend.fr');
    }

    public function testAssertNotRedirectTo()
    {
        $this->dispatch('/redirect');
        $this->assertNotRedirectTo('http://www.zend.fr');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotRedirectTo('http://www.zend.com');
    }

    public function testAssertRedirectRegex()
    {
        $this->dispatch('/redirect');
        $this->assertRedirectRegex('#zend\.com$#');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual redirection is "http://www.zend.com"' // check actual redirection is display
        );
        $this->assertRedirectRegex('#zend\.fr$#');
    }

    public function testAssertNotRedirectRegex()
    {
        $this->dispatch('/redirect');
        $this->assertNotRedirectRegex('#zend\.fr#');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotRedirectRegex('#zend\.com$#');
    }

    public function testAssertQuery()
    {
        $this->dispatch('/tests');
        $this->assertQuery('form#myform');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertQuery('form#id');
    }

    public function testAssertXpathQuery()
    {
        $this->dispatch('/tests');
        $this->assertXpathQuery('//form[@id="myform"]');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertXpathQuery('//form[@id="id"]');
    }

    public function testAssertXpathQueryWithBadXpathUsage()
    {
        $this->dispatch('/tests');

        $this->setExpectedException('ErrorException');
        $this->assertXpathQuery('form#myform');
    }

    public function testAssertNotQuery()
    {
        $this->dispatch('/tests');
        $this->assertNotQuery('form#id');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotQuery('form#myform');
    }

    public function testAssertNotXpathQuery()
    {
        $this->dispatch('/tests');
        $this->assertNotXpathQuery('//form[@id="id"]');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotXpathQuery('//form[@id="myform"]');
    }

    public function testAssertQueryCount()
    {
        $this->dispatch('/tests');
        $this->assertQueryCount('div.top', 3);

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actually occurs 3 times' // check actual occurs is display
        );
        $this->assertQueryCount('div.top', 2);
    }

    public function testAssertXpathQueryCount()
    {
        $this->dispatch('/tests');
        $this->assertXpathQueryCount('//div[@class="top"]', 3);

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actually occurs 3 times' // check actual occurs is display
        );
        $this->assertXpathQueryCount('//div[@class="top"]', 2);
    }

    public function testAssertXpathQueryCountWithBadXpathUsage()
    {
        $this->dispatch('/tests');
        $this->assertXpathQueryCount('div.top', 0);
    }

    public function testAssertNotQueryCount()
    {
        $this->dispatch('/tests');
        $this->assertNotQueryCount('div.top', 1);
        $this->assertNotQueryCount('div.top', 2);

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotQueryCount('div.top', 3);
    }

    public function testAssertNotXpathQueryCount()
    {
        $this->dispatch('/tests');
        $this->assertNotXpathQueryCount('//div[@class="top"]', 1);
        $this->assertNotXpathQueryCount('//div[@class="top"]', 2);

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotXpathQueryCount('//div[@class="top"]', 3);
    }

    public function testAssertQueryCountMin()
    {
        $this->dispatch('/tests');
        $this->assertQueryCountMin('div.top', 1);
        $this->assertQueryCountMin('div.top', 2);
        $this->assertQueryCountMin('div.top', 3);

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actually occurs 3 times' // check actual occurs is display
        );
        $this->assertQueryCountMin('div.top', 4);
    }

    public function testAssertXpathQueryCountMin()
    {
        $this->dispatch('/tests');
        $this->assertXpathQueryCountMin('//div[@class="top"]', 1);
        $this->assertXpathQueryCountMin('//div[@class="top"]', 2);
        $this->assertXpathQueryCountMin('//div[@class="top"]', 3);

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actually occurs 3 times' // check actual occurs is display
        );
        $this->assertXpathQueryCountMin('//div[@class="top"]', 4);
    }

    public function testAssertQueryCountMax()
    {
        $this->dispatch('/tests');
        $this->assertQueryCountMax('div.top', 5);
        $this->assertQueryCountMax('div.top', 4);
        $this->assertQueryCountMax('div.top', 3);

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actually occurs 3 times' // check actual occurs is display
        );
        $this->assertQueryCountMax('div.top', 2);
    }

    public function testAssertXpathQueryCountMax()
    {
        $this->dispatch('/tests');
        $this->assertXpathQueryCountMax('//div[@class="top"]', 5);
        $this->assertXpathQueryCountMax('//div[@class="top"]', 4);
        $this->assertXpathQueryCountMax('//div[@class="top"]', 3);

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actually occurs 3 times' // check actual occurs is display
        );
        $this->assertXpathQueryCountMax('//div[@class="top"]', 2);
    }

    public function testAssertQueryContentContains()
    {
        $this->dispatch('/tests');
        $this->assertQueryContentContains('div#content', 'foo');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual content is "foo"' // check actual content is display
        );
        $this->assertQueryContentContains('div#content', 'bar');
    }

    public function testAssertXpathQueryContentContains()
    {
        $this->dispatch('/tests');
        $this->assertXpathQueryContentContains('//div[@id="content"]', 'foo');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual content is "foo"' // check actual content is display
        );
        $this->assertXpathQueryContentContains('//div[@id="content"]', 'bar');
    }

    public function testAssertNotQueryContentContains()
    {
        $this->dispatch('/tests');
        $this->assertNotQueryContentContains('div#content', 'bar');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotQueryContentContains('div#content', 'foo');
    }

    public function testAssertNotXpathQueryContentContains()
    {
        $this->dispatch('/tests');
        $this->assertNotXpathQueryContentContains('//div[@id="content"]', 'bar');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotXpathQueryContentContains('//div[@id="content"]', 'foo');
    }

    public function testAssertQueryContentRegex()
    {
        $this->dispatch('/tests');
        $this->assertQueryContentRegex('div#content', '#o{2}#');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual content is "foo"' // check actual content is display
        );
        $this->assertQueryContentRegex('div#content', '#o{3,}#');
    }

    public function testAssertXpathQueryContentRegex()
    {
        $this->dispatch('/tests');
        $this->assertXpathQueryContentRegex('//div[@id="content"]', '#o{2}#');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual content is "foo"' // check actual content is display
        );
        $this->assertXpathQueryContentRegex('//div[@id="content"]', '#o{3,}#');
    }

    public function testAssertNotQueryContentRegex()
    {
        $this->dispatch('/tests');
        $this->assertNotQueryContentRegex('div#content', '#o{3,}#');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotQueryContentRegex('div#content', '#o{2}#');
    }

    public function testAssertNotXpathQueryContentRegex()
    {
        $this->dispatch('/tests');
        $this->assertNotXpathQueryContentRegex('//div[@id="content"]', '#o{3,}#');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotXpathQueryContentRegex('//div[@id="content"]', '#o{2}#');
    }

    public function testAssertQueryWithDynamicQueryParams()
    {
        $this->getRequest()
            ->setMethod('GET')
            ->setQuery(new Parameters(array('num_get' => 5)));
        $this->dispatch('/tests');
        $this->assertQueryCount('div.get', 5);
        $this->assertXpathQueryCount('//div[@class="get"]', 5);
        $this->assertQueryCount('div.post', 0);
        $this->assertXpathQueryCount('//div[@class="post"]', 0);
    }

    public function testAssertQueryWithDynamicQueryParamsInDispatchMethod()
    {
        $this->dispatch('/tests', 'GET', array('num_get' => 5));
        $this->assertQueryCount('div.get', 5);
        $this->assertXpathQueryCount('//div[@class="get"]', 5);
        $this->assertQueryCount('div.post', 0);
        $this->assertXpathQueryCount('//div[@class="post"]', 0);
    }

    public function testAssertQueryWithDynamicQueryParamsInUrl()
    {
        $this->dispatch('/tests?foo=bar&num_get=5');
        $this->assertQueryCount('div.get', 5);
        $this->assertXpathQueryCount('//div[@class="get"]', 5);
        $this->assertQueryCount('div.post', 0);
        $this->assertXpathQueryCount('//div[@class="post"]', 0);
    }

    public function testAssertQueryWithDynamicQueryParamsInUrlAnsPostInParams()
    {
        $this->dispatch('/tests?foo=bar&num_get=5', 'POST', array('num_post' => 5));
        $this->assertQueryCount('div.get', 5);
        $this->assertXpathQueryCount('//div[@class="get"]', 5);
        $this->assertQueryCount('div.post', 5);
        $this->assertXpathQueryCount('//div[@class="post"]', 5);
    }

    public function testAssertQueryWithDynamicPostParams()
    {
        $this->getRequest()
            ->setMethod('POST')
            ->setPost(new Parameters(array('num_post' => 5)));
        $this->dispatch('/tests');
        $this->assertQueryCount('div.post', 5);
        $this->assertXpathQueryCount('//div[@class="post"]', 5);
        $this->assertQueryCount('div.get', 0);
        $this->assertXpathQueryCount('//div[@class="get"]', 0);
    }

    public function testAssertQueryWithDynamicPostParamsInDispatchMethod()
    {
        $this->dispatch('/tests', 'POST', array('num_post' => 5));
        $request = $this->getRequest();
        $this->assertEquals($request->getMethod(), 'POST');
        $this->assertQueryCount('div.post', 5);
        $this->assertXpathQueryCount('//div[@class="post"]', 5);
        $this->assertQueryCount('div.get', 0);
        $this->assertXpathQueryCount('//div[@class="get"]', 0);
    }

    public function testAssertQueryWithDynamicPutParamsInDispatchMethod()
    {
        $this->dispatch('/tests', 'PUT', array('num_post' => 5, 'foo' => 'bar'));
        $request = $this->getRequest();
        $this->assertEquals($request->getMethod(), 'PUT');
        $this->assertEquals('num_post=5&foo=bar', $request->getContent());
    }
    /*
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
        $this->assertXpathQueryCount('//div[@class="get"]', 0);
        $this->assertQueryCount('div.post', 0);
        $this->assertXpathQueryCount('//div[@class="post"]', 0);

        $this->reset();

        $this->dispatch('/tests?foo=bar&num_get=3');
        $this->assertQueryCount('div.get', 3);
        $this->assertXpathQueryCount('//div[@class="get"]', 3);
        $this->assertQueryCount('div.post', 0);
        $this->assertXpathQueryCount('//div[@class="post"]', 0);

        $this->reset();

        $this->dispatch('/tests');
        $this->assertQueryCount('div.get', 0);
        $this->assertXpathQueryCount('//div[@class="get"]', 0);
        $this->assertQueryCount('div.post', 0);
        $this->assertXpathQueryCount('//div[@class="post"]', 0);
    }

    public function testAssertWithEventShared()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../_files/application.config.with.shared.events.php'
        );
        $this->dispatch('/tests');
        $this->assertNotQuery('div#content');
        $this->assertNotXpathQuery('//div[@id="content"]');
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
        $this->assertXpathQuery('//div[@id="content"]');
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
}
