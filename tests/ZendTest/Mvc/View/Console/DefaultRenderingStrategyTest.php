<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\View\Console;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\View\Console\DefaultRenderingStrategy;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Response;
use Zend\View\Model;
use ZendTest\Console\TestAssets\ConsoleAdapter;
use ZendTest\ModuleManager\TestAsset\MockApplication;

class DefaultRenderingStrategyTest extends TestCase
{
    protected $strategy;

    public function setUp()
    {
        $this->strategy = new DefaultRenderingStrategy();
    }

    public function testAttachesRendererAtExpectedPriority()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $listeners = $events->getListeners(MvcEvent::EVENT_RENDER);

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
        $this->assertEquals(1, count($events->getListeners(MvcEvent::EVENT_RENDER)));

        $events->detachAggregate($this->strategy);
        $this->assertEquals(0, count($events->getListeners(MvcEvent::EVENT_RENDER)));
    }

    public function testIgnoresNonConsoleModelNotContainingResultKeyWhenObtainingResult()
    {
        //Register console service
        $sm = new ServiceManager();
        $sm->setService('console', new ConsoleAdapter());

        $mockApplication = new MockApplication;
        $mockApplication->setServiceManager($sm);

        $event    = new MvcEvent();
        $event->setApplication($mockApplication);

        $model    = new Model\ViewModel(array('content' => 'Page not found'));
        $response = new Response();
        $event->setResult($model);
        $event->setResponse($response);
        $this->strategy->render($event);
        $content = $response->getContent();
        $this->assertNotContains('Page not found', $content);
    }
}
