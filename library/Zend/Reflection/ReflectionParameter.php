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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Reflection;

/**
 * @uses       ReflectionParameter
 * @uses       \Zend\Reflection\Exception
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReflectionParameter extends \ReflectionParameter
{
    /**
     * @var bool
     */
    protected $_isFromMethod = false;

    /**
     * Get declaring class reflection object
     *
     * @param  string $reflectionClass Reflection class to use
     * @return \Zend\Reflection\ReflectionClass
     */
    public function getDeclaringClass($reflectionClass = '\Zend\Reflection\ReflectionClass')
    {
        $phpReflection  = parent::getDeclaringClass();
        $zendReflection = new $reflectionClass($phpReflection->getName());
        if (!$zendReflection instanceof ReflectionClass) {
            throw new Exception('Invalid reflection class provided; must extend Zend_Reflection_Class');
        }
        unset($phpReflection);
        return $zendReflection;
    }

    /**
     * Get class reflection object
     *
     * @param  string $reflectionClass Reflection class to use
     * @return \Zend\Reflection\ReflectionClass
     */
    public function getClass($reflectionClass = '\Zend\Reflection\ReflectionClass')
    {
        $phpReflection  = parent::getClass();
        if($phpReflection == null) {
            return null;
        }

        $zendReflection = new $reflectionClass($phpReflection->getName());
        if (!$zendReflection instanceof ReflectionClass) {
            throw new Exception('Invalid reflection class provided; must extend Zend\Reflection\ReflectionClass');
        }
        unset($phpReflection);
        return $zendReflection;
    }

    /**
     * Get declaring function reflection object
     *
     * @param  string $reflectionClass Reflection class to use
     * @return Zend_Reflection_Function|\Zend\Reflection\ReflectionMethod
     */
    public function getDeclaringFunction($reflectionClass = null)
    {
        $phpReflection = parent::getDeclaringFunction();
        if ($phpReflection instanceof \ReflectionMethod) {
            $baseClass = '\Zend\Reflection\ReflectionMethod';
            if (null === $reflectionClass) {
                $reflectionClass = $baseClass;
            }
            $zendReflection = new $reflectionClass($this->getDeclaringClass()->getName(), $phpReflection->getName());
        } else {
            $baseClass = '\Zend\Reflection\ReflectionFunction';
            if (null === $reflectionClass) {
                $reflectionClass = $baseClass;
            }
            $zendReflection = new $reflectionClass($phpReflection->getName());
        }
        if (!$zendReflection instanceof $baseClass) {
            throw new Exception('Invalid reflection class provided; must extend ' . $baseClass);
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
        if ($docblock = $this->getDeclaringFunction()->getDocblock()) {
            $params = $docblock->getTags('param');

            if (isset($params[$this->getPosition()])) {
                return $params[$this->getPosition()]->getType();
            }

        }

        return null;
    }
}
