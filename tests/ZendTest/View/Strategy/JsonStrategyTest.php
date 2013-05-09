<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Strategy;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManager;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\View\Model\ModelInterface as Model;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\JsonRenderer;
use Zend\View\Strategy\JsonStrategy;
use Zend\View\ViewEvent;
use Zend\Stdlib\Parameters;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTest
 */
class JsonStrategyTest extends TestCase
{
    public function setUp()
    {
        $this->renderer = new JsonRenderer;
        $this->strategy = new JsonStrategy($this->renderer);
        $this->event    = new ViewEvent();
        $this->response = new HttpResponse();
    }

    public function testJsonModelSelectsJsonStrategy()
    {
        $this->event->setModel(new JsonModel());
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertSame($this->renderer, $result);
    }

    /**
     * @group #2410
     */
    public function testJsonAcceptHeaderDoesNotSelectJsonStrategy()
    {
        $request = new HttpRequest();
        $request->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->event->setRequest($request);
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertNotSame($this->renderer, $result);
    }

    /**
     * @group #2410
     */
    public function testJavascriptAcceptHeaderDoesNotSelectJsonStrategy()
    {
        $request = new HttpRequest();
        $request->getHeaders()->addHeaderLine('Accept', 'application/javascript');
        $this->event->setRequest($request);
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertNotSame($this->renderer, $result);
    }

    /**
     * @group #2410
     */
    public function testJsonModelJavascriptAcceptHeaderDoesNotSetJsonpCallback()
    {
        $this->event->setModel(new JsonModel());
        $request = new HttpRequest();
        $request->getHeaders()->addHeaderLine('Accept', 'application/javascript');
        $request->setQuery(new Parameters(array('callback' => 'foo')));
        $this->event->setRequest($request);
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertSame($this->renderer, $result);
        $this->assertFalse($result->hasJsonpCallback());
    }

    public function testLackOfJsonModelDoesNotSelectJsonStrategy()
    {
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertNotSame($this->renderer, $result);
        $this->assertNull($result);
    }

    protected function assertResponseNotInjected()
    {
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertTrue(empty($content));
        $this->assertFalse($headers->has('content-type'));
    }

    public function testNonMatchingRendererDoesNotInjectResponse()
    {
        $this->event->setResponse($this->response);

        // test empty renderer
        $this->strategy->injectResponse($this->event);
        $this->assertResponseNotInjected();

        // test non-matching renderer
        $renderer = new JsonRenderer();
        $this->event->setRenderer($renderer);
        $this->strategy->injectResponse($this->event);
        $this->assertResponseNotInjected();
    }

    public function testNonStringResultDoesNotInjectResponse()
    {
        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult($this->response);

        $this->strategy->injectResponse($this->event);
        $this->assertResponseNotInjected();
    }

    public function testMatchingRendererAndStringResultInjectsResponse()
    {
        $expected = json_encode(array('foo' => 'bar'));
        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult($expected);

        $this->strategy->injectResponse($this->event);
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertEquals($expected, $content);
        $this->assertTrue($headers->has('content-type'));
        $this->assertContains('application/json', $headers->get('content-type')->getFieldValue());
    }

    public function testMatchingRendererAndStringResultInjectsResponseJsonp()
    {
        $expected = json_encode(array('foo' => 'bar'));
        $this->renderer->setJsonpCallback('foo');
        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult($expected);

        $this->strategy->injectResponse($this->event);
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertEquals($expected, $content);
        $this->assertTrue($headers->has('content-type'));
        $this->assertContains('application/javascript', $headers->get('content-type')->getFieldValue());
    }

    public function testReturnsNullWhenCannotSelectRenderer()
    {
        $model   = new ViewModel();
        $request = new HttpRequest();
        $this->event->setModel($model);
        $this->event->setRequest($request);

        $this->assertNull($this->strategy->selectRenderer($this->event));
    }

    public function testAttachesListenersAtExpectedPriorities()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);

        foreach (array('renderer' => 'selectRenderer', 'response' => 'injectResponse') as $event => $method) {
            $listeners        = $events->getListeners($event);
            $expectedCallback = array($this->strategy, $method);
            $expectedPriority = 1;
            $found            = false;
            foreach ($listeners as $listener) {
                $callback = $listener->getCallback();
                if ($callback === $expectedCallback) {
                    if ($listener->getMetadatum('priority') == $expectedPriority) {
                        $found = true;
                        break;
                    }
                }
            }
            $this->assertTrue($found, 'Listener not found');
        }
    }

    public function testCanAttachListenersAtSpecifiedPriority()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy, 1000);

        foreach (array('renderer' => 'selectRenderer', 'response' => 'injectResponse') as $event => $method) {
            $listeners        = $events->getListeners($event);
            $expectedCallback = array($this->strategy, $method);
            $expectedPriority = 1000;
            $found            = false;
            foreach ($listeners as $listener) {
                $callback = $listener->getCallback();
                if ($callback === $expectedCallback) {
                    if ($listener->getMetadatum('priority') == $expectedPriority) {
                        $found = true;
                        break;
                    }
                }
            }
            $this->assertTrue($found, 'Listener not found');
        }
    }

    public function testDetachesListeners()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $listeners = $events->getListeners('renderer');
        $this->assertEquals(1, count($listeners));
        $listeners = $events->getListeners('response');
        $this->assertEquals(1, count($listeners));
        $events->detachAggregate($this->strategy);
        $listeners = $events->getListeners('renderer');
        $this->assertEquals(0, count($listeners));
        $listeners = $events->getListeners('response');
        $this->assertEquals(0, count($listeners));
    }

    public function testDefaultsToUtf8CharsetWhenCreatingJavascriptHeader()
    {
        $expected = json_encode(array('foo' => 'bar'));
        $this->renderer->setJsonpCallback('foo');
        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult($expected);

        $this->strategy->injectResponse($this->event);
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertEquals($expected, $content);
        $this->assertTrue($headers->has('content-type'));
        $this->assertContains('application/javascript; charset=utf-8', $headers->get('content-type')->getFieldValue());
    }

    public function testDefaultsToUtf8CharsetWhenCreatingJsonHeader()
    {
        $expected = json_encode(array('foo' => 'bar'));
        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult($expected);

        $this->strategy->injectResponse($this->event);
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertEquals($expected, $content);
        $this->assertTrue($headers->has('content-type'));
        $this->assertContains('application/json; charset=utf-8', $headers->get('content-type')->getFieldValue());
    }

    public function testUsesProvidedCharsetWhenCreatingJavascriptHeader()
    {
        $expected = json_encode(array('foo' => 'bar'));
        $this->renderer->setJsonpCallback('foo');
        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult($expected);

        $this->strategy->setCharset('utf-16');
        $this->strategy->injectResponse($this->event);
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertEquals($expected, $content);
        $this->assertTrue($headers->has('content-type'));
        $this->assertContains('application/javascript; charset=utf-16', $headers->get('content-type')->getFieldValue());
    }

    public function testUsesProvidedCharsetWhenCreatingJsonHeader()
    {
        $expected = json_encode(array('foo' => 'bar'));
        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult($expected);

        $this->strategy->setCharset('utf-16');
        $this->strategy->injectResponse($this->event);
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertEquals($expected, $content);
        $this->assertTrue($headers->has('content-type'));
        $this->assertContains('application/json; charset=utf-16', $headers->get('content-type')->getFieldValue());
    }

    public function testCharsetIsUtf8ByDefault()
    {
        $this->assertEquals('utf-8', $this->strategy->getCharset());
    }

    public function testCharsetIsMutable()
    {
        $this->strategy->setCharset('iso-8859-1');
        $this->assertEquals('iso-8859-1', $this->strategy->getCharset());
    }

    public function multibyteCharsets()
    {
        return array(
            'utf-16' => array('utf-16'),
            'utf-32' => array('utf-32'),
        );
    }

    /**
     * @dataProvider multibyteCharsets
     */
    public function testContentTransferEncodingHeaderSetToBinaryForSpecificMultibyteCharsets($charset)
    {
        $this->strategy->setCharset($charset);

        $this->event->setResponse($this->response);
        $this->event->setRenderer($this->renderer);
        $this->event->setResult(json_encode(array('foo' => 'bar')));

        $this->strategy->injectResponse($this->event);
        $content = $this->response->getContent();
        $headers = $this->response->getHeaders();
        $this->assertTrue($headers->has('content-transfer-encoding'));
        $this->assertEquals('BINARY', $headers->get('content-transfer-encoding')->getFieldValue());
    }
}
