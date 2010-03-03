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
 * @package    Zend_Oauth
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @uses       Zend_Oauth_Client
 * @uses       Zend_Oauth_Exception
 * @uses       Zend_Oauth_Http
 * @uses       Zend_Oauth_Token
 * @uses       Zend_Uri
 * @uses       Zend_Uri_Http
 * @category   Zend
 * @package    Zend_Oauth
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Oauth_Token_Access extends Zend_Oauth_Token
{
    /**
     * Cast to HTTP header
     * 
     * @param  string $url 
     * @param  Zend_Oauth_Config_ConfigInterface $config 
     * @param  null|array $customParams 
     * @param  null|string $realm 
     * @return string
     */
    public function toHeader(
        $url, Zend_Oauth_Config_ConfigInterface $config, array $customParams = null, $realm = null
    ) {
        if (!Zend_Uri::check($url)) {
            throw new Zend_Oauth_Exception(
                '\'' . $url . '\' is not a valid URI'
            );
        }
        $params = $this->_httpUtility->assembleParams($url, $config, $customParams);
        return $this->_httpUtility->toAuthorizationHeader($params, $realm);
    }

    /**
     * Cast to HTTP query string
     * 
     * @param  mixed $url 
     * @param  Zend_Oauth_Config_ConfigInterface $config 
     * @param  null|array $params 
     * @return string
     */
    public function toQueryString($url, Zend_Oauth_Config_ConfigInterface $config, array $params = null)
    {
        if (!Zend_Uri::check($url)) {
            throw new Zend_Oauth_Exception(
                '\'' . $url . '\' is not a valid URI'
            );
        }
        $params = $this->_httpUtility->assembleParams($url, $config, $params);
        return $this->_httpUtility->toEncodedQueryString($params);
    }

    /**
     * Get OAuth client
     * 
     * @param  array $oauthOptions 
     * @param  null|string $uri 
     * @param  null|array|Zend_Config $config 
     * @param  bool $excludeCustomParamsFromHeader 
     * @return Zend_Oauth_Client
     */
    public function getHttpClient(array $oauthOptions, $uri = null, $config = null, $excludeCustomParamsFromHeader = true)
    {
        $client = new Zend_Oauth_Client($oauthOptions, $uri, $config, $excludeCustomParamsFromHeader);
        $client->setToken($this);
        return $client;
    }
}
