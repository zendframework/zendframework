<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\View\Console;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ConsoleModel;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage View
 */
class ExceptionStrategy implements ListenerAggregateInterface
{
    /**
     * Display exceptions?
     * @var bool
     */
    protected $displayExceptions = true;

    /**
     * A template for message to show in console when an exception has occurred.
     * @var string|callable
     */
    protected $message = <<<EOT
======================================================================
   The application has thrown an exception!
======================================================================
 :className
 :message
----------------------------------------------------------------------
:file::line
:stack
======================================================================
   Previous Exception(s):
======================================================================
:previous

EOT;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'prepareExceptionViewModel'));
    }

    /**
     * Detach aggregate listeners from the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Flag: display exceptions in error pages?
     *
     * @param  bool $displayExceptions
     * @return ExceptionStrategy
     */
    public function setDisplayExceptions($displayExceptions)
    {
        $this->displayExceptions = (bool) $displayExceptions;
        return $this;
    }

    /**
     * Should we display exceptions in error pages?
     *
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->displayExceptions;
    }

    /**
     * Get current template for message that will be shown in Console.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set template for message that will be shown in Console.
     * The message can be a string (template) or a callable (i.e. a closure).
     *
     * The closure is expected to return a string and will be called with 2 parameters:
     *        Exception $exception           - the exception being thrown
     *        boolean   $displayExceptions   - whether to display exceptions or not
     *
     * If the message is a string, one can use the following template params:
     *
     *   :className   - full class name of exception instance
     *   :message     - exception message
     *   :code        - exception code
     *   :file        - the file where the exception has been thrown
     *   :line        - the line where the exception has been thrown
     *   :stack       - full exception stack
     *
     * @param string|callable  $message
     * @return ExceptionStrategy
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Create an exception view model, and set the HTTP status code
     *
     * @todo   dispatch.error does not halt dispatch unless a response is
     *         returned. As such, we likely need to trigger rendering as a low
     *         priority dispatch.error event (or goto a render event) to ensure
     *         rendering occurs, and that munging of view models occurs when
     *         expected.
     * @param  MvcEvent $e
     * @return void
     */
    public function prepareExceptionViewModel(MvcEvent $e)
    {
        // Do nothing if no error in the event
        $error = $e->getError();
        if (empty($error)) {
            return;
        }

        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof Response) {
            return;
        }

        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                // Specifically not handling these because they are handled by routeNotFound strategy
                return;

            case Application::ERROR_EXCEPTION:
            default:
                // Prepare error message
                $exception = $e->getParam('exception');

                if (is_callable($this->message)) {
                    $callback = $this->message;
                    $message = (string) $callback($exception, $this->displayExceptions);
                } elseif ($this->displayExceptions && $exception instanceof \Exception) {
                    /* @var $exception \Exception */
                    $message = str_replace(
                        array(
                            ':className',
                            ':message',
                            ':code',
                            ':file',
                            ':line',
                            ':stack',
                            ':previous',
                        ),array(
                            get_class($exception),
                            $exception->getMessage(),
                            $exception->getCode(),
                            $exception->getFile(),
                            $exception->getLine(),
                            $exception->getTraceAsString(),
                            $exception->getPrevious(),
                        ),
                        $this->message
                    );
                } else {
                    $message = str_replace(
                        array(
                            ':className',
                            ':message',
                            ':code',
                            ':file',
                            ':line',
                            ':stack',
                            ':previous',
                        ),array(
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                        ),
                        $this->message
                    );
                }

                // Prepare view model
                $model = new ConsoleModel();
                $model->setResult($message);
                $model->setErrorLevel(1);

                // Inject it into MvcEvent
                $e->setResult($model);

                break;
        }
    }
}
