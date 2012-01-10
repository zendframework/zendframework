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

use Zend\Cache,
    Zend\Cache\Exception;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ClassCache extends CallbackCache
{
    /**
     * Set options
     *
     * @param  PatternOptions $options
     * @throws Exception\InvalidArgumentException if missing 'class' or 'storage' options
     */
    public function setOptions(PatternOptions $options)
    {
        parent::setOptions($options);

        if (!$options->getClass()) {
            throw new Exception\InvalidArgumentException("Missing option 'class'");
        } elseif (!$options->getStorage()) {
            throw new Exception\InvalidArgumentException("Missing option 'storage'");
        }
        return $this;
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
        $classname    = $classOptions->getClass();
        $method       = strtolower($method);
        $callback     = $classname . '::' . $method;

        $cache = $classOptions->getCacheByDefault();
        if ($cache) {
            $cache = !in_array($method, $classOptions->getClassNonCacheMethods());
        } else {
            $cache = in_array($method, $classOptions->getClassCacheMethods());
        }

        if (!$cache) {
            if ($args) {
                return call_user_func_array($callback, $args);
            } else {
                return $classname::$method();
            }
        }

        // speed up key generation
        if (!isset($options['callback_key'])) {
            $options['callback_key'] = $callback;
        }

        return parent::call($callback, $args, $options);
    }

    /**
     * Generate a key from the method name and arguments
     *
     * @param  string   $method  The method name
     * @param  array    $args    Method arguments
     * @return string
     * @throws Exception
     */
    public function generateKey($method, array $args = array(), array $options = array())
    {
        // speed up key generation
        $classOptions = $this->getOptions();
        if (!isset($options['callback_key'])) {
            $callback = $classOptions->getClass() . '::' . strtolower($method);
            $options['callback_key'] = $callback;
        } else {
            $callback = $classOptions->getClass() . '::' . $method;
        }

        return parent::generateKey($callback, $args, $options);
    }

    /**
     * Calling a method of the entity.
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
     * Set a static property
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     * @see   http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        $class = $this->getOptions()->getClass();
        $class::$name = $value;
    }

    /**
     * Get a static property
     *
     * @param  string $name
     * @return mixed
     * @see    http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        $class = $this->getOptions()->getClass();
        return $class::$name;
    }

    /**
     * Is a static property exists.
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        $class = $this->getOptions()->getClass();
        return isset($class::$name);
    }

    /**
     * Unset a static property
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $class = $this->getOptions()->getClass();
        unset($class::$name);
    }
}
