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
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * PHP Array (OO wrapper)
 * Used to be returned by reference by __get() methods
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @todo       also implement Countable for PHP 5.1 but not yet to stay 5.0 compatible
 */
class Zend_Pdf_PhpArray implements ArrayAccess, Iterator, Countable {
    /**
     * Array element
     * @var mixed
     */
    protected $_items = array();


    /**
     * Object constructor
     *
     * @param array $srcArray
     */
    public function __construct($srcArray = null)
    {
        if ($srcArray === null) {
            reset($this->_items);
        } else if (is_array($srcArray)) {
            $this->_items = $srcArray;
        } else {
            throw new Exception('Array can be initialized only by other array');
        }
    }


    public function current()
    {
        return current($this->_items);
    }


    public function next()
    {
        return next($this->_items);
    }


    public function key()
    {
        return key($this->_items);
    }


    public function valid() {
        return current($this->_items)!==false;
    }


    public function rewind()
    {
        reset($this->_items);
    }


    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_items);
    }


    public function offsetGet($offset)
    {
        return $this->_items[$offset];
    }


    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->_items[]        = $value;
        } else {
            $this->_items[$offset] = $value;
        }
    }


    public function offsetUnset($offset)
    {
        unset($this->_items[$offset]);
    }


    public function clear()
    {
        $this->_items = array();
    }
    
    /**
     * Defined by Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->_items);
    }
    
}

