<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */
namespace Zend\Crypt\Key\Derivation;

use Zend\Crypt\Hmac;

/**
 * PKCS #5 v2.0 standard RFC 2898
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Pbkdf2
{
    /**
     * Generate the new key
     *
     * @param  string  $hash       The hash algorithm to be used by HMAC
     * @param  string  $password   The source password/key
     * @param  string  $salt
     * @param  integer $iterations The number of iterations
     * @param  integer $length     The output size
     * @return string
     */
    public static function calc($hash, $password, $salt, $iterations, $length)
    {
        if (!in_array($hash, Hmac::getSupportedAlgorithms())) {
            throw new Exception\InvalidArgumentException("The hash algorihtm $hash is not supported by " . __CLASS__);
        }
        $num    = ceil($length / Hmac::getOutputSize($hash, Hmac::BINARY));
        $result = '';
        for ($block = 0; $block < $num; $block++) {
            $hmac = Hmac::compute($password, $hash, $salt . pack('N', $block), Hmac::BINARY);
            for ($i = 1; $i < $iterations; $i++) {
                $hmac ^= Hmac::compute($password, $hash, $hmac, Hmac::BINARY);
            }
            $result .= $hmac;
        }
        return substr($result, 0, $length);
    }
}
