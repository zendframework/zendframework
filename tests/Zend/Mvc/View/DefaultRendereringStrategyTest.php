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
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mvc\View;

use PHPUnit_Framework_TestCase as TestCase,
    ReflectionClass,
    stdClass,
    Zend\EventManager\Event,
    Zend\EventManager\EventManager,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\View\DefaultRenderingStrategy,
    Zend\Registry,
    Zend\View\Helper\Placeholder\Registry as PlaceholderRegistry,
    Zend\View\Model,
    Zend\View\Renderer\FeedRenderer,
    Zend\View\Renderer\JsonRenderer,
    Zend\View\Renderer\PhpRenderer,
    Zend\View\Resolver\TemplateMapResolver,
    Zend\View\View,
    Zend\View\ViewEvent;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DefaultRenderingStrategyTest extends TestCase
{
    protected $event;
    protected $request;
    protected $response;
    protected $view;

    public function setUp()
    {
        $this->view     = new View();
        $this->request  = new Request();
        $this->response = new Response();
        $this->event    = new MvcEvent();
        $this->renderer = new PhpRenderer();

        $this->event->setRequest($this->request)
                    ->setResponse($this->response);

        $this->strategy = new DefaultRenderingStrategy($this->view);
    }

    public function testAttachesRendererAtExpectedPriority()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $listeners = $events->getListeners('render');

        $expectedCallback = array($this->strategy, 'render');
        $expectedPriority = -10000;
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
        $this->assertTrue($found, 'Renderer not found');
    }

    public function testCanDetachListenersFromEventManager()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $this->assertEquals(1, count($events->getListeners('render')));

        $events->detachAggregate($this->strategy);
        $this->assertEquals(0, count($events->getListeners('render')));
    }

    public function testWillRenderAlternateStrategyWhenSelected()
    {
        $renderer = new TestAsset\DumbStrategy();
        $this->view->addRenderingStrategy(function ($e) use ($renderer) {
            return $renderer;
        }, 100);
        $model = new Model\ViewModel(array('foo' => 'bar'));
        $model->setOption('template', 'content');
        $this->event->setResult($model);

        $result = $this->strategy->render($this->event);
        $this->assertSame($this->response, $result);

        $expected = sprintf('content (%s): %s', json_encode(array('template' => 'content')), json_encode(array('foo' => 'bar')));
    }

    public function testLayoutTemplateIsLayoutByDefault()
    {
        $this->assertEquals('layout', $this->strategy->getLayoutTemplate());
    }

    public function testLayoutTemplateIsMutable()
    {
        $this->strategy->setLayoutTemplate('alternate/layout');
        $this->assertEquals('alternate/layout', $this->strategy->getLayoutTemplate());
    }

    public function testBypassesRenderingIfResultIsAResponse()
    {
        $renderer = new TestAsset\DumbStrategy();
        $this->view->addRenderingStrategy(function ($e) use ($renderer) {
            return $renderer;
        }, 100);
        $model = new Model\ViewModel(array('foo' => 'bar'));
        $model->setOption('template', 'content');
        $this->event->setViewModel($model);
        $this->event->setResult($this->response);

        $result = $this->strategy->render($this->event);
        $this->assertSame($this->response, $result);
    }
}
