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
            $object = get_class($object);
        }
        $type = strtolower($type);
        $object = strtolower($object);
        if (!array_key_exists($object, self::$cache)) {
            self::$cache[$object] = class_parents($object, true) + class_implements($object, true);
        }
        return (isset(self::$cache[$object][$type]));
    }
}
