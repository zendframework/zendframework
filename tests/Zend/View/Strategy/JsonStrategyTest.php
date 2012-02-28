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
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Strategy;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\EventManager\EventManager,
    Zend\Http\Request as HttpRequest,
    Zend\Http\Response as HttpResponse,
    Zend\View\Model,
    Zend\View\Renderer\JsonRenderer,
    Zend\View\Strategy\JsonStrategy,
    Zend\View\ViewEvent;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $this->event->setModel(new Model\JsonModel());
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertSame($this->renderer, $result);
    }

    public function testJsonAcceptHeaderSelectsJsonStrategy()
    {
        $request = new HttpRequest();
        $request->headers()->addHeaderLine('Accept', 'application/json');
        $this->event->setRequest($request);
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertSame($this->renderer, $result);
    }

    public function testLackOfJsonModelOrAcceptHeaderDoesNotSelectJsonStrategy()
    {
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertNotSame($this->renderer, $result);
        $this->assertNull($result);
    }

    protected function assertResponseNotInjected()
    {
        $content = $this->response->getContent();
        $headers = $this->response->headers();
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
        $headers = $this->response->headers();
        $this->assertEquals($expected, $content);
        $this->assertTrue($headers->has('content-type'));
        $this->assertEquals('application/json', $headers->get('content-type')->getFieldValue());
    }

    public function testReturnsNullWhenCannotSelectRenderer()
    {
        $model   = new Model\ViewModel();
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
}
