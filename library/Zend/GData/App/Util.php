<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\App;

/**
 * Utility class for static functions needed by \Zend\GData\App
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
class Util
{

    /**
     *  Convert timestamp into RFC 3339 date string.
     *  2005-04-19T15:30:00
     *
     * @param int $timestamp
     * @throws \Zend\GData\App\InvalidArgumentException
     */
    public static function formatTimestamp($timestamp)
    {
        $rfc3339 = '/^(\d{4})\-?(\d{2})\-?(\d{2})((T|t)(\d{2})\:?(\d{2})' .
                   '\:?(\d{2})(\.\d{1,})?((Z|z)|([\+\-])(\d{2})\:?(\d{2})))?$/';

        if (ctype_digit((string)$timestamp)) {
            return gmdate('Y-m-d\TH:i:sP', $timestamp);
        } elseif (preg_match($rfc3339, $timestamp) > 0) {
            // timestamp is already properly formatted
            return $timestamp;
        } else {
            $ts = strtotime($timestamp);
            if ($ts === false) {
                throw new InvalidArgumentException("Invalid timestamp: $timestamp.");
            }
            return date('Y-m-d\TH:i:s', $ts);
        }
    }

    /** Find the greatest key that is less than or equal to a given upper
      * bound, and return the value associated with that key.
      *
      * @param integer|null $maximumKey The upper bound for keys. If null, the
      *        maxiumum valued key will be found.
      * @param array $collection An two-dimensional array of key/value pairs
      *        to search through.
      * @returns mixed The value corresponding to the located key.
      * @throws \Zend\GData\App\Exception Thrown if $collection is empty.
      */
    public static function findGreatestBoundedValue($maximumKey, $collection)
    {
        $found = false;
        $foundKey = $maximumKey;

        // Sanity check: Make sure that the collection isn't empty
        if (count($collection) == 0) {
            throw new Exception("Empty namespace collection encountered.");
        }

        if ($maximumKey === null) {
            // If the key is null, then we return the maximum available
            $keys = array_keys($collection);
            sort($keys);
            $found = true;
            $foundKey = end($keys);
        } else {
            // Otherwise, we optimistically guess that the current version
            // will have a matching namespce. If that fails, we decrement the
            // version until we find a match.
            while (!$found && $foundKey >= 0) {
                if (array_key_exists($foundKey, $collection))
                    $found = true;
                else
                    $foundKey--;
            }
        }

        // Guard: A namespace wasn't found. Either none were registered, or
        // the current protcol version is lower than the maximum namespace.
        if (!$found) {
            throw new Exception("Namespace compatible with current protocol not found.");
        }

        return $foundKey;
    }

}
