<?php

namespace Zend\Stdlib;

/**
 * Simple class for testing whether a value is an associative array
 *
 * Declared abstract, as we have no need for instantiation.
 */
abstract class IsAssocArray
{
    /**
     * Test whether a value is an associative array
     *
     * We have an associative array if at least one key is a string.
     * 
     * @param  mixed $value 
     * @param  bool  $allowEmpty
     * @return bool
     */
    public static function test($value, $allowEmpty = false)
    {
        if (is_array($value)) {
            if ($allowEmpty && count($value) == 0) {
                return true;
            }
            return count(array_filter(array_keys($value), 'is_string')) > 0;
        }
        return false;
    }
}
