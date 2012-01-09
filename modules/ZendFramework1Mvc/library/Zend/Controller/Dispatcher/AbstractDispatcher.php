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
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Dispatcher;

use Zend\Controller\Dispatcher,
    Zend\Controller\Front as FrontController,
    Zend\Controller\Response\AbstractResponse,
    Zend\Loader\Broker;

/**
 * @uses       \Zend\Controller\Dispatcher\Exception
 * @uses       \Zend\Controller\Dispatcher
 * @uses       \Zend\Controller\Front
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractDispatcher implements Dispatcher
{
    /**
     * Helper broker instance
     * @var Broker
     */
    protected $broker;

    /**
     * Default action
     * @var string
     */
    protected $_defaultAction = 'index';

    /**
     * Default controller
     * @var string
     */
    protected $_defaultController = 'index';

    /**
     * Default module
     * @var string
     */
    protected $_defaultModule = 'application';

    /**
     * Front Controller instance
     * @var \Zend\Controller\Front
     */
    protected $_frontController;

    /**
     * Array of invocation parameters to use when instantiating action
     * controllers
     * @var array
     */
    protected $_invokeParams = array();

    /**
     * Path delimiter character
     * @var string
     */
    protected $_pathDelimiter = '\\';

    /**
     * Response object to pass to action controllers, if any
     * @var \Zend\Controller\Response\AbstractResponse|null
     */
    protected $_response = null;

    /**
     * Word delimiter characters
     * @var array
     */
    protected $_wordDelimiter = array('-', '.');

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        $this->setParams($params);
    }

    /**
     * Formats a string into a controller name.  This is used to take a raw
     * controller name, such as one stored inside a Zend_Controller_Request_Abstract
     * object, and reformat it to a proper class name that a class extending
     * Zend_Controller_Action would use.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatControllerName($unformatted)
    {
        return ucfirst($this->_formatName($unformatted)) . 'Controller';
    }

    /**
     * Formats a string into an action name.  This is used to take a raw
     * action name, such as one that would be stored inside a Zend_Controller_Request_Abstract
     * object, and reformat into a proper method name that would be found
     * inside a class extending Zend_Controller_Action.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatActionName($unformatted)
    {
        $formatted = $this->_formatName($unformatted, true);
        return strtolower(substr($formatted, 0, 1)) . substr($formatted, 1) . 'Action';
    }

    /**
     * Verify delimiter
     *
     * Verify a delimiter to use in controllers or actions. May be a single
     * string or an array of strings.
     *
     * @param string|array $spec
     * @return array
     * @throws \Zend\Controller\Dispatcher\Exception with invalid delimiters
     */
    public function _verifyDelimiter($spec)
    {
        if (is_string($spec)) {
            return (array) $spec;
        } elseif (is_array($spec)) {
            $allStrings = true;
            foreach ($spec as $delim) {
                if (!is_string($delim)) {
                    $allStrings = false;
                    break;
                }
            }

            if (!$allStrings) {
                throw new Exception('Word delimiter array must contain only strings');
            }

            return $spec;
        }

        throw new Exception('Invalid word delimiter');
    }

    /**
     * Retrieve the word delimiter character(s) used in
     * controller or action names
     *
     * @return array
     */
    public function getWordDelimiter()
    {
        return $this->_wordDelimiter;
    }

    /**
     * Set word delimiter
     *
     * Set the word delimiter to use in controllers and actions. May be a
     * single string or an array of strings.
     *
     * @param string|array $spec
     * @return \Zend\Controller\Dispatcher\AbstractDispatcher
     */
    public function setWordDelimiter($spec)
    {
        $spec = $this->_verifyDelimiter($spec);
        $this->_wordDelimiter = $spec;

        return $this;
    }

    /**
     * Retrieve the path delimiter character(s) used in
     * controller names
     *
     * @return array
     */
    public function getPathDelimiter()
    {
        return $this->_pathDelimiter;
    }

    /**
     * Set path delimiter
     *
     * Set the path delimiter to use in controllers. May be a single string or
     * an array of strings.
     *
     * @param string $spec
     * @return \Zend\Controller\Dispatcher\AbstractDispatcher
     */
    public function setPathDelimiter($spec)
    {
        if (!is_string($spec)) {
            throw new Exception('Invalid path delimiter');
        }
        $this->_pathDelimiter = $spec;

        return $this;
    }

    /**
     * Formats a string from a URI into a PHP-friendly name.
     *
     * By default, replaces words separated by the word separator character(s)
     * with camelCaps. If $isAction is false, it also preserves replaces words
     * separated by the path separation character with an underscore, making
     * the following word Title cased. All non-alphanumeric characters are
     * removed.
     *
     * @param string $unformatted
     * @param boolean $isAction Defaults to false
     * @return string
     */
    protected function _formatName($unformatted, $isAction = false)
    {
        // preserve directories
        if (!$isAction) {
            $segments = explode($this->getPathDelimiter(), $unformatted);
        } else {
            $segments = (array) $unformatted;
        }

        foreach ($segments as $key => $segment) {
            $segment        = str_replace($this->getWordDelimiter(), ' ', strtolower($segment));
            $segment        = preg_replace('/[^a-z0-9 ]/', '', $segment);
            $segments[$key] = str_replace(' ', '', ucwords($segment));
        }

        return implode('\\', $segments);
    }

    /**
     * Retrieve front controller instance
     *
     * @return \Zend\Controller\Front
     */
    public function getFrontController()
    {
        if (null === $this->_frontController) {
            $this->_frontController = FrontController::getInstance();
        }

        return $this->_frontController;
    }

    /**
     * Set front controller instance
     *
     * @param \Zend\Controller\Front $controller
     * @return \Zend\Controller\Dispatcher\AbstractDispatcher
     */
    public function setFrontController(FrontController $controller)
    {
        $this->_frontController = $controller;
        return $this;
    }

    /**
     * Add or modify a parameter to use when instantiating an action controller
     *
     * @param string $name
     * @param mixed $value
     * @return \Zend\Controller\Dispatcher\AbstractDispatcher
     */
    public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->_invokeParams[$name] = $value;
        return $this;
    }

    /**
     * Set parameters to pass to action controller constructors
     *
     * @param array $params
     * @return \Zend\Controller\Dispatcher\AbstractDispatcher
     */
    public function setParams(array $params)
    {
        $this->_invokeParams = array_merge($this->_invokeParams, $params);
        return $this;
    }

    /**
     * Retrieve a single parameter from the controller parameter stack
     *
     * @param string $name
     * @return mixed
     */
    public function getParam($name)
    {
        if(isset($this->_invokeParams[$name])) {
            return $this->_invokeParams[$name];
        }

        return null;
    }

    /**
     * Retrieve action controller instantiation parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_invokeParams;
    }

    /**
     * Clear the controller parameter stack
     *
     * By default, clears all parameters. If a parameter name is given, clears
     * only that parameter; if an array of parameter names is provided, clears
     * each.
     *
     * @param null|string|array single key or array of keys for params to clear
     * @return \Zend\Controller\Dispatcher\AbstractDispatcher
     */
    public function clearParams($name = null)
    {
        if (null === $name) {
            $this->_invokeParams = array();
        } elseif (is_string($name) && isset($this->_invokeParams[$name])) {
            unset($this->_invokeParams[$name]);
        } elseif (is_array($name)) {
            foreach ($name as $key) {
                if (is_string($key) && isset($this->_invokeParams[$key])) {
                    unset($this->_invokeParams[$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Set response object to pass to action controllers
     *
     * @param \Zend\Controller\Response\AbstractResponse|null $response
     * @return \Zend\Controller\Dispatcher\AbstractDispatcher
     */
    public function setResponse(AbstractResponse $response = null)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Return the registered response object
     *
     * @return \Zend\Controller\Response\AbstractResponse|null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Set the default controller (minus any formatting)
     *
     * @param string $controller
     * @return \Zend\Controller\Dispatcher\AbstractDispatcher
     */
    public function setDefaultControllerName($controller)
    {
        $this->_defaultController = (string) $controller;
        return $this;
    }

    /**
     * Retrieve the default controller name (minus formatting)
     *
     * @return string
     */
    public function getDefaultControllerName()
    {
        return $this->_defaultController;
    }

    /**
     * Set the default action (minus any formatting)
     *
     * @param string $action
     * @return \Zend\Controller\Dispatcher\AbstractDispatcher
     */
    public function setDefaultAction($action)
    {
        $this->_defaultAction = (string) $action;
        return $this;
    }

    /**
     * Retrieve the default action name (minus formatting)
     *
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->_defaultAction;
    }

    /**
     * Set the default module
     *
     * @param string $module
     * @return \Zend\Controller\Dispatcher\AbstractDispatcher
     */
    public function setDefaultModule($module)
    {
        $this->_defaultModule = (string) $module;
        return $this;
    }

    /**
     * Retrieve the default module
     *
     * @return string
     */
    public function getDefaultModule()
    {
        return $this->_defaultModule;
    }

    /**
     * Set helper broker instance
     * 
     * @param  Broker $broker 
     * @return AbstractDispatcher
     */
    public function setHelperBroker(Broker $broker = null)
    {
        $this->broker = $broker;
        return $this;
    }
}
