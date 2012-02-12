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
 * @package    Zend_Cache
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Pattern;

use Zend\Cache\Exception;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ObjectCache extends CallbackCache
{
    /**
     * Set options
     *
     * @param  PatternOptions $options
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions(PatternOptions $options)
    {
        parent::setOptions($options);

        if (!$options->getObject()) {
            throw new Exception\InvalidArgumentException("Missing option 'object'");
        } elseif (!$options->getStorage()) {
            throw new Exception\InvalidArgumentException("Missing option 'storage'");
        }
    }

    /**
     * Call and cache a class method
     *
     * @param  string $method  Method name to call
     * @param  array  $args    Method arguments
     * @param  array  $options Cache options
     * @return mixed
     * @throws Exception
     */
    public function call($method, array $args = array(), array $options = array())
    {
        $classOptions = $this->getOptions();
        $object       = $classOptions->getObject();
        $method       = strtolower($method);

        // handle magic methods
        switch ($method) {
            case '__set':
                $property = array_shift($args);
                $value    = array_shift($args);

                $object->{$property} = $value;

                if (!$classOptions->getObjectCacheMagicProperties()
                    || property_exists($object, $property) 
                ) {
                    // no caching if property isn't magic
                    // or caching magic properties is disabled
                    return;
                }

                // remove cached __get and __isset
                $removeKeys = null;
                if (method_exists($object, '__get')) {
                    $removeKeys[] = $this->generateKey('__get', array($property), $options);
                }
                if (method_exists($object, '__isset')) {
                    $removeKeys[] = $this->generateKey('__isset', array($property), $options);
                }
                if ($removeKeys) {
                    $classOptions->getStorage()->removeMulti($removeKeys);
                }
                return;

            case '__get':
                $property = array_shift($args);

                if (!$classOptions->getObjectCacheMagicProperties()
                    || property_exists($object, $property)
                ) {
                    // no caching if property isn't magic
                    // or caching magic properties is disabled
                    return $object->{$property};
                }

                array_unshift($args, $property);

                if (!isset($options['callback_key'])) {
                    if ((isset($options['entity_key']) 
                        && ($entityKey = $options['entity_key']) !== null)
                        || ($entityKey = $classOptions->getObjectKey() !== null)
                    ) {
                        $options['callback_key'] = $entityKey . '::' . strtolower($method);
                        unset($options['entity_key']);
                    }
                }

                return parent::call(array($object, '__get'), $args, $options);

           case '__isset':
                $property = array_shift($args);

                if (!$classOptions->getObjectCacheMagicProperties()
                    || property_exists($object, $property)
                ) {
                    // no caching if property isn't magic
                    // or caching magic properties is disabled
                    return isset($object->{$property});
                }

                if (!isset($options['callback_key'])) {
                    if ((isset($options['entity_key']) 
                        && ($entityKey = $options['entity_key']) !== null)
                        || ($entityKey = $classOptions->getObjectKey() !== null)
                    ) {
                        $options['callback_key'] = $entityKey . '::' . strtolower($method);
                        unset($options['entity_key']);
                    }
                }

                return parent::call(array($object, '__isset'), array($property), $options);

            case '__unset':
                $property = array_shift($args);

                unset($object->{$property});

                if (!$classOptions->getObjectCacheMagicProperties()
                    || property_exists($object, $property)
                ) {
                    // no caching if property isn't magic
                    // or caching magic properties is disabled
                    return;
                }

                // remove previous cached __get and __isset calls
                $removeKeys = null;
                if (method_exists($object, '__get')) {
                    $removeKeys[] = $this->generateKey('__get', array($property), $options);
                }
                if (method_exists($object, '__isset')) {
                    $removeKeys[] = $this->generateKey('__isset', array($property), $options);
                }
                if ($removeKeys) {
                    $classOptions->getStorage()->removeMulti($removeKeys);
                }
                return;
        }

        $cache = $classOptions->getCacheByDefault();
        if ($cache) {
            $cache = !in_array($method, $classOptions->getObjectNonCacheMethods());
        } else {
            $cache = in_array($method, $classOptions->getObjectCacheMethods());
        }

        if (!$cache) {
            if ($args) {
                return call_user_func_array(array($object, $method), $args);
            } 
            return $object->{$method}();
        }

        if (!isset($options['callback_key'])) {
            if ((isset($options['entity_key']) && ($entityKey = $options['entity_key']) !== null)
                || (($entityKey = $classOptions->getObjectKey()) !== null)
            ) {
                $options['callback_key'] = $entityKey . '::' . strtolower($method);
                unset($options['entity_key']);
            }
        }

        return parent::call(array($object, $method), $args, $options);
    }

    /**
     * Generate a unique key from the method name and arguments
     *
     * @param  string   $method  The method name
     * @param  array    $args    Method arguments
     * @param  array    $options Options
     * @return string
     * @throws Exception
     */
    public function generateKey($method, array $args = array(), array $options = array())
    {
        $classOptions = $this->getOptions();
        if (!isset($options['callback_key'])) {
            if ( (isset($options['entity_key']) && ($entityKey = $options['entity_key']) !== null)
              || (($entityKey = $classOptions->getObjectKey()) !== null)) {
                $options['callback_key'] = $entityKey . '::' . strtolower($method);
                unset($options['entity_key']);
            }
        }

        return parent::generateKey(array($classOptions->getObject(), $method), $args, $options);
    }

    /**
     * Class method call handler
     *
     * @param  string $method  Method name to call
     * @param  array  $args    Method arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($method, array $args)
    {
        return $this->call($method, $args);
    }

    /**
     * Writing data to properties.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it calls __set
     * and removes cached data of previous __get and __isset calls.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     * @see    http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        return $this->call('__set', array($name, $value));
    }

    /**
     * Reading data from properties.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it calls __get.
     *
     * @param  string $name
     * @return mixed
     * @see http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        return $this->call('__get', array($name));
    }

    /**
     * Checking existing properties.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it calls __get.
     *
     * @param  string $name
     * @return bool
     * @see    http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __isset($name)
    {
        return $this->call('__isset', array($name));
    }

    /**
     * Unseting a property.
     *
     * NOTE:
     * Magic properties will be cached too if the option cacheMagicProperties
     * is enabled and the property doesn't exist in real. If so it removes
     * previous cached __isset and __get calls.
     *
     * @param  string $name
     * @return void
     * @see    http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __unset($name)
    {
        return $this->call('__unset', array($name));
    }

    /**
     * Handle casting to string
     *
     * @return string
     * @see    http://php.net/manual/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return $this->call('__toString');
    }

    /**
     * Handle invoke calls
     *
     * @return mixed
     * @see    http://php.net/manual/language.oop5.magic.php#language.oop5.magic.invoke
     */
    public function __invoke() 
    {
        return $this->call('__invoke', func_get_args());
    }
}
