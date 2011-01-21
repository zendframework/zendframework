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
 * @package    Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\SignalSlot;

/**
 * Representation of a signal
 *
 * Encapsulates the target context and parameters passed, and provides some 
 * behavior for interacting with the signal manager.
 *
 * @uses       Zend\SignalSlot\SignalSlot
 * @category   Zend
 * @package    Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Signal
{
    /**
     * @var string Signal name
     */
    protected $name;

    /**
     * @var string|object The signal target
     */
    protected $target;

    /**
     * @var array|ArrayAccess The signal parameters
     */
    protected $params = array();

    /**
     * @var bool Whether or not to stop propagation
     */
    protected $stopPropagation = false;

    /**
     * Constructor
     *
     * Accept a target and its parameters.
     * 
     * @param  string $name Signal name
     * @param  string|object $target 
     * @param  array|ArrayAccess $params 
     * @return void
     */
    public function __construct($name, $target, $params)
    {
        $this->name   = $name;
        $this->target = $target;
        $this->setParams($params);
    }

    /**
     * Get signal name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the signal target
     *
     * This may be either an object, or the name of a static method.
     * 
     * @return string|object
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set parameters
     *
     * Overwrites parameters
     * 
     * @param  array|ArrayAccess $params 
     * @return Signal
     */
    public function setParams($params)
    {
        if (!is_array($params) && !$params instanceof \ArrayAccess) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Signal parameters must be an array or implement ArrayAccess; received "%s"',
                (is_object($params) ? get_class($params) : gettype($params))
            ));
        }

        $this->params = $params;
        return $this;
    }

    /**
     * Get all parameters
     * 
     * @return array|ArrayAccess
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get an individual parameter
     *
     * If the parameter does not exist, the $default value will be returned.
     * 
     * @param  string|int $name 
     * @param  mixed $default 
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        if (!isset($this->params[$name])) {
            return $default;
        }

        return $this->params[$name];
    }

    /**
     * Set an individual parameter to a value
     * 
     * @param  string|int $name 
     * @param  mixed $value 
     * @return Signal
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Stop further signal propagation
     * 
     * @param  bool $flag 
     * @return void
     */
    public function stopPropagation($flag)
    {
        $this->stopPropagation = (bool) $flag;
    }

    /**
     * Is propagation stopped?
     * 
     * @return bool
     */
    public function propagationIsStopped()
    {
        return $this->stopPropagation;
    }
}
