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
 * @subpackage AgileZen
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\AgileZen;

use ArrayAccess;
use Countable;
use Iterator;
use Traversable;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AgileZen
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Container implements Countable, Iterator, ArrayAccess
{
    /**
     * @var array of Zend\Service\AgileZen\Resources\*
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
     * Project id
     * 
     * @var integer 
     */
    protected $projectId;

    /**
     * Namespace prefix for Resources
     * 
     * @var string 
     */
    protected $namespacePrefix = 'Zend\Service\AgileZen\Resources';

    /**
     * Constructor
     * 
     * @param  AgileZen $service 
     * @param  array|Traversable $list 
     * @param  string $resource 
     * @param  null|int|string $projectId 
     * @return void
     */
    public function __construct(AgileZen $service, $list, $resource, $projectId = null)
    {
        if (empty($list) || (!is_array($list) && !$list instanceof Traversable)) {
            throw new Exception\InvalidArgumentException("You must pass an array of data objects");
        }
        if (empty($resource)) {
            throw new Exception\InvalidArgumentException("You must pass a valid resource name");
        }

        $resource = $this->namespacePrefix . '\\' . ucfirst($resource);
        if (!class_exists($resource)) {
            throw new Exception\InvalidArgumentException("The resource provided doesn't exist");
        }

        $this->service  = $service;
        $this->resource = $resource;

        if (!empty($projectId)) {
            $this->projectId = $projectId;
        }

        $this->constructFromArray($list);
    }

    /**
     * Transforms the Array to array of container
     *
     * @param  array|Traversable $list
     * @return void
     */
    private function constructFromArray($list)
    {
        foreach ($list as $obj) {
            if (!empty($this->projectId)) {
                $obj['projectId'] = $this->projectId;
            }
            $this->addObject(new $this->resource($this->service, $obj));
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
     * @return Zend\Service\AgileZen\Resources\*
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
        }
        return false;
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
     * @return  Zend\Service\AgileZen\Resources\*
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new Exception\OutOfBoundsException('Illegal index');
        }
        return $this->objects[$offset];
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
}
