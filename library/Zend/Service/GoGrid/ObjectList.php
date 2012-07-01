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
 * @package    Zend\Service
 * @subpackage GoGrid
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\GoGrid;

use ArrayAccess;
use Countable;
use Iterator;
use Zend\Service\GoGrid\Object;

/**
 * List of servers retrived from the GoGrid web service
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage GoGrid
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ObjectList implements
    Countable,
    Iterator,
    ArrayAccess
{
    const SUCCESS_STATUS= 'success';

    /**
     * @var array Array of Zend\Service\GoGrid\Object
     */
    protected $_objects = array();

    /**
     * @var int Iterator key
     */
    protected $_iteratorKey = 0;

    /**
     * @var array
     */
    protected $_summary;

    /**
     * @var string
     */
    protected $_status;

    /**
     * @var string
     */
    protected $_method;

    /**
     * @var boolean
     */
    protected $_error= true;

    /**
     * @var string
     */
    protected $_errorMsg= '';

    /**
     * Constructor
     * 
     * @param array $list
     */
    public function __construct(array $list = array())
    {
        if (empty($list)) {
            return;
        }
        if (array_key_exists('status', $list)) {
            $this->_status= $list['status'];
            if ($this->_status!=self::SUCCESS_STATUS) {
                $this->_errorMsg= $list['list'][0]['message'];
            } else {
                $this->_error= false;
            }
        }
        if (array_key_exists('summary', $list)) {
            $this->_summary= $list['summary'];
        }
        if (array_key_exists('method', $list)) {
            $this->_method= $list['method'];
        }
        if (!$this->_error) {
            $this->_constructFromArray($list['list']);
        }    
    }

    /**
     * Transforms the Array to array of posts
     *
     * @param  array $list
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
     * @param  Object $obj
     * @return ObjectList
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
     * @throws  Exception\OutOfBoundsException
     * @return  Zend\Service\GoGrid\Object
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->_objects[$offset];
        } else {
            throw new Exception\OutOfBoundsException('Illegal index');
        }
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetSet()
     *
     * @param   int     $offset
     * @param   string  $value
     * @throws  Exception\RuntimeException
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception\RuntimeException('You are trying to set read-only property');
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetUnset()
     *
     * @param   int     $offset
     * @throws  Exception\RuntimeException
     */
    public function offsetUnset($offset)
    {
        throw new Exception\RuntimeException('You are trying to unset read-only property');
    }

    /**
     * Check if the service call was successful
     * 
     * @return boolen
     */
    public function isSuccess() {
        return ($this->_error===false);
    }

    /**
     * Get the error masg
     * 
     * @return string 
     */
    public function getError() {
        return $this->_errorMsg;
    }

    /**
     * getSummary
     *
     * @param string $key
     * @return string|array
     */
    public function getSummary($key=null) {
        if (!empty($key)) {
            if (array_key_exists($key, $this->_summary)) {
                return $this->_summary[$key];
            } else {
                return false;
            }
        }
        return $this->_summary;
    }

    /**
     * getMethod
     *
     * @return string
     */
    public function getMethod() {
        return $this->_method;
    }

    /**
     * getStatus
     *
     * @return string
     */
    public function getStatus() {
        return $this->_status;
    }
}
