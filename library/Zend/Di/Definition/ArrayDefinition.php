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

class ArrayDefinition implements DefinitionInterface
{

    protected $dataArray = array();

    public function __construct(Array $dataArray)
    {
        foreach ($dataArray as $class => $value) {
            // force lower names
            $dataArray[$class] = array_change_key_case($dataArray[$class], CASE_LOWER);
        }
        $this->dataArray = $dataArray;
    }

    public function getClasses()
    {
        return array_keys($this->dataArray);
    }

    public function hasClass($class)
    {
        return array_key_exists($class, $this->dataArray);
    }

    public function getClassSupertypes($class)
    {
        if (!isset($this->dataArray[$class])) {
            return array();
        }

        if (!isset($this->dataArray[$class]['supertypes'])) {
            return array();
        }

        return $this->dataArray[$class]['supertypes'];
    }

    public function getInstantiator($class)
    {
        if (!isset($this->dataArray[$class])) {
            return null;
        }

        if (!isset($this->dataArray[$class]['instantiator'])) {
            return '__construct';
        }

        return $this->dataArray[$class]['instantiator'];
    }

    public function hasMethods($class)
    {
        if (!isset($this->dataArray[$class])) {
            return array();
        }

        if (!isset($this->dataArray[$class]['methods'])) {
            return array();
        }

        return (count($this->dataArray[$class]['methods']) > 0);
    }

    public function hasMethod($class, $method)
    {
        if (!isset($this->dataArray[$class])) {
            return false;
        }

        if (!isset($this->dataArray[$class]['methods'])) {
            return false;
        }

        return array_key_exists($method, $this->dataArray[$class]['methods']);
    }

    public function getMethods($class)
    {
        if (!isset($this->dataArray[$class])) {
            return array();
        }

        if (!isset($this->dataArray[$class]['methods'])) {
            return array();
        }

        return $this->dataArray[$class]['methods'];
    }

    /**
     * @param $class
     * @param $method
     * @return bool
     */
    public function hasMethodParameters($class, $method)
    {
        return isset($this->dataArray[$class]['parameters'][$method]);
    }

    public function getMethodParameters($class, $method)
    {
        if (!isset($this->dataArray[$class])) {
            return array();
        }

        if (!isset($this->dataArray[$class]['parameters'])) {
            return array();
        }

        if (!isset($this->dataArray[$class]['parameters'][$method])) {
            return array();
        }

        return $this->dataArray[$class]['parameters'][$method];
    }

    public function toArray()
    {
        return $this->dataArray;
    }

}
