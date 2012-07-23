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

/**
 * @category   Zend
 * @package    Zend_OAuth
 */
class Plaintext extends AbstractSignature
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
        if ($this->_tokenSecret === null) {
            return $this->_consumerSecret . '&';
        }
        $return = implode('&', array($this->_consumerSecret, $this->_tokenSecret));
        return $return;
    }
}
