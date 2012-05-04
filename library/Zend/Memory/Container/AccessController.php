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
 * @package    Zend_Memory
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Memory\Container;

/**
 * Memory object container access controller.
 *
 * Memory manager stores a list of generated objects to control them.
 * So container objects always have at least one reference and can't be automatically destroyed.
 *
 * This class is intended to be an userland proxy to memory container object.
 * It's not referenced by memory manager and class destructor is invoked immidiately after gouing
 * out of scope or unset operation.
 *
 * Class also provides Zend\Memory\Container interface and works as proxy for such cases.
 *
 * @category   Zend
 * @package    Zend_Memory
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AccessController implements ContainerInterface
{
    /**
     * Memory container object
     *
     * @var Movable
     */
    private $_memContainer;


    /**
     * Object constructor
     *
     * @param Movable $memContainer
     */
    public function __construct(Movable $memContainer)
    {
        $this->_memContainer = $memContainer;
    }

    /**
     * Object destructor
     */
    public function __destruct()
    {
        $this->_memContainer->destroy();
    }


    /**
     * Get string value reference
     *
     * _Must_ be used for value access before PHP v 5.2
     * or _may_ be used for performance considerations
     *
     * @return &string
     */
    public function &getRef()
    {
        return $this->_memContainer->getRef();
    }

    /**
     * Signal, that value is updated by external code.
     *
     * Should be used together with getRef()
     */
    public function touch()
    {
        $this->_memContainer->touch();
    }

    /**
     * Lock object in memory.
     */
    public function lock()
    {
        $this->_memContainer->lock();
    }


    /**
     * Unlock object
     */
    public function unlock()
    {
        $this->_memContainer->unlock();
    }

    /**
     * Return true if object is locked
     *
     * @return boolean
     */
    public function isLocked()
    {
        return $this->_memContainer->isLocked();
    }

    /**
     * Get handler
     *
     * Loads object if necessary and moves it to the top of loaded objects list.
     * Swaps objects from the bottom of loaded objects list, if necessary.
     *
     * @param string $property
     * @return string
     */
    public function __get($property)
    {
        return $this->_memContainer->$property;
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  string $value
     */
    public function __set($property, $value)
    {
        $this->_memContainer->$property = $value;
    }
}
