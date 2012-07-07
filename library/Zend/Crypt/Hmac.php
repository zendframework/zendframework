<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace Zend\Crypt;

/**
 * PHP implementation of the RFC 2104 Hash based Message Authentication Code
 *
 * @category   Zend
 * @package    Zend_Crypt
 */
class Hmac
{
    const OUTPUT_STRING = 'string';
    const OUTPUT_BINARY = 'binary';

    /**
     * List of hash algorithms supported
     *
     * @var array
     */
    protected static $supportedAlgorithms = array();

    /**
     * Performs a HMAC computation given relevant details such as Key, Hashing
     * algorithm, the data to compute MAC of, and an output format of String,
     * or Binary.
     *
     * @param  string $key
     * @param  string $hash
     * @param  string $data
     * @param  string $output
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public static function compute($key, $hash, $data, $output = self::OUTPUT_STRING)
    {
        if (!isset($key) || empty($key)) {
            throw new Exception\InvalidArgumentException('Provided key is null or empty');
        }

        $hash = strtolower($hash);
        if (!self::isSupported($hash)) {
            throw new Exception\InvalidArgumentException(
                "Hash algorithm is not supported on this PHP installation; provided '{$hash}'"
            );
        }

        $output = ($output === self::OUTPUT_BINARY);
        return hash_hmac($hash, $data, $key, $output);
    }

    /**
     * Get the output size according to the hash algorithm and the output format
     *
     * @param  string $hash
     * @param  string $output
     * @return integer
     */
    public static function getOutputSize($hash, $output = self::OUTPUT_STRING)
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
