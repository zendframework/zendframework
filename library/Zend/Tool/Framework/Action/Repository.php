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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Action;

use \Zend\Tool\Framework\Action,
    \Zend\Tool\Framework\RegistryEnabled,
    \Zend\Tool\Framework\Exception;

/**
 * @uses       ArrayIterator
 * @uses       Countable
 * @uses       IteratorAggregate
 * @uses       \Zend\Tool\Framework\Action\Exception
 * @uses       \Zend\Tool\Framework\RegistryEnabled
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Repository implements RegistryEnabled, \IteratorAggregate, \Countable
{

    /**
     * @var \Zend\Tool\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @var array
     */
    protected $_actions = array();

    /**
     * setRegistry()
     *
     * @param \Zend\Tool\Framework\Registry $registry
     */
    public function setRegistry(\Zend\Tool\Framework\Registry $registry)
    {
        $this->_registry = $registry;
    }

    /**
     * addAction()
     *
     * @param \Zend\Tool\Framework\Action $action
     * @return \Zend\Tool\Framework\Action\Repository
     */
    public function addAction(Action $action, $overrideExistingAction = false)
    {
        $actionName = $action->getName();

        if ($actionName == '' || $actionName == 'Base') {
            throw new Exception\InvalidArgumentException('An action name for the provided action could not be determined.');
        }

        if (!$overrideExistingAction && array_key_exists(strtolower($actionName), $this->_actions)) {
            throw new Exception\InvalidArgumentException('An action by the name ' . $actionName
                . ' is already registered and $overrideExistingAction is set to false.');
        }

        $this->_actions[strtolower($actionName)] = $action;
        return $this;
    }

    /**
     * process() - this is called when the client is done constructing (after init())
     *
     * @return unknown
     */
    public function process()
    {
        return null;
    }

    /**
     * getActions() - get all actions in the repository
     *
     * @return array
     */
    public function getActions()
    {
        return $this->_actions;
    }

    /**
     * getAction() - get an action by a specific name
     *
     * @param string $actionName
     * @return \Zend\Tool\Framework\Action
     */
    public function getAction($actionName)
    {
        if (!array_key_exists(strtolower($actionName), $this->_actions)) {
            return null;
        }

        return $this->_actions[strtolower($actionName)];
    }

    /**
     * count() required by the Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->_actions);
    }

    /**
     * getIterator() - get all actions, this supports the IteratorAggregate interface
     *
     * @return array
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_actions);
    }

}
