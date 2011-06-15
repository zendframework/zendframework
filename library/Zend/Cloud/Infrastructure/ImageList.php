<?php
/**
 * List of images
 *
 * @uses       ArrayAccess
 * @uses       Countable
 * @uses       Iterator
 * @uses       OutOfBoundsException
 * @uses       Zend\Cloud\Infrastructure\Image
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

use Zend\Cloud\Infrastructure\Image,  
    Zend\Cloud\Infrastructure\Exception;

class ImageList implements \Countable, \Iterator, \ArrayAccess
{
    /**
     * @var array Array of Zend\Cloud\Infrastructure\Image
     */
    protected $imagess = array();
    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;
    /**
     * The Image adapter (if exists)
     * 
     * @var object
     */
    protected $adapter;
    /**
     * __construct()
     *
     * @param  array $list
     * @return boolean
     */
    public function __construct($images,$adapter=null)
    {
        if (empty($images) || !is_array($images)) {
            throw new Exception\InvalidArgumentException("You must pass an array of Image's params");
        }
        $this->adapter= $adapter;
        $this->constructFromArray($images);
    }
    /**
     * Transforms the Array to array of Instances
     *
     * @param  array $list
     * @return void
     */
    private function constructFromArray(array $list)
    {
        foreach ($list as $image) {
            $this->addImage(new Image($image,$this->adapter));
        }
    }
    /**
     * Add an image
     *
     * @param  Zend\Service\Cloud\Infrastructure\Image
     * @return Zend\Service\Cloud\Infrastructure\ImageList
     */
    protected function addImage (Image $image)
    {
        $this->images[] = $image;
        return $this;
    }
    /**
     * Return number of images
     *
     * Implement Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->images);
    }
    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return Zend\Cloud\Infrastructure\Image
     */
    public function current()
    {
        return $this->images[$this->iteratorKey];
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
     * @return  Zend\Cloud\Infrastructure\Image
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->images[$offset];
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
