<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Server
 */

namespace Zend\Server\Method;

use Zend\Server;

/**
 * Method callback metadata
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage Zend_Server_Method
 */
class Callback
{
    /**
     * @var string Class name for class method callback
     */
    protected $_class;

    /**
     * @var string Function name for function callback
     */
    protected $_function;

    /**
     * @var string Method name for class method callback
     */
    protected $_method;

    /**
     * @var string Callback type
     */
    protected $_type;

    /**
     * @var array Valid callback types
     */
    protected $_types = array('function', 'static', 'instance');

    /**
     * Constructor
     *
     * @param  null|array $options
     * @return void
     */
    public function __construct($options = null)
    {
        if ((null !== $options) && is_array($options))  {
            $this->setOptions($options);
        }
    }

    /**
     * Set object state from array of options
     *
     * @param  array $options
     * @return \Zend\Server\Method\Callback
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Set callback class
     *
     * @param  string $class
     * @return \Zend\Server\Method\Callback
     */
    public function setClass($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $this->_class = $class;
        return $this;
    }

    /**
     * Get callback class
     *
     * @return string|null
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Set callback function
     *
     * @param  string $function
     * @return \Zend\Server\Method\Callback
     */
    public function setFunction($function)
    {
        $this->_function = (string) $function;
        $this->setType('function');
        return $this;
    }

    /**
     * Get callback function
     *
     * @return null|string
     */
    public function getFunction()
    {
        return $this->_function;
    }

    /**
     * Set callback class method
     *
     * @param  string $method
     * @return \Zend\Server\Method\Callback
     */
    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * Get callback class  method
     *
     * @return null|string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Set callback type
     *
     * @param  string $type
     * @return \Zend\Server\Method\Callback
     * @throws \Zend\Server\Exception
     */
    public function setType($type)
    {
        if (!in_array($type, $this->_types)) {
            throw new Server\Exception\InvalidArgumentException('Invalid method callback type "' . $type . '" passed to ' . __CLASS__ . '::' . __METHOD__);
        }
        $this->_type = $type;
        return $this;
    }

    /**
     * Get callback type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Cast callback to array
     *
     * @return array
     */
    public function toArray()
    {
        $type = $this->getType();
        $array = array(
            'type' => $type,
        );
        if ('function' == $type) {
            $array['function'] = $this->getFunction();
        } else {
            $array['class']  = $this->getClass();
            $array['method'] = $this->getMethod();
        }
        return $array;
    }
}
