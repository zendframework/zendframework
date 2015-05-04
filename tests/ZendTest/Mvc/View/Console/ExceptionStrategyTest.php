<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\View\Console;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Console\Response;
use Zend\EventManager\EventManager;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\View\Console\ExceptionStrategy;

class ExceptionStrategyTest extends TestCase
{
    protected $strategy;

    public function setUp()
    {
        $this->strategy = new ExceptionStrategy();
    }

    public function testEventListeners()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);

        $listeners        = $events->getListeners(MvcEvent::EVENT_DISPATCH_ERROR);
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
        $this->assertTrue($found, 'MvcEvent::EVENT_DISPATCH_ERROR not found');


        $listeners        = $events->getListeners(MvcEvent::EVENT_RENDER_ERROR);
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
        $this->assertTrue($found, 'MvcEvent::EVENT_RENDER_ERROR not found');
    }

    public function testDefaultDisplayExceptions()
    {
        $this->assertTrue($this->strategy->displayExceptions(), 'displayExceptions should be true by default');
    }

    public function messageTokenProvider()
    {
        return array(
            array(':className', true),
            array(':message', true),
            array(':code', false),
            array(':file', true),
            array(':line', true),
            array(':stack', true),
        );
    }

    /**
     * @dataProvider messageTokenProvider
     */
    public function testMessageTokens($token, $found)
    {
        if ($found) {
            $this->assertContains($token, $this->strategy->getMessage(), sprintf('%s token not in message', $token));
        } else {
            $this->assertNotContains($token, $this->strategy->getMessage(), sprintf('%s token in message', $token));
        }
    }

    public function previousMessageTokenProvider()
    {
        return array(
            array(':className', true),
            array(':message', true),
            array(':code', false),
            array(':file', true),
            array(':line', true),
            array(':stack', true),
            array(':previous', true),
        );
    }

    /**
     * @dataProvider previousMessageTokenProvider
     */
    public function testPreviousMessageTokens($token, $found)
    {
        if ($found) {
            $this->assertContains($token, $this->strategy->getMessage(), sprintf('%s token not in previousMessage', $token));
        } else {
            $this->assertNotContains($token, $this->strategy->getMessage(), sprintf('%s token in previousMessage', $token));
        }
    }

    public function testCanSetMessage()
    {
        $this->strategy->setMessage('something else');

        $this->assertEquals('something else', $this->strategy->getMessage());
    }

    public function testCanSetPreviousMessage()
    {
        $this->strategy->setPreviousMessage('something else');

        $this->assertEquals('something else', $this->strategy->getPreviousMessage());
    }

    public function testPrepareExceptionViewModelNoErrorInResultGetsSameResult()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);

        $event = new MvcEvent(MvcEvent::EVENT_DISPATCH_ERROR);

        $event->setResult('something');
        $this->assertEquals('something', $event->getResult(), 'When no error has been set on the event getResult should not be modified');
    }

    public function testPrepareExceptionViewModelResponseObjectInResultGetsSameResult()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);

        $event = new MvcEvent(MvcEvent::EVENT_DISPATCH_ERROR);

        $result = new Response();
        $event->setResult($result);
        $this->assertEquals($result, $event->getResult(), 'When a response object has been set on the event getResult should not be modified');
    }

    public function testPrepareExceptionViewModelErrorsThatMustGetSameResult()
    {
        $errors = array(Application::ERROR_CONTROLLER_NOT_FOUND, Application::ERROR_CONTROLLER_INVALID, Application::ERROR_ROUTER_NO_MATCH);

        foreach ($errors as $error) {
            $events = new EventManager();
            $events->attachAggregate($this->strategy);

            $exception = new \Exception('some exception');
            $event = new MvcEvent(MvcEvent::EVENT_DISPATCH_ERROR, null, array('exception'=>$exception));
            $event->setResult('something');
            $event->setError($error);

            $events->trigger($event, null, array('exception'=>$exception));

            $this->assertEquals('something', $event->getResult(), sprintf('With an error of %s getResult should not be modified', $error));
        }
    }

    public function testPrepareExceptionViewModelErrorException()
    {
        $errors = array(Application::ERROR_EXCEPTION, 'user-defined-error');

        foreach ($errors as $error) {
            $events = new EventManager();
            $events->attachAggregate($this->strategy);

            $exception = new \Exception('message foo');
            $event = new MvcEvent(MvcEvent::EVENT_DISPATCH_ERROR, null, array('exception'=>$exception));

            $event->setError($error);

            $this->strategy->prepareExceptionViewModel($event);

            $this->assertInstanceOf('Zend\View\Model\ConsoleModel', $event->getResult());
            $this->assertNotEquals('something', $event->getResult()->getResult(), sprintf('With an error of %s getResult should have been modified', $error));
            $this->assertContains('message foo', $event->getResult()->getResult(), sprintf('With an error of %s getResult should have been modified', $error));
        }
    }

    public function testPrepareExceptionRendersPreviousMessages()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);

        $messages  = array('message foo', 'message bar', 'deepest message');
        $exception = null;
        $i         = 0;
        do {
            $exception = new \Exception($messages[$i], null, $exception);
            $i++;
        } while ($i < count($messages));

        $event = new MvcEvent(MvcEvent::EVENT_DISPATCH_ERROR, null, array('exception'=>$exception));
        $event->setError('user-defined-error');

        $events->trigger($event, null, array('exception'=>$exception)); //$this->strategy->prepareExceptionViewModel($event);

        foreach ($messages as $message) {
            $this->assertContains($message, $event->getResult()->getResult(), sprintf('Not all errors are rendered'));
        }
    }
}
