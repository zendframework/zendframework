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
 * Tools for cryptography
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Tool
{
    /**
     * Compare two strings to avoid timing attacks
     *
     * @param  string $stringA
     * @param  string $stringB
     * @return boolean
     */
    public static function compareString($stringA, $stringB)
    {
        $stringA = (string)$stringA;
        $stringB = (string)$stringB;
        if (strlen($stringA) === 0) {
            return false;
        }
        if (strlen($stringA) !== strlen($stringB)) {
            return false;
        }
        $result = 0;
        $len    = strlen($stringA);
        for ($i = 0; $i < $len; $i++) {
            $result |= ord($stringA{$i}) ^ ord($stringB{$i});
        }
        return $result === 0;
    }
}
