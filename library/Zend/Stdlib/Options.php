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
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Stdlib;

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Options implements ParameterObject
{
    /**
     * @param  array|Traversable|null $options
     * @return Options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        if (is_null($options)) {
            return;
        }
        $this->processArray($options);
    }

    /**
     * @param  array|Traversable $config
     * @return void
     */
    protected function processArray($config)
    {
        if (!is_array($config) && !$config instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter provided to %s must be an array or Traversable',
                __METHOD__
            ));
        }

        foreach ($config as $key => $value) {
            $setter = $this->assembleSetterNameFromConfigKey($key);
            $this->{$setter}($value);
        }
    }

    /**
     * @param string $key name of option with underscore
     * @return string name of setter method
     * @throws Exception\BadMethodCallException if setter method is undefined
     */
    protected function assembleSetterNameFromConfigKey($key)
    {
        $parts = explode('_', $key);
        $parts = array_map('ucfirst', $parts);
        $setter = 'set' . implode('', $parts);
        if (!method_exists($this, $setter)) {
            throw new Exception\BadMethodCallException(
                'The configuration key "' . $key . '" does not '
                . 'have a matching ' . $setter . ' setter method '
                . 'which must be defined'
            );
        }
        return $setter;
    }

    /**
     * @param string $key name of option with underscore
     * @return string name of getter method
     * @throws Exception\BadMethodCallException if getter method is undefined
     */
    protected function assembleGetterNameFromConfigKey($key)
    {
        $parts = explode('_', $key);
        $parts = array_map('ucfirst', $parts);
        $getter = 'get' . implode('', $parts);
        if (!method_exists($this, $getter)) {
            throw new Exception\BadMethodCallException(
                'The configuration key "' . $key . '" does not '
                . 'have a matching ' . $getter . ' getter method '
                . 'which must be defined'
            );
        }
        return $getter;
    }

    /**
     * @see ParameterObject::__set()
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $setter = $this->assembleSetterNameFromConfigKey($key);
        $this->{$setter}($value);
    }

    /**
     * @see ParameterObject::__get()
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $getter = $this->assembleGetterNameFromConfigKey($key);
        return $this->{$getter}();
    }

    /**
     * @see ParameterObject::__isset()
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        $getter = $this->assembleGetterNameFromConfigKey($key);
        return !is_null($this->{$getter}());
    }

    /**
     * @see ParameterObject::__unset()
     * @param string $key
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function __unset($key)
    {
        $setter = $this->assembleSetterNameFromConfigKey($key);
        try {
            $this->{$setter}(null);
        } catch(\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException(
                'The class property $' . $key . ' cannot be unset as'
                    . ' NULL is an invalid value for it',
                0,
                $e
            );
        }
    }
}
