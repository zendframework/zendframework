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
namespace Zend\Reflection;

/**
 * @todo       implement line numbers
 * @uses       ReflectionProperty
 * @uses       \Zend\Reflection\ReflectionClass
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReflectionProperty extends \ReflectionProperty
{
    /**
     * Get declaring class reflection object
     *
     * @return \Zend\Reflection\ReflectionClass
     */
    public function getDeclaringClass($reflectionClass = 'Zend\\Reflection\\ReflectionClass')
    {
        $phpReflection  = parent::getDeclaringClass();
        $zendReflection = new $reflectionClass($phpReflection->getName());
        if (!$zendReflection instanceof ReflectionClass) {
            throw new Exception\InvalidArgumentException('Invalid reflection class provided; must extend Zend\Reflection\ReflectionClass');
        }
        unset($phpReflection);
        return $zendReflection;
    }

    /**
     * Get docblock comment
     *
     * @param  string $reflectionClass
     * @return Zend_Reflection_Docblock|false False if no docblock defined
     */
    public function getDocComment($reflectionClass = 'Zend\\Reflection\\ReflectionDocblock')
    {
        $docblock = parent::getDocComment();
        if (!$docblock) {
            return false;
        }

        $r = new $reflectionClass($docblock);
        if (!$r instanceof ReflectionDocblock) {
            throw new Exception\InvalidArgumentException('Invalid reflection class provided; must extend Zend\Reflection\ReflectionDocblock');
        }
        return $r;
    }
}
