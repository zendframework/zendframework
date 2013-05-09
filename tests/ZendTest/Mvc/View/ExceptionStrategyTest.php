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
use Zend\Http\Response;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\View\Http\ExceptionStrategy;
use Zend\View\Model\ViewModel;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 */
class ExceptionStrategyTest extends TestCase
{
    public function setUp()
    {
        $this->strategy = new ExceptionStrategy();
    }

    public function testDisplayExceptionsIsDisabledByDefault()
    {
        $this->assertFalse($this->strategy->displayExceptions());
    }

    public function testDisplayExceptionsFlagIsMutable()
    {
        $this->strategy->setDisplayExceptions(true);
        $this->assertTrue($this->strategy->displayExceptions());
    }

    public function testExceptionTemplateHasASaneDefault()
    {
        $this->assertEquals('error', $this->strategy->getExceptionTemplate());
    }

    public function testExceptionTemplateIsMutable()
    {
        $this->strategy->setExceptionTemplate('pages/error');
        $this->assertEquals('pages/error', $this->strategy->getExceptionTemplate());
    }

    public function test404ApplicationErrorsResultInNoOperations()
    {
        $event = new MvcEvent();
        foreach (array(Application::ERROR_CONTROLLER_NOT_FOUND, Application::ERROR_CONTROLLER_INVALID) as $error) {
            $event->setError($error);
            $this->strategy->prepareExceptionViewModel($event);
            $response = $event->getResponse();
            if (null !== $response) {
                $this->assertNotEquals(500, $response->getStatusCode());
            }
            $model = $event->getResult();
            if (null !== $model) {
                $variables = $model->getVariables();
                $this->assertArrayNotHasKey('message', $variables);
                $this->assertArrayNotHasKey('exception', $variables);
                $this->assertArrayNotHasKey('display_exceptions', $variables);
                $this->assertNotEquals('error', $model->getTemplate());
            }
        }
    }

    public function testCatchesApplicationExceptions()
    {
        $exception = new \Exception;
        $event     = new MvcEvent();
        $event->setParam('exception', $exception)
              ->setError(Application::ERROR_EXCEPTION);
        $this->strategy->prepareExceptionViewModel($event);

        $response = $event->getResponse();
        $this->assertTrue($response->isServerError());

        $model = $event->getResult();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $model);
        $this->assertEquals($this->strategy->getExceptionTemplate(), $model->getTemplate());

        $variables = $model->getVariables();
        $this->assertArrayHasKey('message', $variables);
        $this->assertContains('error occurred', $variables['message']);
        $this->assertArrayHasKey('exception', $variables);
        $this->assertSame($exception, $variables['exception']);
        $this->assertArrayHasKey('display_exceptions', $variables);
        $this->assertEquals($this->strategy->displayExceptions(), $variables['display_exceptions']);
    }

    public function testCatchesUnknownErrorTypes()
    {
        $exception = new \Exception;
        $event     = new MvcEvent();
        $event->setParam('exception', $exception)
              ->setError('custom_error');
        $this->strategy->prepareExceptionViewModel($event);

        $response = $event->getResponse();
        $this->assertTrue($response->isServerError());
    }

    public function testEmptyErrorInEventResultsInNoOperations()
    {
        $event = new MvcEvent();
        $this->strategy->prepareExceptionViewModel($event);
        $response = $event->getResponse();
        if (null !== $response) {
            $this->assertNotEquals(500, $response->getStatusCode());
        }
        $model = $event->getResult();
        if (null !== $model) {
            $variables = $model->getVariables();
            $this->assertArrayNotHasKey('message', $variables);
            $this->assertArrayNotHasKey('exception', $variables);
            $this->assertArrayNotHasKey('display_exceptions', $variables);
            $this->assertNotEquals('error', $model->getTemplate());
        }
    }

    public function testDoesNothingIfEventResultIsAResponse()
    {
        $event = new MvcEvent();
        $response = new Response();
        $event->setResponse($response);
        $event->setResult($response);
        $event->setError('foobar');

        $this->assertNull($this->strategy->prepareExceptionViewModel($event));
    }

    public function testAttachesListenerAtExpectedPriority()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH_ERROR);

        $expectedCallback = array($this->strategy, 'prepareExceptionViewModel');
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

    public function testDetachesListeners()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH_ERROR);
        $this->assertEquals(1, count($listeners));
        $events->detachAggregate($this->strategy);
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH_ERROR);
        $this->assertEquals(0, count($listeners));
    }

    public function testReuseResponseStatusCodeIfItExists()
    {
        $event = new MvcEvent();
        $response = new Response();
        $response->setStatusCode(401);
        $event->setResponse($response);
        $this->strategy->prepareExceptionViewModel($event);
        $response = $event->getResponse();
        if (null !== $response) {
            $this->assertEquals(401, $response->getStatusCode());
        }
        $model = $event->getResult();
        if (null !== $model) {
            $variables = $model->getVariables();
            $this->assertArrayNotHasKey('message', $variables);
            $this->assertArrayNotHasKey('exception', $variables);
            $this->assertArrayNotHasKey('display_exceptions', $variables);
            $this->assertNotEquals('error', $model->getTemplate());
        }
    }
}
