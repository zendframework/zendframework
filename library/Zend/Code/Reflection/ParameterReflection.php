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
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Code\Reflection;

use ReflectionParameter,
    Zend\Code\Reflection;

/**
 * @uses       ReflectionParameter
 * @uses       \Zend\Code\Reflection\Exception
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ParameterReflection extends ReflectionParameter implements Reflection
{
    /**
     * @var bool
     */
    protected $isFromMethod = false;

    /**
     * Get declaring class reflection object
     *
     * @param  string $reflectionClass Reflection class to use
     * @return \Zend\Code\Reflection\ReflectionClass
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
     * @param  string $reflectionClass Reflection class to use
     * @return \Zend\Code\Reflection\ReflectionClass
     */
    public function getClass()
    {
        $phpReflection  = parent::getClass();
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
     * @return Zend_Reflection_Function|\MethodReflection\Code\Reflection\ReflectionMethod
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
        if ($docblock = $this->getDeclaringFunction()->getDocBlock()) {
            $params = $docblock->getTags('param');

            if (isset($params[$this->getPosition()])) {
                return $params[$this->getPosition()]->getType();
            }

        }

        return null;
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
