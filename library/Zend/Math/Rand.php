<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace Zend\Math;

/**
 * Pseudorandom number generator (PRNG)
 *
 * @category   Zend
 * @package    Zend_Math
 * @subpackage Rand
 */
abstract class Rand
{
    /**
     * Generate random bytes using OpenSSL or Mcrypt and mt_rand() as fallback
     *
     * @param  integer $length
     * @param  boolean $strong true if you need a strong random generator (cryptography)
     * @return string
     * @throws Exception\RuntimeException
     */
    public static function getBytes($length, $strong = false)
    {
        if ($length <= 0) {
            return false;
        }
        if (extension_loaded('openssl')) {
            $rand = openssl_random_pseudo_bytes($length, $secure);
            if ($secure === true) {
                return $rand;
            }
        }
        if (extension_loaded('mcrypt')) {
            // PHP bug #55169
            // @see https://bugs.php.net/bug.php?id=55169
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' ||
                version_compare(PHP_VERSION, '5.3.7') >= 0) {
                $rand = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
                if ($rand !== false && strlen($rand) === $length) {
                    return $rand;
                }
            }
        }
        if ($strong) {
            throw new Exception\RuntimeException(
                'This PHP environment doesn\'t support secure random number generation. ' .
                'Please consider to install the OpenSSL and/or Mcrypt extensions'
            );
        }
        $rand = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= chr(mt_rand(0, 255));
        }
        return $rand;
    }

    /**
     * Generate random boolean
     *
     * @param  boolean $strong true if you need a strong random generator (cryptography)
     * @return bool
     */
    public static function getBoolean($strong = false)
    {
        $byte = static::getBytes(1, $strong);
        return (boolean) (ord($byte) % 2);
    }

    /**
     * Generate a random integer between $min and $max
     *
     * @param  integer $min
     * @param  integer $max
     * @param  boolean $strong true if you need a strong random generator (cryptography)
     * @return integer
     * @throws Exception\DomainException
     */
    public static function getInteger($min, $max, $strong = false)
    {
        if ($min > $max) {
            throw new Exception\DomainException(
                'The min parameter must be lower than max parameter'
            );
        }
        $range = $max - $min;
        if ($range == 0) {
            return $max;
        } elseif ($range > PHP_INT_MAX || is_float($range)) {
            throw new Exception\DomainException(
                'The supplied range is too great to generate'
            );
        }
        $log    = log($range, 2);
        $bytes  = (int) ($log / 8) + 1;
        $bits   = (int) $log + 1;
        $filter = (int) (1 << $bits) - 1;
        do {
            $rnd = hexdec(bin2hex(self::getBytes($bytes, $strong)));
            $rnd = $rnd & $filter;
        } while ($rnd > $range);

        return ($min + $rnd);
    }

    /**
     * Generate random float (0..1)
     * This function generates floats with platform-dependent precision
     *
     * PHP uses double precision floating-point format (64-bit) which has
     * 52-bits of significand precision. We gather 7 bytes of random data,
     * and we fix the exponent to the bias (1023). In this way we generate
     * a float of 1.mantissa.
     *
     * @param  boolean $strong  true if you need a strong random generator (cryptography)
     * @return float
     */
    public static function getFloat($strong = false)
    {
        $bytes    = static::getBytes(7, $strong);
        $bytes[6] = $bytes[6] | chr(0xF0);
        $bytes   .= chr(63); // exponent bias (1023)
        list(, $float) = unpack('d', $bytes);

        return ($float - 1);
    }

    /**
     * Generate a random string of specified length.
     *
     * Uses supplied character list for generating the new string.
     * If no character list provided - uses Base 64 character set.
     *
     * @param  integer $length
     * @param  string|null $charlist
     * @param  boolean $strong  true if you need a strong random generator (cryptography)
     * @return string
     * @throws Exception\DomainException
     */
    public static function getString($length, $charlist = null, $strong = false)
    {
        if ($length < 1) {
            throw new Exception\DomainException('Length should be >= 1');
        }

        // charlist is empty or not provided
        if (empty($charlist)) {
            $numBytes = ceil($length * 0.75);
            $bytes    = static::getBytes($numBytes);
            return substr(rtrim(base64_encode($bytes), '='), 0, $length);
        }

        $listLen = strlen($charlist);

        if ($listLen == 1) {
            return str_repeat($charlist, $length);
        }

        $bytes  = static::getBytes($length, $strong);
        $pos    = 0;
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $pos     = ($pos + ord($bytes[$i])) % $listLen;
            $result .= $charlist[$pos];
        }

        return $result;
    }
}