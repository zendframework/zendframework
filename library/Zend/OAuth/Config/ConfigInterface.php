<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OAuth
 */

namespace Zend\OAuth\Config;

use Zend\OAuth\Token\TokenInterface;

/**
 * @category   Zend
 * @package    Zend_OAuth
 */
interface ConfigInterface
{
    public function setOptions(array $options);

    public function setConsumerKey($key);

    public function getConsumerKey();

    public function setConsumerSecret($secret);

    public function getConsumerSecret();

    public function setSignatureMethod($method);

    public function getSignatureMethod();

    public function setRequestScheme($scheme);

    public function getRequestScheme();

    public function setVersion($version);

    public function getVersion();

    public function setCallbackUrl($url);

    public function getCallbackUrl();

    public function setRequestTokenUrl($url);

    public function getRequestTokenUrl();

    public function setRequestMethod($method);

    public function getRequestMethod();

    public function setAccessTokenUrl($url);

    public function getAccessTokenUrl();

    public function setUserAuthorizationUrl($url);

    public function getUserAuthorizationUrl();

    public function setToken(TokenInterface $token);

    public function getToken();
}
