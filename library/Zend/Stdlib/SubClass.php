<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace Zend\Stdlib;

use ReflectionClass;

/**
 * @see https://bugs.php.net/bug.php?id=53727
 *
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage SubClass
 */
abstract class SubClass
{

    /**
     * Checks if the object has this class as one of its parents
     *
     * @see https://bugs.php.net/bug.php?id=53727
     *
     * @param object|string $object
     * @param string $type
     */
    public static function isSubclassOf($object, $type)
    {
        if (version_compare(PHP_VERSION, '5.3.7', '>=')) {
            return is_subclass_of($object, $type);
        }
        if (is_object($object)) {
            return ($object instanceof $type);
        } else {
            $className = $object;
        }
        if (is_subclass_of($className, $type)) {
            return true;
        }
        if (!class_exists($type)) {
            return false;
        }
        $r = new ReflectionClass($className);
        return $r->implementsInterface($type);
    }
}
