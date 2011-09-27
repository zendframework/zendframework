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

use ReflectionProperty as PhpReflectionProperty,
    Zend\Code\Reflection;

/**
 * @todo       implement line numbers
 * @uses       ReflectionProperty
 * @uses       \Zend\Code\Reflection\ReflectionClass
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PropertyReflection extends PhpReflectionProperty implements Reflection
{
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
     * Get docblock comment
     *
     * @return string|false False if no docblock defined
     */
    public function getDocComment()
    {
        return parent::getDocComment();
    }

    /**
     * @return false|DocBlockReflection
     */
    public function getDocBlock()
    {
        if (!($docComment = $this->getDocComment())) {
            return false;
        }
        $r = new DocBlockReflection($docComment);
        return $r;
    }

    public function toString()
    {
        return $this->__toString();
    }
}
