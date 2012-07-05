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

namespace Zend\Stdlib;

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractOptions implements ParameterObjectInterface
{
    /**
     * @param  array|Traversable|null $options
     * @return AbstractOptions
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setFromArray($options);
        }
    }

    /**
     * @param  array|Traversable $options
     * @return void
     */
    public function setFromArray($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter provided to %s must be an array or Traversable',
                __METHOD__
            ));
        }

        foreach ($options as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * @param string $key name of option with underscore
     * @return string name of setter method
     * @throws Exception\BadMethodCallException if setter method is undefined
     */
    protected function assembleSetterNameFromKey($key)
    {
        $parts = explode('_', $key);
        $parts = array_map('ucfirst', $parts);
        $setter = 'set' . implode('', $parts);
        if (!method_exists($this, $setter)) {
            throw new Exception\BadMethodCallException(
                'The option "' . $key . '" does not '
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
    protected function assembleGetterNameFromKey($key)
    {
        $parts = explode('_', $key);
        $parts = array_map('ucfirst', $parts);
        $getter = 'get' . implode('', $parts);
        if (!method_exists($this, $getter)) {
            throw new Exception\BadMethodCallException(
                'The option "' . $key . '" does not '
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
        $setter = $this->assembleSetterNameFromKey($key);
        $this->{$setter}($value);
    }

    /**
     * @see ParameterObject::__get()
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $getter = $this->assembleGetterNameFromKey($key);
        return $this->{$getter}();
    }

    /**
     * @see ParameterObject::__isset()
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return null !== $this->__get($key);
    }

    /**
     * @see ParameterObject::__unset()
     * @param string $key
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function __unset($key)
    {
        try {
            $this->__set($key, null);
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
