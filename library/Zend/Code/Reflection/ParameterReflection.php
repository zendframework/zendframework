<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace Zend\Code\Reflection;

use ReflectionParameter;

/**
 * @category   Zend
 * @package    Zend_Reflection
 */
class ParameterReflection extends ReflectionParameter implements ReflectionInterface
{
    /**
     * @var bool
     */
    protected $isFromMethod = false;

    /**
     * Get declaring class reflection object
     *
     * @return ClassReflection
     */
    public function getDeclaringClass()
    {
        $phpReflection  = parent::getDeclaringClass();
        $zendReflection = new ClassReflection($phpReflection->getName());
        unset($phpReflection);

        return $zendReflection;
    }

    /**
     * Get class reflection object
     *
     * @return ClassReflection
     */
    public function getClass()
    {
        $phpReflection = parent::getClass();
        if ($phpReflection == null) {
            return null;
        }
        $zendReflection = new ClassReflection($phpReflection->getName());
        unset($phpReflection);

        return $zendReflection;
    }

    /**
     * Get declaring function reflection object
     *
     * @param  string $reflectionClass Reflection class to use
     * @return FunctionReflection|MethodReflection
     */
    public function getDeclaringFunction($reflectionClass = null)
    {
        $phpReflection = parent::getDeclaringFunction();
        if ($phpReflection instanceof \ReflectionMethod) {
            $zendReflection = new MethodReflection($this->getDeclaringClass()->getName(), $phpReflection->getName());
        } else {
            $zendReflection = new FunctionReflection($phpReflection->getName());
        }
        unset($phpReflection);

        return $zendReflection;
    }

    /**
     * Get parameter type
     *
     * @return string
     */
    public function getType()
    {
        $type = null;

        $checkDefault = true;

        if ($this->isArray()) {
            $type = 'array';
        } elseif (($class = $this->getClass()) instanceof \ReflectionClass) {
            $type = $class->getName();
        } elseif ($docBlock = $this->getDeclaringFunction()->getDocBlock()) {
            $params = $docBlock->getTags('param');

            if (isset($params[$this->getPosition()])) {
                $type = $params[$this->getPosition()]->getType();
                $checkDefault = false;
            }
        }

        if ($this->isDefaultValueAvailable() && $checkDefault) {
            if ($type === null) {
                $value = $this->getDefaultValue();
                $type = strtolower(gettype($value));

                switch ($type) {
                    case 'boolean' : $type = 'bool'; break;
                    case 'integer' : $type = 'int'; break;
                }
            } else {
                if ($this->getDefaultValue() === null) {
                   $type .= '|null';
                }
            }
        }

        return $type;
    }

    public function toString()
    {
        return parent::__toString();
    }

    public function __toString()
    {
        return parent::__toString();
    }

}
