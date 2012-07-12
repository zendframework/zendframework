<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OAuth
 */

namespace Zend\OAuth\Signature;

use Zend\Crypt\Hmac as HMACEncryption;

/**
 * @category   Zend
 * @package    Zend_OAuth
 */
class Hmac extends AbstractSignature
{
    /**
     * Sign a request
     *
     * @param  array $params
     * @param  mixed $method
     * @param  mixed $url
     * @return string
     */
    public function sign(array $params, $method = null, $url = null)
    {
        $binaryHash = HMACEncryption::compute(
            $this->_key,
            $this->_hashAlgorithm,
            $this->_getBaseSignatureString($params, $method, $url),
            HMACEncryption::OUTPUT_BINARY
        );
        return base64_encode($binaryHash);
    }
}
