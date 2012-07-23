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

use Zend\Crypt\PublicKey\Rsa as RsaEnc;
use Zend\Crypt\PublicKey\RsaOptions as RsaEncOptions;

/**
 * @category   Zend
 * @package    Zend_OAuth
 */
class Rsa extends AbstractSignature
{
    /**
     * Sign a request
     *
     * @param  array $params
     * @param  null|string $method
     * @param  null|string $url
     * @return string
     */
    public function sign(array $params, $method = null, $url = null)
    {
        $rsa = new RsaEnc(new RsaEncOptions(array(
            'hash_algorithm' => $this->_hashAlgorithm,
            'bnary_output'   => true
        )));

        return $rsa->sign($this->_getBaseSignatureString($params, $method, $url), $this->_key);
    }

    /**
     * Assemble encryption key
     *
     * @return string
     */
    protected function _assembleKey()
    {
        return $this->_consumerSecret;
    }
}
