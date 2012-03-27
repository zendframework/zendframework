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
use Zend\Controller\Request;

/**
 * Manage a stack of actions
 *
 * @uses       \Zend\Controller\Exception
 * @uses       \Zend\Controller\Plugin\AbstractPlugin
 * @uses       \Zend\Registry
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ActionStack extends AbstractPlugin
{
    /** @var \Zend\Registry */
    protected $_registry;

    /**
     * Registry key under which actions are stored
     * @var string
     */
    protected $_registryKey = 'Zend_Controller_Plugin_ActionStack';

    /**
     * Valid keys for stack items
     * @var array
     */
    protected $_validKeys = array(
        'module',
        'controller',
        'action',
        'params'
    );

    /**
     * Flag to determine whether request parameters are cleared between actions, or whether new parameters
     * are added to existing request parameters.
     *
     * @var Bool
     */
    protected $_clearRequestParams = false;

    /**
     * Constructor
     *
     * @param  \Zend\Registry $registry
     * @param  string $key
     * @return void
     */
    public function __construct(\Zend\Registry $registry = null, $key = null)
    {
        if (null === $registry) {
            $registry = \Zend\Registry::getInstance();
        }
        $this->setRegistry($registry);

        if (null !== $key) {
            $this->setRegistryKey($key);
        } else {
            $key = $this->getRegistryKey();
        }

        $registry[$key] = array();
    }

    /**
     * Set registry object
     *
     * @param  \Zend\Registry $registry
     * @return \Zend\Controller\Plugin\ActionStack
     */
    public function setRegistry(\Zend\Registry $registry)
    {
        $this->_registry = $registry;
        return $this;
    }

    /**
     * Retrieve registry object
     *
     * @return \Zend\Registry
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

    /**
     * Retrieve registry key
     *
     * @return string
     */
    public function getRegistryKey()
    {
        return $this->_registryKey;
    }

    /**
     * Set registry key
     *
     * @param  string $key
     * @return \Zend\Controller\Plugin\ActionStack
     */
    public function setRegistryKey($key)
    {
        $this->_registryKey = (string) $key;
        return $this;
    }

    /**
     *  Set clearRequestParams flag
     *
     *  @param  bool $clearRequestParams
     *  @return \Zend\Controller\Plugin\ActionStack
     */
    public function setClearRequestParams($clearRequestParams)
    {
        $this->_clearRequestParams = (bool) $clearRequestParams;
        return $this;
    }

    /**
     * Retrieve clearRequestParams flag
     *
     * @return bool
     */
    public function getClearRequestParams()
    {
        return $this->_clearRequestParams;
    }

    /**
     * Retrieve action stack
     *
     * @return array
     */
    public function getStack()
    {
        $registry = $this->getRegistry();
        $stack    = $registry[$this->getRegistryKey()];
        return $stack;
    }

    /**
     * Save stack to registry
     *
     * @param  array $stack
     * @return \Zend\Controller\Plugin\ActionStack
     */
    protected function _saveStack(array $stack)
    {
        $registry = $this->getRegistry();
        $registry[$this->getRegistryKey()] = $stack;
        return $this;
    }

    /**
     * Push an item onto the stack
     *
     * @param  \Zend\Controller\Request\AbstractRequest $next
     * @return \Zend\Controller\Plugin\ActionStack
     */
    public function pushStack(Request\AbstractRequest $next)
    {
        $stack = $this->getStack();
        array_push($stack, $next);
        return $this->_saveStack($stack);
    }

    /**
     * Pop an item off the action stack
     *
     * @return false|\Zend\Controller\Request\AbstractRequest
     */
    public function popStack()
    {
        $stack = $this->getStack();
        if (0 == count($stack)) {
            return false;
        }

        $next = array_pop($stack);
        $this->_saveStack($stack);

        if (!$next instanceof Request\AbstractRequest) {
            throw new end\Controller\Exception('ArrayStack should only contain request objects');
        }
        $action = $next->getActionName();
        if (empty($action)) {
            return $this->popStack($stack);
        }

        $request    = $this->getRequest();
        $controller = $next->getControllerName();
        if (empty($controller)) {
            $next->setControllerName($request->getControllerName());
        }

        $module = $next->getModuleName();
        if (empty($module)) {
            $next->setModuleName($request->getModuleName());
        }

        return $next;
    }

    /**
     * postDispatch() plugin hook -- check for actions in stack, and dispatch if any found
     *
     * @param  \Zend\Controller\Request\AbstractRequest $request
     * @return void
     */
    public function postDispatch(Request\AbstractRequest $request)
    {
        // Don't move on to next request if this is already an attempt to
        // forward
        if (!$request->isDispatched()) {
            return;
        }

        $this->setRequest($request);
        $stack = $this->getStack();
        if (empty($stack)) {
            return;
        }
        $next = $this->popStack();
        if (!$next) {
            return;
        }

        $this->forward($next);
    }

    /**
     * Forward request with next action
     *
     * @param  array $next
     * @return void
     */
    public function forward(Request\AbstractRequest $next)
    {
        $request = $this->getRequest();
        if ($this->getClearRequestParams()) {
            $request->clearParams();
        }

        $request->setModuleName($next->getModuleName())
                ->setControllerName($next->getControllerName())
                ->setActionName($next->getActionName())
                ->setParams($next->getParams())
                ->setDispatched(false);
    }
}
