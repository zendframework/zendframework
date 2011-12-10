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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Config;

use \Countable,
    \Iterator,
    \ArrayAccess,
    Zend\Config\Exception\InvalidArgumentException,
    Zend\Config\Parser,
    Zend\Config\Parser\Queue as ParserQueue;

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Config implements Countable, Iterator, ArrayAccess
{
    /**
     * Whether modifications to configuration data are allowed.
     *
     * @var boolean
     */
    protected $allowModifications;

    /**
     * Number of elements in configuration data.
     *
     * @var integer
     */
    protected $count;

    /**
     * Data withing the configuration.
     *
     * @var array
     */
    protected $data = array();

    /**
     * Data withing the configuration.
     *
     * @var \Zend\Config\Parser\Queue
     */
    protected $parsers;

    /**
     * Used when unsetting values during iteration to ensure we do not skip
     * the next element.
     *
     * @var boolean
     */
    protected $skipNextIteration;

    /**
     * Internal error messages.
     *
     * @var null|array
     */
    protected $_errorMessages = array();

    /**
     * Zend_Config provides a property based interface to
     * an array. The data are read-only unless $allowModifications
     * is set to true on construction.
     *
     * Zend_Config also implements Countable, Iterator and ArrayAccess to
     * facilitate easy access to the data.
     *
     * @param  array   $array
     * @param  boolean $allowModifications
     * @param \Zend\Config\Parser\Queue|\Zend\Config\Parser|Traversable|array $parsers
     * @return \Zend\Config\Config
     */
    public function __construct(array $array, $allowModifications = false, $parsers = null)
    {
        $this->allowModifications = (boolean) $allowModifications;

        if ($parsers !== null) {
            $parsers = $this->setParsers($parsers);
        } else {
            // create empty queue
            $parsers = $this->getParsers();
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->data[$key] = new self($value, $this->allowModifications, $parsers);
            } else {
                $this->data[$key] = $value;
            }

            $this->count++;
        }

        /**
         * Process config
         */
        if(!$parsers->isEmpty()){
            $this->parse();
        }
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        
        return $default;
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Set a value in the config.
     * 
     * Only allow setting of a property if $allowModifications  was set to true
     * on construction. Otherwise, throw an exception.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function __set($name, $value)
    {
        if ($this->allowModifications) {
            if (is_array($value)) {
                $this->data[$name] = new self($value, true);
            } else {
                $this->data[$name] = $value;
            }
            
            $this->count++;
        } else {
            throw new Exception\RuntimeException('Config is read only');
        }
    }

    /**
     * Deep clone of this instance to ensure that nested Zend\Configs are also
     * cloned.
     *
     * @return void
     */
    public function __clone()
    {
        $array = array();
      
        foreach ($this->data as $key => $value) {
            if ($value instanceof self) {
                $array[$key] = clone $value;
            } else {
                $array[$key] = $value;
            }
        }
      
        $this->data = $array;
    }

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        $data  = $this->data;
        
        foreach ($data as $key => $value) {
            if ($value instanceof self) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        
        return $array;
    }

    /**
     * Support isset() overloading on PHP 5.1.
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Support unset() overloading on PHP 5.1.
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        if (!$this->allowModifications) {
            throw new Exception\InvalidArgumentException('Config is read only');
        } elseif (isset($this->data[$name])) {
            unset($this->data[$name]);
            $this->count--;            
            $this->skipNextIteration = true;
        }
    }

    /**
     * count(): defined by Countable interface.
     * 
     * @see    Countable::count()
     * @return integer
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return mixed
     */
    public function current()
    {
        $this->skipNextIteration = false;
        return current($this->data);
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next()
    {
        if ($this->skipNextIteration) {
            $this->skipNextIteration = false;
            return;
        }
        
        next($this->data);
    }

    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind()
    {
        $this->skipNextIteration = false;
        reset($this->data);
    }

    /**
     * valid(): defined by Iterator interface.
     *
     * @see    Iterator::valid()
     * @return boolean
     */
    public function valid()
    {
        return ($this->key() !== null);
    }

    /**
     * offsetExists(): defined by ArrayAccess interface.
     * 
     * @see    ArrayAccess::offsetExists()
     * @param  mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }
    
    /**
     * offsetGet(): defined by ArrayAccess interface.
     * 
     * @see    ArrayAccess::offsetGet()
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }
    
    /**
     * offsetSet(): defined by ArrayAccess interface.
     * 
     * @see    ArrayAccess::offsetSet()
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }
    
    /**
     * offsetUnset(): defined by ArrayAccess interface.
     * 
     * @see    ArrayAccess::offsetUnset()
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }
    
    /**
     * Merge another Config with this one.
     * 
     * The items in $merge will override the same named items in the current
     * config.
     *
     * @param  self $merge
     * @return self
     */
    public function merge(self $merge)
    {
        foreach ($merge as $key => $item) {
            if (array_key_exists($key, $this->data)) {
                if ($item instanceof self && $this->data[$key] instanceof self) {
                    $this->data[$key] = $this->data[$key]->merge(new self($item->toArray(), $this->allowModifications));
                } else {
                    $this->data[$key] = $item;
                }
            } else {
                if ($item instanceof self) {
                    $this->data[$key] = new self($item->toArray(), $this->allowModifications);
                } else {
                    $this->data[$key] = $item;
                }
            }
        }

        return $this;
    }

    /**
     * Prevent any more modifications being made to this instance.
     * 
     * Useful after merge() has been used to merge multiple Config objects
     * into one object which should then not be modified again.
     *
     * @return void
     */
    public function setReadOnly()
    {
        $this->allowModifications = false;
        
        foreach ($this->data as $key => $value) {
            if ($value instanceof self) {
                $value->setReadOnly();
            }
        }
    }

    /**
     * Returns whether this Config object is read only or not.
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        return !$this->allowModifications;
    }

    /**
     * Get parsers queue for this config.
     *
     * @return \Zend\Config\Parser\Queue
     */
    public function getParsers(){
        if ($this->parsers === null) {
            $this->parsers = new ParserQueue();
        }
        return $this->parsers;
    }

    /**
     * Set config parsers
     *
     * @param \Zend\Config\Parser\Queue|\Zend\Config\Parser|Traversable|array $parsers
     * @return \Zend\Config\Parser\Queue
     * @throws Exception\InvalidArgumentException
     */
    public function setParsers($parsers)
    {
        // A complete, ready to use queue object
        if ($parsers instanceof ParserQueue) {
            return $this->parsers = $parsers;
        }

        // A single parser
        elseif ($parsers instanceof Parser) {
            $this->parsers = new ParserQueue();
            $this->parsers->insert($parsers);
            return $this->parsers;
        }

        // An array of parsers
        elseif (
            !is_array($parsers) &&
            !($parsers instanceof \Traversable) &&
            !($parsers instanceof ParserQueue)
        ) {
            throw new InvalidArgumentException('Cannot use ' . gettype($parsers) . ' as a parsers.');
        }

        $this->parsers = new ParserQueue();
        foreach ($parsers as $parser) {
            if ($parser instanceof Parser) {
                $this->parsers->insert($parser);
            } else {
                throw new InvalidArgumentException('Cannot use ' . gettype($parser) . ' as a parser');
            }
        }

        return $this->parsers;
    }

    /**
     * Process the whole config structure with each parser in the queue.
     *
     * @return void
     */
    public function parse()
    {
        $this->getParsers()->parse($this);
    }
}
