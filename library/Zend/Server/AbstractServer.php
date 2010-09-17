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
 * @package    Zend_Server
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Server;

/**
 * Abstract Server implementation
 *
 * @uses       ReflectionClass
 * @uses       \Zend\Server\Definition
 * @uses       \Zend\Server\Exception
 * @uses       \Zend\Server\Server
 * @uses       \Zend\Server\Method\Callback
 * @uses       \Zend\Server\Method\Definition
 * @uses       \Zend\Server\Method\Parameter
 * @uses       \Zend\Server\Method\Prototype
 * @category   Zend
 * @package    Zend_Server
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractServer implements Server
{
    /**
     * @deprecated
     * @var array List of PHP magic methods (lowercased)
     */
    protected static $magic_methods = array(
        '__call',
        '__clone',
        '__construct',
        '__destruct',
        '__get',
        '__isset',
        '__set',
        '__set_state',
        '__sleep',
        '__tostring',
        '__unset',
        '__wakeup',
    );

    /**
     * @var bool Flag; whether or not overwriting existing methods is allowed
     */
    protected $_overwriteExistingMethods = false;

    /**
     * @var \Zend\Server\Definition
     */
    protected $_table;

    /**
     * Constructor
     *
     * Setup server description
     *
     * @return void
     */
    public function __construct()
    {
        $this->_table = new Definition();
        $this->_table->setOverwriteExistingMethods($this->_overwriteExistingMethods);
    }

    /**
     * Returns a list of registered methods
     *
     * Returns an array of method definitions.
     *
     * @return \Zend\Server\Definition
     */
    public function getFunctions()
    {
        return $this->_table;
    }

    /**
     * Lowercase a string
     *
     * Lowercase's a string by reference
     *
     * @deprecated
     * @param  string $string value
     * @param  string $key
     * @return string Lower cased string
     */
    public static function lowerCase(&$value, &$key)
    {
        trigger_error(__CLASS__ . '::' . __METHOD__ . '() is deprecated and will be removed in a future version', E_USER_NOTICE);
        return $value = strtolower($value);
    }

    /**
     * Build callback for method signature
     *
     * @param  \Zend\Server\Reflection\AbstractFunction $reflection
     * @return \Zend\Server\Method\Callback
     */
    protected function _buildCallback(Reflection\AbstractFunction $reflection)
    {
        $callback = new Method\Callback();
        if ($reflection instanceof Reflection\ReflectionMethod) {
            $callback->setType($reflection->isStatic() ? 'static' : 'instance')
                     ->setClass($reflection->getDeclaringClass()->getName())
                     ->setMethod($reflection->getName());
        } elseif ($reflection instanceof Reflection\ReflectionFunction) {
            $callback->setType('function')
                     ->setFunction($reflection->getName());
        }
        return $callback;
    }

    /**
     * Build a method signature
     *
     * @param  \Zend\Server\Reflection\AbstractFunction $reflection
     * @param  null|string|object $class
     * @return \Zend\Server\Method\Definition
     * @throws \Zend\Server\Exception on duplicate entry
     */
    protected function _buildSignature(Reflection\AbstractFunction $reflection, $class = null)
    {
        $ns         = $reflection->getNamespace();
        $name       = $reflection->getName();
        $method     = empty($ns) ? $name : $ns . '.' . $name;

        if (!$this->_overwriteExistingMethods && $this->_table->hasMethod($method)) {
            throw new Exception('Duplicate method registered: ' . $method);
        }

        $definition = new Method\Definition();
        $definition->setName($method)
                   ->setCallback($this->_buildCallback($reflection))
                   ->setMethodHelp($reflection->getDescription())
                   ->setInvokeArguments($reflection->getInvokeArguments());

        foreach ($reflection->getPrototypes() as $proto) {
            $prototype = new Method\Prototype();
            $prototype->setReturnType($this->_fixType($proto->getReturnType()));
            foreach ($proto->getParameters() as $parameter) {
                $param = new Method\Parameter(array(
                    'type'     => $this->_fixType($parameter->getType()),
                    'name'     => $parameter->getName(),
                    'optional' => $parameter->isOptional(),
                ));
                if ($parameter->isDefaultValueAvailable()) {
                    $param->setDefaultValue($parameter->getDefaultValue());
                }
                $prototype->addParameter($param);
            }
            $definition->addPrototype($prototype);
        }
        if (is_object($class)) {
            $definition->setObject($class);
        }
        $this->_table->addMethod($definition);
        return $definition;
    }

    /**
     * Dispatch method
     *
     * @param  \Zend\Server\Method\Definition $invocable
     * @param  array $params
     * @return mixed
     */
    protected function _dispatch(Method\Definition $invocable, array $params)
    {
        $callback = $invocable->getCallback();
        $type     = $callback->getType();

        if ('function' == $type) {
            $function = $callback->getFunction();
            return call_user_func_array($function, $params);
        }

        $class  = $callback->getClass();
        $method = $callback->getMethod();

        if ('static' == $type) {
            return call_user_func_array(array($class, $method), $params);
        }

        $object = $invocable->getObject();
        if (!is_object($object)) {
            $invokeArgs = $invocable->getInvokeArguments();
            if (!empty($invokeArgs)) {
                $reflection = new \ReflectionClass($class);
                $object     = $reflection->newInstanceArgs($invokeArgs);
            } else {
                $object = new $class;
            }
        }
        return call_user_func_array(array($object, $method), $params);
    }

    /**
     * Map PHP type to protocol type
     *
     * @param  string $type
     * @return string
     */
    abstract protected function _fixType($type);
}
