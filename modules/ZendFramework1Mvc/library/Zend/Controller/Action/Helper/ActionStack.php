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
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Action\Helper;
use Zend\Controller\Request;
use Zend\Controller\Action;

/**
 * Add to action stack
 *
 * @uses       \Zend\Controller\Action\Exception
 * @uses       \Zend\Controller\Action\Helper\AbstractHelper
 * @uses       \Zend\Controller\Plugin\ActionStack
 * @uses       \Zend\Controller\Request\Simple
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ActionStack extends AbstractHelper
{
    /**
     * @var \Zend\Controller\Plugin\ActionStack
     */
    protected $_actionStack;

    /**
     * Constructor
     *
     * Register action stack plugin
     *
     * @return void
     */
    public function __construct()
    {
        $front = \Zend\Controller\Front::getInstance();
        if (!$front->hasPlugin('Zend\Controller\Plugin\ActionStack')) {
            $this->_actionStack = new \Zend\Controller\Plugin\ActionStack();
            $front->registerPlugin($this->_actionStack, 97);
        } else {
            $this->_actionStack = $front->getPlugin('Zend\Controller\Plugin\ActionStack');
        }
    }

    /**
     * Push onto the stack
     *
     * @param  \Zend\Controller\Request\AbstractRequest $next
     * @return \Zend\Controller\Action\Helper\ActionStack Provides a fluent interface
     */
    public function pushStack(Request\AbstractRequest $next)
    {
        $this->_actionStack->pushStack($next);
        return $this;
    }

    /**
     * Push a new action onto the stack
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array  $params
     * @throws \Zend\Controller\Action\Exception
     * @return \Zend\Controller\Action\Helper\ActionStack
     */
    public function actionToStack($action, $controller = null, $module = null, array $params = array())
    {
        if ($action instanceof Request\AbstractRequest) {
            return $this->pushStack($action);
        } elseif (!is_string($action)) {
            throw new Action\Exception('ActionStack requires either a request object or minimally a string action');
        }

        $request = $this->getRequest();

        if ($request instanceof Request\AbstractRequest === false){
            throw new Action\Exception('Request object not set yet');
        }

        $controller = (null === $controller) ? $request->getControllerName() : $controller;
        $module     = (null === $module)     ? $request->getModuleName()     : $module;
        $newRequest = new Request\Simple($action, $controller, $module, $params);
        return $this->pushStack($newRequest);
    }

    /**
     * Perform helper when called as $this->_helper->actionStack() from an action controller
     *
     * Proxies to {@link simple()}
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array $params
     * @return boolean
     */
    public function direct($action, $controller = null, $module = null, array $params = array())
    {
        return $this->actionToStack($action, $controller, $module, $params);
    }
}
