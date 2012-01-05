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
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Plugin;
use Zend\Controller;
use Zend\Controller\Request;

/**
 * Handle exceptions that bubble up based on missing controllers, actions, or
 * application errors, and forward to an error handler.
 *
 * @uses       \Zend\Controller\Plugin\AbstractPlugin
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ErrorHandler extends AbstractPlugin
{
    /**
     * Const - No controller exception; controller does not exist
     */
    const EXCEPTION_NO_CONTROLLER = 'EXCEPTION_NO_CONTROLLER';

    /**
     * Const - No action exception; controller exists, but action does not
     */
    const EXCEPTION_NO_ACTION = 'EXCEPTION_NO_ACTION';

    /**
     * Const - No route exception; no routing was possible
     */
    const EXCEPTION_NO_ROUTE = 'EXCEPTION_NO_ROUTE';

    /**
     * Const - Other Exception; exceptions thrown by application controllers
     */
    const EXCEPTION_OTHER = 'EXCEPTION_OTHER';

    /**
     * Module to use for errors; defaults to default module in dispatcher
     * @var string
     */
    protected $_errorModule;

    /**
     * Controller to use for errors; defaults to 'error'
     * @var string
     */
    protected $_errorController = 'error';

    /**
     * Action to use for errors; defaults to 'error'
     * @var string
     */
    protected $_errorAction = 'error';

    /**
     * Flag; are we already inside the error handler loop?
     * @var bool
     */
    protected $_isInsideErrorHandlerLoop = false;

    /**
     * Exception count logged at first invocation of plugin
     * @var int
     */
    protected $_exceptionCountAtFirstEncounter = 0;

    /**
     * Constructor
     *
     * Options may include:
     * - module
     * - controller
     * - action
     *
     * @param  Array $options
     * @return void
     */
    public function __construct(Array $options = array())
    {
        $this->setErrorHandler($options);
    }

    /**
     * setErrorHandler() - setup the error handling options
     *
     * @param  array $options
     * @return \Zend\Controller\Plugin\ErrorHandler
     */
    public function setErrorHandler(Array $options = array())
    {
        if (isset($options['module'])) {
            $this->setErrorHandlerModule($options['module']);
        }
        if (isset($options['controller'])) {
            $this->setErrorHandlerController($options['controller']);
        }
        if (isset($options['action'])) {
            $this->setErrorHandlerAction($options['action']);
        }
        return $this;
    }

    /**
     * Set the module name for the error handler
     *
     * @param  string $module
     * @return \Zend\Controller\Plugin\ErrorHandler
     */
    public function setErrorHandlerModule($module)
    {
        $this->_errorModule = (string) $module;
        return $this;
    }

    /**
     * Retrieve the current error handler module
     *
     * @return string
     */
    public function getErrorHandlerModule()
    {
        if (null === $this->_errorModule) {
            $this->_errorModule = Controller\Front::getInstance()->getDispatcher()->getDefaultModule();
        }
        return $this->_errorModule;
    }

    /**
     * Set the controller name for the error handler
     *
     * @param  string $controller
     * @return \Zend\Controller\Plugin\ErrorHandler
     */
    public function setErrorHandlerController($controller)
    {
        $this->_errorController = (string) $controller;
        return $this;
    }

    /**
     * Retrieve the current error handler controller
     *
     * @return string
     */
    public function getErrorHandlerController()
    {
        return $this->_errorController;
    }

    /**
     * Set the action name for the error handler
     *
     * @param  string $action
     * @return \Zend\Controller\Plugin\ErrorHandler
     */
    public function setErrorHandlerAction($action)
    {
        $this->_errorAction = (string) $action;
        return $this;
    }

    /**
     * Retrieve the current error handler action
     *
     * @return string
     */
    public function getErrorHandlerAction()
    {
        return $this->_errorAction;
    }

    /**
     * Route shutdown hook -- check for router exceptions
     * 
     * @param \Zend\Controller\Request\AbstractRequest $request 
     */
    public function routeShutdown(Request\AbstractRequest $request)
    {
        $this->_handleError($request);
    }

    /**
     * Post dispatch hook -- check for exceptions and dispatch error handler if
     * necessary
     *
     * @param \Zend\Controller\Request\AbstractRequest $request
     */
    public function postDispatch(Request\AbstractRequest $request)
    {
        $this->_handleError($request);
    }

    /**
     * Handle errors and exceptions
     *
     * If the 'noErrorHandler' front controller flag has been set,
     * returns early.
     *
     * @param  \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    protected function _handleError(Request\AbstractRequest $request)
    {
        $frontController = Controller\Front::getInstance();
        if ($frontController->getParam('noErrorHandler')) {
            return;
        }

        $response = $this->getResponse();

        if ($this->_isInsideErrorHandlerLoop) {
            $exceptions = $response->getException();
            if (count($exceptions) > $this->_exceptionCountAtFirstEncounter) {
                // Exception thrown by error handler; tell the front controller to throw it
                $frontController->throwExceptions(true);
                throw array_pop($exceptions);
            }
        }

        // check for an exception AND allow the error handler controller the option to forward
        if (($response->isException()) && (!$this->_isInsideErrorHandlerLoop)) {
            $this->_isInsideErrorHandlerLoop = true;

            // Get exception information
            $error            = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            $exceptions       = $response->getException();
            $exception        = $exceptions[0];
            $exceptionType    = get_class($exception);
            $error->exception = $exception;
            switch ($exceptionType) {
                case 'Zend\Controller\Router\Exception':
                    if (404 == $exception->getCode()) {
                        $error->type = self::EXCEPTION_NO_ROUTE;
                    } else {
                        $error->type = self::EXCEPTION_OTHER;
                    }
                    break;
                case 'Zend\Controller\Dispatcher\Exception':
                    $error->type = self::EXCEPTION_NO_CONTROLLER;
                    break;
                case 'Zend\Controller\Action\Exception':
                    if (404 == $exception->getCode()) {
                        $error->type = self::EXCEPTION_NO_ACTION;
                    } else {
                        $error->type = self::EXCEPTION_OTHER;
                    }
                    break;
                default:
                    $error->type = self::EXCEPTION_OTHER;
                    break;
            }

            // Keep a copy of the original request
            $error->request = clone $request;

            // get a count of the number of exceptions encountered
            $this->_exceptionCountAtFirstEncounter = count($exceptions);

            // Forward to the error handler
            $request->setParam('error_handler', $error)
                    ->setModuleName($this->getErrorHandlerModule())
                    ->setControllerName($this->getErrorHandlerController())
                    ->setActionName($this->getErrorHandlerAction())
                    ->setDispatched(false);
        }
    }
}
