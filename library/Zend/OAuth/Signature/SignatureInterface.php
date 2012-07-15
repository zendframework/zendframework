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

interface SignatureInterface
{
    /**
     * Constructor
     *
     * @param  string $consumerSecret
     * @param  null|string $tokenSecret
     * @param  null|string $hashAlgo
     * @return void
     */
    public function __construct($consumerSecret, $tokenSecret = null, $hashAlgo = null);

    /**
     * Sign a request
     *
     * @param  array $params
     * @param  null|string $method
     * @param  null|string $url
     * @return string
     */
    public function sign(array $params, $method = null, $url = null);
}
