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
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Crypt;

/**
 * PHP implementation of the RFC 2104 Hash based Message Authentication Code
 * algorithm.
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Hmac
{

    /**
     * The key to use for the hash
     *
     * @var string
     */
    protected static $_key = null;

    /**
     * pack() format to be used for current hashing method
     *
     * @var string
     */
    protected static $_packFormat = null;

    /**
     * Hashing algorithm; can be the md5/sha1 functions or any algorithm name
     * listed in the output of PHP 5.1.2+ hash_algos().
     *
     * @var string
     */
    protected static $_hashAlgorithm = 'md5';

    /**
     * List of algorithms supported my mhash()
     *
     * @var array
     */
    protected static $_supportedMhashAlgorithms = array(
        'adler32',
        'crc32',
        'crc32b',
        'gost',
        'haval128',
        'haval160',
        'haval192',
        'haval256',
        'md4',
        'md5',
        'ripemd160',
        'sha1',
        'sha256',
        'tiger',
        'tiger128',
        'tiger160',
    );

    /**
     * Constants representing the output mode of the hash algorithm
     */
    const STRING = 'string';
    const BINARY = 'binary';

    /**
     * Performs a HMAC computation given relevant details such as Key, Hashing
     * algorithm, the data to compute MAC of, and an output format of String,
     * Binary notation or BTWOC.
     *
     * @param  string $key
     * @param  string $hash
     * @param  string $data
     * @param  string $output
     * @param  boolean $internal
     * @return string
     */
    public static function compute($key, $hash, $data, $output = self::STRING)
    {
        // set the key
        if (!isset($key) || empty($key)) {
            throw new Exception\InvalidArgumentException('Provided key is null or empty');
        }
        self::$_key = $key;
        
        // set the hash
        self::_setHashAlgorithm($hash);

        // perform hashing and return
        return self::_hash($data, $output);
    }

    public static function getOutputSize($hash, $output = self::STRING)
    {
        return strlen(self::compute('key', $hash, 'data', $output));
    }
    /**
     * Setter for the hash method.
     *
     * @param  string $hash
     * @return void
     */
    protected static function _setHashAlgorithm($hash)
    {
        if (!isset($hash) || empty($hash)) {
            throw new Exception\InvalidArgumentException('Provided hash string is null or empty');
        }

        $hash = strtolower($hash);
        $hashSupported = false;

        if (!in_array($hash, self::$_supportedMhashAlgorithms)) {
            throw new Exception\InvalidArgumentException('Hash algorithm provided is not supported on this PHP installation');
        }
        
        self::$_hashAlgorithm = $hash;
        
        return true;
    }

    /**
     * Perform HMAC and return the keyed data
     *
     * @param  string $data
     * @param  string $output
     * @return string
     */
    protected static function _hash($data, $output = self::STRING)
    {
        if ($output == self::BINARY) {
            return hash_hmac(self::$_hashAlgorithm, $data, self::$_key, 1);
        } else {
            return hash_hmac(self::$_hashAlgorithm, $data, self::$_key);
        }
    }
    
    public static function getSupportedAlgorithms()
    {
        return self::$_supportedMhashAlgorithms;
    }
}