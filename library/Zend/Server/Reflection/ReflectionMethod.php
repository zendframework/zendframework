<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Server
 */

namespace Zend\Server\Reflection;

/**
 * Method Reflection
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage Zend_Server_Reflection
 */
class ReflectionMethod extends AbstractFunction
{
    /**
     * Parent class name
     * @var string
     */
    protected $class;

    /**
     * Parent class reflection
     * @var Zend\Server\Reflection\ReflectionClass
     */
    protected $classReflection;

    /**
     * Constructor
     *
     * @param \Zend\Server\Reflection\ReflectionClass $class
     * @param ReflectionMethod $r
     * @param string $namespace
     * @param array $argv
     * @return void
     */
    public function __construct(ReflectionClass $class, \ReflectionMethod $r, $namespace = null, $argv = array())
    {
        $this->classReflection = $class;
        $this->_reflection      = $r;

        $classNamespace = $class->getNamespace();

        // Determine namespace
        if (!empty($namespace)) {
            $this->setNamespace($namespace);
        } elseif (!empty($classNamespace)) {
            $this->setNamespace($classNamespace);
        }

        // Determine arguments
        if (is_array($argv)) {
            $this->_argv = $argv;
        }

        // If method call, need to store some info on the class
        $this->class = $class->getName();

        // Perform some introspection
        $this->_reflect();
    }

    /**
     * Return the reflection for the class that defines this method
     *
     * @return \Zend\Server\Reflection\ReflectionClass
     */
    public function getDeclaringClass()
    {
        return $this->classReflection;
    }

    /**
     * Wakeup from serialization
     *
     * Reflection needs explicit instantiation to work correctly. Re-instantiate
     * reflection object on wakeup.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->classReflection = new ReflectionClass(new \ReflectionClass($this->class), $this->getNamespace(), $this->getInvokeArguments());
        $this->_reflection = new \ReflectionMethod($this->classReflection->getName(), $this->getName());
    }

}
