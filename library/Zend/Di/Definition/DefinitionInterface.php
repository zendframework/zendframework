<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace Zend\Di\Definition;

/**
 * @category   Zend
 * @package    Zend_Di
 */
interface DefinitionInterface
{
    /**
     * @abstract
     * @return string[]
     */
    public function getClasses();

    /**
     * @abstract
     * @param  string $class
     * @return bool
     */
    public function hasClass($class);

    /**
     * @abstract
     * @param  string   $class
     * @return string[]
     */
    public function getClassSupertypes($class);

    /**
     * @abstract
     * @param  string       $class
     * @return string|array
     */
    public function getInstantiator($class);

    /**
     * @abstract
     * @param  string $class
     * @return bool
     */
    public function hasMethods($class);

    /**
     * @abstract
     * @param  string   $class
     * @return string[]
     */
    public function getMethods($class);

    /**
     * @abstract
     * @param  string $class
     * @param  string $method
     * @return bool
     */
    public function hasMethod($class, $method);

    /**
     * @abstract
     * @param $class
     * @param $method
     * @return bool
     */
    public function hasMethodParameters($class, $method);

    /**
     * getMethodParameters() return information about a methods parameters.
     *
     * Should return an ordered named array of parameters for a given method.
     * Each value should be an array, of length 4 with the following information:
     *
     * array(
     *     0, // string|null: Type Name (if it exists)
     *     1, // bool: whether this param is required
     *     2, // string: fully qualified path to this parameter
     * );
     *
     *
     * @abstract
     * @param $class
     * @param $method
     * @return array[]
     */
    public function getMethodParameters($class, $method);
}
