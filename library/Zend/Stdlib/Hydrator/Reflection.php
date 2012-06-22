<?php

namespace Zend\Stdlib\Hydrator;

use ReflectionClass;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Exception;

class Reflection implements HydratorInterface
{
    /**
     * Simple in-memory array cache of ReflectionProperties used.
     * @var array
     */
    static protected $reflProperties = array();

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     */
    public function extract($object)
    {
        $result = array();
        foreach(self::getReflProperties($object) as $property) {
            $result[$property->getName()] = $property->getValue($object);
        }

        return $result;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $reflProperties = self::getReflProperties($object);
        foreach($data as $key => $value) {
            if (isset($reflProperties[$key])) {
                $reflProperties[$key]->setValue($object, $value);
            }
        }
        return $object;
    }

    /**
     * Get a reflection properties from in-memory cache and lazy-load if
     * class has not been loaded.
     *
     * @static
     * @param string|object $object
     * @return array
     */
    protected static function getReflProperties($input)
    {
        if (is_object($input)) {
            $input = get_class($input);
        } else if (!is_string($input)) {
            throw new Exception\InvalidArgumentException('Input must be a string or an object.');
        }

        if (!isset(self::$reflProperties[$input])) {
            $reflClass      = new ReflectionClass($input);
            $reflProperties = $reflClass->getProperties();

            foreach($reflProperties as $key => $property) {
                $property->setAccessible(true);
                self::$reflProperties[$input][$property->getName()] = $property;
            }
        }

        return self::$reflProperties[$input];
    }
}