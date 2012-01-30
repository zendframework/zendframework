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
 * @subpackage AgileZen
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace Zend\Service\AgileZen;

/**
 * @category   Zend
 * @package    Zend\Service
 * @subpackage AgileZen
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Container implements \Countable, \Iterator, \ArrayAccess
{
    /**
     * @var array of Zend\Service\Resources\*
     */
    protected $objects = array();
    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;
    /**
     * @var AgileZen
     */
    protected $service;
    /**
     * Namespace prefix for Resources
     * 
     * @var string 
     */
    protected $namespacePrefix = 'Zend\Service\AgileZen\Resources';
    /**
     * __construct()
     *
     * @param  array $list
     * @return boolean
     */
    public function __construct($service, $list, $resource)
    {
        if (!($service instanceof AgileZen)) {
            throw new Exception\InvalidArgumentException("You must pass an AgileZen object");
        }
        if (empty($list)) {
            throw new Exception\InvalidArgumentException("You must pass an array of data objects");
        }
        if (empty($resource)) {
            throw new Exception\InvalidArgumentException("You must pass a valid resource name");
        }
        if (!class_exists($this->namespacePrefix . '\\'. ucfirst($resource))) {
            throw new Exception\InvalidArgumentException("The resource provided doesn't exist");
        }
        $this->service = $service;
        $this->resource= $this->namespacePrefix . '\\'. ucfirst($resource);
        $this->constructFromArray($list);
    }
    /**
     * Transforms the Array to array of container
     *
     * @param  array $list
     * @return void
     */
    private function constructFromArray(array $list)
    {
        foreach ($list as $obj) {
            $this->addObject(new $this->resource($this->service,$obj));
        }
    }
    /**
     * Add an object
     *
     * @param  $obj
     * @return Container
     */
    protected function addObject ($obj)
    {
        $this->objects[] = $obj;
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
        return count($this->objects);
    }
    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return Zend\Service\Resources\*
     */
    public function current()
    {
        return $this->objects[$this->iteratorKey];
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
        return $this->iteratorKey;
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
        $this->iteratorKey += 1;
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
        $this->iteratorKey = 0;
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
        if ($numItems > 0 && $this->iteratorKey < $numItems) {
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
     * @return  Zend\Service\AgileZend\Resources\*
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->objects[$offset];
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
     * @throws  Zend\Service\AgileZend\Resources\*
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
     * @throws  Zend\Service\AgileZend\Resources\*
     */
    public function offsetUnset($offset)
    {
        throw new Exception('You are trying to unset read-only property');
    }
}
