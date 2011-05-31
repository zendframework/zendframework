<?php
namespace Zend\Di;

/**
 * A collection of methods to inject
 * 
 * @copyright Copyright (C) 2006-Present, Zend Technologies, Inc.
 * @license   New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class Methods implements InjectibleMethods
{
    protected $methods = array();

    /**
     * Insert an injectible method into the list
     * 
     * @param  InjectibleMethod $method 
     * @return void
     */
    public function insert(InjectibleMethod $method)
    {
        $this->methods[] = $method;
    }

    /**
     * Return the current method object
     * 
     * @return InjectibleMethod
     */
    public function current()
    {
        return current($this->methods);
    }

    /**
     * Return the current method's name
     * 
     * @return string
     */
    public function key()
    {
        $method = $this->current();
        return $method->getName();
    }

    /**
     * Iterator: Move to the next item in the list
     * 
     * @return void
     */
    public function next()
    {
        return next($this->methods);
    }

    /**
     * Iterator: Reset the pointer
     * 
     * @return void
     */
    public function rewind()
    {
        return reset($this->methods);
    }

    /**
     * Iterator: Is the current index valid?
     * 
     * @return bool
     */
    public function valid()
    {
        return (current($this->methods) !== false);
    }
}
