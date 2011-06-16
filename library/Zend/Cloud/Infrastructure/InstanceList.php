<?php
/**
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

use Countable,
    Iterator,
    ArrayAccess;

/**
 * List of instances
 *
 * @package    Zend\Cloud
 * @subpackage Infrastructure
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class InstanceList implements Countable, Iterator, ArrayAccess
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
     * Constructor
     *
     * @param  Adapter $adapter
     * @param  array $instances
     * @return void
     */
    public function __construct(Adapter $adapter, array $instances = null)
    {
        if (!($adapter instanceof Adapter)) {
            throw new Exception\InvalidArgumentException('You must pass a Zend\Cloud\Infrastructure\Adapter');
        }
        if (empty($instances)) {
            throw new Exception\InvalidArgumentException('You must pass an array of Instances');
        }

        $this->adapter = $adapter;
        $this->constructFromArray($instances);
    }

    /**
     * Transforms the Array to array of Instances
     *
     * @param  array $list
     * @return void
     */
    protected function constructFromArray(array $list)
    {
        foreach ($list as $instance) {
            $this->addInstance(new Instance($this->adapter,$instance));
        }
    }

    /**
     * Add an instance
     *
     * @param  Instance
     * @return InstanceList
     */
    protected function addInstance(Instance $instance)
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
     * @return Instance
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
        $this->iteratorKey++;
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
        }
        return false;
    }

    /**
     * Whether the offset exists
     *
     * Implement ArrayAccess::offsetExists()
     *
     * @param  int $offset
     * @return bool
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
     * @param  int $offset
     * @return Instance
     * @throws Exception\OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new Exception\OutOfBoundsException('Illegal index');
        }
        return $this->instances[$offset];
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetSet()
     *
     * @param   int     $offset
     * @param   string  $value
     * @throws  Exception\InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception\InvalidArgumentException('You are trying to set read-only property');
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetUnset()
     *
     * @param   int     $offset
     * @throws  Exception\InvalidArgumentException
     */
    public function offsetUnset($offset)
    {
        throw new Exception\InvalidArgumentException('You are trying to unset read-only property');
    }
}
