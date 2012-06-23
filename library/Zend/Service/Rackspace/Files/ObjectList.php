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
 * @package    Zend\Service\Rackspace\
 * @subpackage Files
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Rackspace\Files;

use Zend\Service\Rackspace\Files\Object,
        Zend\Service\Rackspace\Files as RackspaceFiles;

/**
 * List of servers retrived from the GoGrid web service
 *
 * @category   Zend
 * @package    Zend\Service\Rackspace
 * @subpackage Files
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ObjectList implements \Countable, \Iterator, \ArrayAccess
{
    /**
     * @var array of Zend\Service\GoGrid\Object
     */
    protected $objects = array();
    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;
    /**
     * @var RackspaceFiles
     */
    protected $service;
    /**
     * The container name of the object list
     * 
     * @var string
     */
    protected $container;
    /**
     * __construct()
     *
     * @param  array $list
     * @return boolean
     */
    public function __construct(RackspaceFiles $service,$list,$container)
    {
        if (!($service instanceof RackspaceFiles)) {
            throw new Exception\InvalidArgumentException("You must pass a RackspaceFiles object");
        }
        if (empty($list)) {
            throw new Exception\InvalidArgumentException("You must pass an array of data objects");
        }
        if (empty($container)) {
            throw new Exception\InvalidArgumentException("You must pass the container of the object list");
        }
        $this->service= $service;
        $this->container= $container;
        $this->_constructFromArray($list);
    }
    /**
     * Transforms the Array to array of container
     *
     * @param  array $list
     * @return void
     */
    private function _constructFromArray(array $list)
    {
        foreach ($list as $obj) {
            $obj['container']= $this->container;
            $this->_addObject(new Object($this->service,$obj));
        }
    }
    /**
     * Add an object
     *
     * @param  Zend\Service\Rackspace\Files\Object $obj
     * @return Zend\Service\Rackspace\Files\ObjectList
     */
    protected function _addObject (Object $obj)
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
     * @return Zend\Service\Rackspace\Files\Object
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
     * @return  Zend\Service\Rackspace\Files\Object
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
     * @throws  Zend\Service\Rackspace\Exception
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
     * @throws  Zend\Service\Rackspace\Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception('You are trying to unset read-only property');
    }
}
