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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Code\Reflection;

use ReflectionParameter;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        if ($docBlock = $this->getDeclaringFunction()->getDocBlock()) {
            $params = $docBlock->getTags('param');

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
