<?php

namespace Zend\Stdlib;

abstract class SubClass
{

    /**
     * @var array
     */
    protected static $cache = array();

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
        if (!array_key_exists(strtolower($className), self::$cache)) {
            $parents = class_parents($className, true) + class_implements($className, true);
            foreach ($parents as $parent) {
                self::$cache[strtolower($className)] = strtolower($parent);
            }
        }
        return (isset(self::$cache[strtolower($className)][strtolower($type)]));
    }
}
