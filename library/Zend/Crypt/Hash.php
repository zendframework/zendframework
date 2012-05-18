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
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Hash
{
    const STRING = 'string';
    const BINARY = 'binary';
    /**
     * List of hash algorithms supported
     *
     * @var array
     */
    protected static $supportedAlgorithms = array();

    /**
     * @param  string $hash
     * @param  string $data
     * @param  string $output
     * @return string
     */
    public static function compute($hash, $data, $output = self::STRING)
    {
        $hash = strtolower($hash);
        if (!self::isSupported($hash)) {
            throw new Exception\InvalidArgumentException('Hash algorithm provided is not supported on this PHP installation');
        }

        $output = ($output === self::BINARY);
        return hash($hash, $data, $output);
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
        return strlen(self::compute($hash, 'data', $output));
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
