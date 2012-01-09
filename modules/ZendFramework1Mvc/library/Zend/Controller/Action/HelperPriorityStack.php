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
 * @subpackage Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Action;

use ArrayObject,
    Zend\Controller\Action;

/**
 * @uses       \Zend\Controller\Action\Exception
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HelperPriorityStack implements \IteratorAggregate, \ArrayAccess, \Countable
{

    protected $_helpersByPriority   = array();
    protected $_helpersByNameRef    = array();
    protected $_nextDefaultPriority = 1;

    /**
     * Magic property overloading for returning helper by name
     *
     * @param string $helperName    The helper name
     * @return \Zend\Controller\Action\Helper\AbstractHelper
     */
    public function __get($helperName)
    {
        $helperName = $this->normalizeHelperName($helperName);
        if (!array_key_exists($helperName, $this->_helpersByNameRef)) {
            return false;
        }

        return $this->_helpersByNameRef[$helperName];
    }

    /**
     * Magic property overloading for returning if helper is set by name
     *
     * @param string $helperName    The helper name
     * @return \Zend\Controller\Action\Helper\AbstractHelper
     */
    public function __isset($helperName)
    {
        $helperName = $this->normalizeHelperName($helperName);
        return array_key_exists($helperName, $this->_helpersByNameRef);
    }

    /**
     * Magic property overloading for unsetting if helper is exists by name
     *
     * @param string $helperName    The helper name
     * @return \Zend\Controller\Action\Helper\AbstractHelper
     */
    public function __unset($helperName)
    {
        $helperName = $this->normalizeHelperName($helperName);
        return $this->offsetUnset($helperName);
    }

    /**
     * push helper onto the stack
     *
     * @param \Zend\Controller\Action\Helper\AbstractHelper $helper
     * @return \Zend\Controller\Action\HelperBroker\PriorityStack
     */
    public function push(Helper\AbstractHelper $helper)
    {
        $this->offsetSet($this->getNextFreeHigherPriority(), $helper);
        return $this;
    }

    /**
     * Return something iterable
     *
     * @return array
     */
    public function getIterator()
    {
        return new ArrayObject($this->_helpersByPriority);
    }

    /**
     * offsetExists()
     *
     * @param int|string $priorityOrHelperName
     * @return \Zend\Controller\Action\HelperBroker\PriorityStack
     */
    public function offsetExists($priorityOrHelperName)
    {
        if (is_string($priorityOrHelperName)) {
            return array_key_exists($priorityOrHelperName, $this->_helpersByNameRef);
        } else {
            return array_key_exists($priorityOrHelperName, $this->_helpersByPriority);
        }
    }

    /**
     * offsetGet()
     *
     * @param int|string $priorityOrHelperName
     * @return \Zend\Controller\Action\HelperBroker\PriorityStack
     */
    public function offsetGet($priorityOrHelperName)
    {
        $priorityOrHelperName = $this->normalizeHelperName($priorityOrHelperName);
        if (!$this->offsetExists($priorityOrHelperName)) {
            throw new Action\Exception('A helper with priority ' . $priorityOrHelperName . ' does not exist.');
        }

        if (is_string($priorityOrHelperName)) {
            return $this->_helpersByNameRef[$priorityOrHelperName];
        } else {
            return $this->_helpersByPriority[$priorityOrHelperName];
        }
    }

    /**
     * offsetSet()
     *
     * @param int $priority
     * @param \Zend\Controller\Action\Helper\AbstractHelper $helper
     * @return \Zend\Controller\Action\HelperBroker\PriorityStack
     */
    public function offsetSet($priority, $helper)
    {
        $priority = (int) $priority;

        if (!$helper instanceof Helper\AbstractHelper) {
            throw new Exception('$helper must extend Zend\Controller\Action\Helper\AbstractHelper');
        }

        $name = $this->normalizeHelperName($helper->getName());

        if (array_key_exists($name, $this->_helpersByNameRef)) {
            // remove any object with the same name
            $this->offsetUnset($name);
        }

        if (array_key_exists($priority, $this->_helpersByPriority)) {
            $priority = $this->getNextFreeHigherPriority($priority);  // ensures LIFO
        }

        $this->_helpersByPriority[$priority] = $helper;
        $this->_helpersByNameRef[$name]      = $helper;

        if ($priority == ($nextFreeDefault = $this->getNextFreeHigherPriority($this->_nextDefaultPriority))) {
            $this->_nextDefaultPriority = $nextFreeDefault;
        }

        krsort($this->_helpersByPriority);  // always make sure priority and LIFO are both enforced
        return $this;
    }

    /**
     * offsetUnset()
     *
     * @param int|string $priorityOrHelperName Priority integer or the helper name
     * @return \Zend\Controller\Action\HelperBroker\PriorityStack
     */
    public function offsetUnset($priorityOrHelperName)
    {
        if (!$this->offsetExists($priorityOrHelperName)) {
            throw new Exception('A helper with priority or name ' . $priorityOrHelperName . ' does not exist.');
        }

        if (is_string($priorityOrHelperName)) {
            $helperName = $priorityOrHelperName;
            $helper = $this->_helpersByNameRef[$helperName];
            $priority = array_search($helper, $this->_helpersByPriority, true);
        } else {
            $priority = $priorityOrHelperName;
            $helperName = $this->_helpersByPriority[$priorityOrHelperName]->getName();
        }

        unset($this->_helpersByNameRef[$helperName]);
        unset($this->_helpersByPriority[$priority]);
        return $this;
    }

    /**
     * return the count of helpers
     *
     * @return int
     */
    public function count()
    {
        return count($this->_helpersByPriority);
    }

    /**
     * Find the next free higher priority.  If an index is given, it will
     * find the next free highest priority after it.
     *
     * @param int $indexPriority OPTIONAL
     * @return int
     */
    public function getNextFreeHigherPriority($indexPriority = null)
    {
        if ($indexPriority == null) {
            $indexPriority = $this->_nextDefaultPriority;
        }

        $priorities = array_keys($this->_helpersByPriority);

        while (in_array($indexPriority, $priorities)) {
            $indexPriority++;
        }

        return $indexPriority;
    }

    /**
     * Find the next free lower priority.  If an index is given, it will
     * find the next free lower priority before it.
     *
     * @param int $indexPriority
     * @return int
     */
    public function getNextFreeLowerPriority($indexPriority = null)
    {
        if ($indexPriority == null) {
            $indexPriority = $this->_nextDefaultPriority;
        }

        $priorities = array_keys($this->_helpersByPriority);

        while (in_array($indexPriority, $priorities)) {
            $indexPriority--;
        }

        return $indexPriority;
    }

    /**
     * return the highest priority
     *
     * @return int
     */
    public function getHighestPriority()
    {
        return max(array_keys($this->_helpersByPriority));
    }

    /**
     * return the lowest priority
     *
     * @return int
     */
    public function getLowestPriority()
    {
        return min(array_keys($this->_helpersByPriority));
    }

    /**
     * return the helpers referenced by name
     *
     * @return array
     */
    public function getHelpersByName()
    {
        return $this->_helpersByNameRef;
    }

    /**
     * Normalize a helper name
     *
     * Normalize to lowercase, with underscore separated words
     * 
     * @param  string $name
     * @return string
     */
    protected function normalizeHelperName($name)
    {
        if (!is_string($name)) {
            return $name;
        }
        $string = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $name);
        $string = strtolower($string);
        return $string;
    }
}
