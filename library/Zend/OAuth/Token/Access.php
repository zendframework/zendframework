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
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\OAuth\Token;

use Zend\OAuth\Config\ConfigInterface as Config;
use Zend\OAuth;
use Zend\Uri;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Access extends AbstractToken
{
    /**
     * Cast to HTTP header
     * 
     * @param  string $url 
     * @param  Config $config
     * @param  null|array $customParams 
     * @param  null|string $realm 
     * @return string
     * @throws OAuth\Exception\InvalidArgumentException
     */
    public function toHeader(
        $url, Config $config, array $customParams = null, $realm = null
    ) {
        $uri = Uri\UriFactory::factory($url);
        if (!$uri->isValid()
            || !in_array($uri->getScheme(), array('http', 'https'))
        ) {
            throw new OAuth\Exception\InvalidArgumentException(
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
     * @param  Zend\OAuth\Config $config 
     * @param  null|array $params 
     * @return string
     * @throws OAuth\Exception\InvalidArgumentException
     */
    public function toQueryString($url, Config $config, array $params = null)
    {
        $uri = Uri\UriFactory::factory($url);
        if (!$uri->isValid()
            || !in_array($uri->getScheme(), array('http', 'https'))
        ) {
            throw new OAuth\Exception\InvalidArgumentException(
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
     * @param  null|array|\Traversable $config
     * @param  bool $excludeCustomParamsFromHeader 
     * @return OAuth\Client
     */
    public function getHttpClient(array $oauthOptions, $uri = null, $config = null, $excludeCustomParamsFromHeader = true)
    {
        $client = new OAuth\Client($oauthOptions, $uri, $config, $excludeCustomParamsFromHeader);
        $client->setToken($this);
        return $client;
    }
}
