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
 * @package    Zend_Math
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Math;

/**
 * @category   Zend
 * @package    Zend_Math
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Math extends BigInteger
{
    /**
     * Generate random bytes using OpenSSL and/or MCRYPT_DEV_URANDOM and/or /dev/urandom
     *
     * @param  integer $length
     * @param  boolean $strong true if you need a strong random generator (cryptography)
     * @return string
     */
    public static function randBytes($length, $strong = false)
    {
        if ($length <= 0) {
            return false;
        }
        $rand = '';
        if (extension_loaded('openssl')) {
            $rand = openssl_random_pseudo_bytes($length, $secure);
            if (!$secure) {
                $rand = '';
            }
        }
        if (extension_loaded('mcrypt')) {
            $rand ^= mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        }
        if (file_exists('/dev/urandom')) {
            $dev = fopen('/dev/urandom', 'r');
            $rand ^= fread($dev, $length);
            fclose($dev);
        }
        if (empty($rand)) {
            if ($strong) {
                throw new Exception\RuntimeException(
                    'This PHP environment doesn\'t support secure random number generation. ' .
                        'Please consider to install the OpenSSL and/or Mcrypt extensions'
                );
            }
            for ($i = 0; $i < $length; $i++) {
                $rand .= chr(mt_rand(0, 255));
            }
        }
        return $rand;
    }

    /**
     * Generate a random number between $min and $max
     *
     * @param  integer $min
     * @param  integer $max
     * @param  boolean $strong true if you need a strong random generator (cryptography)
     * @return integer
     */
    public static function rand($min, $max, $strong = false)
    {
        if ($min > $max) {
            throw new Exception\InvalidArgumentException(
                'The min parameter must be lower than max parameter'
            );
        }
        $range = $max - $min;
        if ($range == 0) {
            return $max;
        } elseif ($range > PHP_INT_MAX || is_float($range)) {
            throw new Exception\InvalidArgumentException(
                'The supplied range is too great to generate'
            );
        }
        $bits  = (int)floor(log($range, 2) + 1);
        $bytes = (int)max(ceil($bits / 8), 1);
        $mask  = (int)(pow(2, $bits) - 1);
        do {
            $test   = self::randBytes($bytes, $strong);
            $result = hexdec(bin2hex($test)) & $mask;
        } while ($result > $range);
        return $result + $min;
    }

    /**
     * Get the big endian two's complement of a given big integer in
     * binary notation
     *
     * @param string $long
     * @return string
     */
    public function btwoc($long)
    {
        if (ord($long[0]) > 127) {
            return "\x00" . $long;
        }
        return $long;
    }

    /**
     * Translate a binary form into a big integer string
     *
     * @param string $binary
     * @return string
     */
    public function fromBinary($binary)
    {
        return $this->_math->binaryToInteger($binary);
    }

    /**
     * Translate a big integer string into a binary form
     *
     * @param string $integer
     * @return string
     */
    public function toBinary($integer)
    {
        return $this->_math->integerToBinary($integer);
    }
}
