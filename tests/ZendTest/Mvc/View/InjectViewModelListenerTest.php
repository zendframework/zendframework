<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\View;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\View\Http\InjectViewModelListener;
use Zend\View\Model\ViewModel;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 */
class InjectViewModelListenerTest extends TestCase
{
    public function setUp()
    {
        $this->listener   = new InjectViewModelListener();
        $this->event      = new MvcEvent();
        $this->routeMatch = new RouteMatch(array());
        $this->event->setRouteMatch($this->routeMatch);
    }

    public function testReplacesEventModelWithChildModelIfChildIsMarkedTerminal()
    {
        $childModel  = new ViewModel();
        $childModel->setTerminal(true);
        $this->event->setResult($childModel);

        $this->listener->injectViewModel($this->event);
        $this->assertSame($childModel, $this->event->getViewModel());
    }

    public function testAddsViewModelAsChildOfEventViewModelWhenChildIsNotTerminal()
    {
        $childModel  = new ViewModel();
        $this->event->setResult($childModel);

        $this->listener->injectViewModel($this->event);
        $model = $this->event->getViewModel();
        $this->assertNotSame($childModel, $model);
        $this->assertTrue($model->hasChildren());
        $this->assertEquals(1, count($model));
        $child = false;
        foreach ($model as $child) {
            break;
        }
        $this->assertSame($childModel, $child);
    }

    public function testLackOfViewModelInResultBypassesViewModelInjection()
    {
        $this->assertNull($this->listener->injectViewModel($this->event));
        $this->assertNull($this->event->getResult());
        $this->assertFalse($this->event->getViewModel()->hasChildren());
    }

    public function testAttachesListenersAtExpectedPriorities()
    {
        $events = new EventManager();
        $events->attachAggregate($this->listener);
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH);

        $expectedCallback = array($this->listener, 'injectViewModel');
        $expectedPriority = -100;
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

        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH_ERROR);
        $found     = false;
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

    public function testDetachesListeners()
    {
        $events = new EventManager();
        $events->attachAggregate($this->listener);
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH);
        $this->assertEquals(1, count($listeners));
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH_ERROR);
        $this->assertEquals(1, count($listeners));
        $events->detachAggregate($this->listener);
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH);
        $this->assertEquals(0, count($listeners));
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH_ERROR);
        $this->assertEquals(0, count($listeners));
    }
}
