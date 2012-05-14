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
 * @uses       Zend\Crypt\Exception
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
     * @param  string $algo
     * @param  string $data
     * @param  string $binaryOutput
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
