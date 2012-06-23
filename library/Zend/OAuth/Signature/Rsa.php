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
 * @package    Zend_OAuth;
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\OAuth\Signature;
use Zend\Crypt\PublicKey\Rsa as RSAEncryption;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $rsa = new RSAEncryption;
        $rsa->setHashAlgorithm($this->_hashAlgorithm);
        $sign = $rsa->sign(
            $this->_getBaseSignatureString($params, $method, $url),
            $this->_key,
            RSAEncryption::BASE64
        );
        return $sign;
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
