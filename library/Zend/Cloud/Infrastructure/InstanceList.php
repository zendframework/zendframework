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
 * @package    Zend\Cloud
 * @subpackage Infrastructure
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Cloud\Infrastructure;

use Zend\Cloud\Infrastructure\Instance,
    Zend\Cloud\Infrastructure\Adapter,    
    Zend\Cloud\Infrastructure\Exception;

/**
 * List of instances
 *
 * @uses       ArrayAccess
 * @uses       Countable
 * @uses       Iterator
 * @uses       OutOfBoundsException
 * @uses       Zend\Cloud\Infrastructure\Instance
 * @category   Zend
 * @package    Zend\Cloud
 * @subpackage Infrastructure
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class InstanceList implements \Countable, \Iterator, \ArrayAccess
{
    /**
     * @var array Array of Zend\Cloud\Infrastructure\Instance
     */
    protected $instances = array();
    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;
    /**
     * @var Zend\Cloud\Infrastructure\Adapter
     */
    protected $adapter;
    /**
     * __construct()
     *
     * @param  array $list
     * @return boolean
     */
    public function __construct(Adapter $adapter,$instances)
    {
        if (!($adapter instanceof Adapter)) {
            throw new Exception\InvalidArgumentException("You must pass a Zend\Cloud\Infrastructure\Adapter");
        }
        if (empty($instances)) {
            throw new Exception\InvalidArgumentException("You must pass an array of Instance's params");
        }
        $this->adapter= $adapter;
        $this->constructFromArray($instances);
    }
    /**
     * Transforms the Array to array of Instances
     *
     * @param  array $list
     * @return void
     */
    private function constructFromArray(array $list)
    {
        foreach ($list as $instance) {
            $this->addInstance(new Instance($this->adapter,$instance));
        }
    }
    /**
     * Add an instance
     *
     * @param  Zend\Service\Cloud\Infrastructure\Instance
     * @return Zend\Service\Cloud\Infrastructure\InstanceList
     */
    protected function addInstance (Instance $instance)
    {
        $this->instances[] = $instance;
        return $this;
    }
    /**
     * Return number of instances
     *
     * Implement Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->instances);
    }
    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return Zend\Cloud\Infrastructure\Instance
     */
    public function current()
    {
        return $this->instances[$this->iteratorKey];
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
            return $this->instances[$offset];
        } else {
            throw new OutOfBoundsException('Illegal index');
        }
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetSet()
     *
     * @param   int     $offset
     * @param   string  $value
     * @throws  Zend\Cloud\Infrastructure\Exception
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
     * @throws  Zend\Cloud\Infrastructure\Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception('You are trying to unset read-only property');
    }
}
