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
 * @package    Zend_Service
 * @subpackage GoGrid
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Service\GoGrid;

use Zend\Service\GoGrid\Object;

/**
 * List of servers retrived from the GoGrid web service
 *
 * @uses       ArrayAccess
 * @uses       Countable
 * @uses       Iterator
 * @uses       OutOfBoundsException
 * @uses       Zend_Service_GoGrid_Server
 * @category   Zend
 * @package    Zend_Service
 * @subpackage GoGrid
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ObjectList implements \Countable, \Iterator, \ArrayAccess
{
    /**
     * @var array Array of Zend\Service\GoGrid\Object
     */
    protected $_objects = array();
    /**
     * @var int Iterator key
     */
    protected $_iteratorKey = 0;
    /**
     * @param  array $list
     * @return void
     */
    public function __construct($list = null)
    {
        if (is_array($list)) {
            $this->_constructFromArray($list['list']);
        }
    }
    /**
     * Transforms the Array to array of posts
     *
     * @param  array $postList
     * @return void
     */
    private function _constructFromArray(array $list)
    {
        foreach ($list as $obj) {
            $this->_addObject(new Object($obj));
        }
    }

    /**
     * Add an object
     *
     * @param  Zend\Service\GoGrid\Object $obj
     * @return Zend\Service\GoGrid\JobList
     */
    protected function _addObject (Object $obj)
    {
        $this->_objects[] = $obj;
        return $this;
    }
    /**
     * Return number of servers
     *
     * Implement Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->_objects);
    }

    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return Zend\Service\GoGrid\Object
     */
    public function current()
    {
        return $this->_objects[$this->_iteratorKey];
    }

    /**
     * Return the key of the current element
     *
     * Implement Iterator::key()
     *
     * @return int
     */
    public function key()
    {
        return $this->_iteratorKey;
    }

    /**
     * Move forward to next element
     *
     * Implement Iterator::next()
     *
     * @return void
     */
    public function next()
    {
        $this->_iteratorKey += 1;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * Implement Iterator::rewind()
     *
     * @return void
     */
    public function rewind()
    {
        $this->_iteratorKey = 0;
    }

    /**
     * Check if there is a current element after calls to rewind() or next()
     *
     * Implement Iterator::valid()
     *
     * @return bool
     */
    public function valid()
    {
        $numItems = $this->count();

        if ($numItems > 0 && $this->_iteratorKey < $numItems) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Whether the offset exists
     *
     * Implement ArrayAccess::offsetExists()
     *
     * @param   int     $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return ($offset < $this->count());
    }

    /**
     * Return value at given offset
     *
     * Implement ArrayAccess::offsetGet()
     *
     * @param   int     $offset
     * @throws  OutOfBoundsException
     * @return  Zend\Service\GoGrid\Object
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->_objects[$offset];
        } else {
            throw new \OutOfBoundsException('Illegal index');
        }
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetSet()
     *
     * @param   int     $offset
     * @param   string  $value
     * @throws  Zend\Service\GoGrid\Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception('You are trying to set read-only property');
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetUnset()
     *
     * @param   int     $offset
     * @throws  Zend\Service\GoGrid\Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception('You are trying to unset read-only property');
    }
    /**
     * Check if the the object list was successful
     * 
     * @return boolen
     */
    public function isSuccessful() {
        return !empty($this->_objects);
    }
}
