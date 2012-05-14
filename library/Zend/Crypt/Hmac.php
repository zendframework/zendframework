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
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Hmac
{
    /**
     * List of hash algorithms supported
     *
     * @var array
     */
    protected static $supportedAlgorithms = array();

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
     * @return string
     */
    public static function compute($key, $hash, $data, $output = self::STRING)
    {
        if (!isset($key) || empty($key)) {
            throw new Exception\InvalidArgumentException('Provided key is null or empty');
        }
        
        $hash = strtolower($hash);
        if (!self::isSupported($hash)) {
            throw new Exception\InvalidArgumentException('Hash algorithm provided is not supported on this PHP installation');
        }
        
        if ($output == self::BINARY) {
            return hash_hmac($hash, $data, $key, 1);
        } else {
            return hash_hmac($hash, $data, $key);
        }
    }
    /**
     * Get the output size according to the hash algorithm and the output format
     * 
     * @param  string $hash
     * @param  string $output
     * @return integer 
     */
    public static function getOutputSize($hash, $output = self::STRING)
    {
        return strlen(self::compute('key', $hash, 'data', $output));
    }
    /**
     * Get the supported algorithm
     * 
     * @return array 
     */
    public static function getSupportedAlgorithms()
    {
        if (empty(self::$supportedAlgorithms)) {
            self::$supportedAlgorithms = hash_algos();
        }
        return self::$supportedAlgorithms;
    }
    /**
     * Is the hash algorithm supported?
     * 
     * @param  string $algo
     * @return boolean 
     */
    public static function isSupported($algo) 
    {
        return in_array($algo, self::getSupportedAlgorithms());
    }
}