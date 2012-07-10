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
            $className = get_class($object);
        } else {
            $className = $object;
        }
        $className = ltrim($className, '\\');
        $type = ltrim($type, '\\');
        static $isSubclassFuncCache = null; // null as unset, array when set
        if ($isSubclassFuncCache === null) {
            $isSubclassFuncCache = array();
        }
        if (!array_key_exists($className, $isSubclassFuncCache)) {
            $parents = class_parents($className, true) + class_implements($className, true);
            $caseInsensitiveParents = array();
            foreach ($parents as $parent) {
                $caseInsensitiveParent = strtolower($parent);
                $caseInsensitiveParents[$caseInsensitiveParent] = $caseInsensitiveParent;
            }
            $isSubclassFuncCache[strtolower($className)] = $caseInsensitiveParents;
        }
        return (isset($isSubclassFuncCache[strtolower($className)][strtolower($type)]));
    }
}
