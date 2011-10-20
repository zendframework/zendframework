<?php

namespace Zend\Stdlib;

use Traversable;

/**
 * Convert an iterator to an array, recursively
 *
 * Declared abstract, as we have no need for instantiation.
 */
abstract class IteratorToArray
{
    /**
     * Convert an iterator to an array, recursively
     *
     * Converts an iterator to an array. The $recursive flag, on by default, 
     * hints whether or not you want to do so recursively.
     *
     * @param  array|Traversable $iterator 
     * @return array
     */
    public static function convert($iterator, $recursive = true)
    {
        if (!is_array($iterator) && !$iterator instanceof Traversable) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable object');
        }

        if (!$recursive) {
            if (is_array($iterator)) {
                return $iterator;
            }

            return iterator_to_array($iterator);
        }

        if (method_exists($iterator, 'toArray')) {
            return $iterator->toArray();
        }

        $array = array();
        foreach ($iterator as $key => $value) {
            if (is_scalar($value)) {
                $array[$key] = $value;
                continue;
            }

            if ($value instanceof Traversable) {
                $array[$key] = static::convert($value, $recursive);
                continue;
            }

            if (is_array($value)) {
                $array[$key] = static::convert($value, $recursive);
                continue;
            }

            $array[$key] = $value;
        }

        return $array;
    }
}
