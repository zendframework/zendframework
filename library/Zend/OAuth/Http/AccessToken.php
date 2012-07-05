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

namespace Zend\OAuth\Http;
use Zend\OAuth\Http as HTTPClient;
use Zend\OAuth;
use Zend\Http;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AccessToken extends HTTPClient
{
    /**
     * Singleton instance if required of the HTTP client
     *
     * @var \Zend\Http\Client
     */
    protected $_httpClient = null;

    /**
     * Initiate a HTTP request to retrieve an Access Token.
     *
     * @return \Zend\OAuth\Token\Access
     */
    public function execute()
    {
        $params   = $this->assembleParams();
        $response = $this->startRequestCycle($params);
        $return   = new OAuth\Token\Access($response);
        return $return;
    }

    /**
     * Assemble all parameters for an OAuth Access Token request.
     *
     * @return array
     */
    public function assembleParams()
    {
        $params = array(
            'oauth_consumer_key'     => $this->_consumer->getConsumerKey(),
            'oauth_nonce'            => $this->_httpUtility->generateNonce(),
            'oauth_signature_method' => $this->_consumer->getSignatureMethod(),
            'oauth_timestamp'        => $this->_httpUtility->generateTimestamp(),
            'oauth_token'            => $this->_consumer->getLastRequestToken()->getToken(),
            'oauth_version'          => $this->_consumer->getVersion(),
        );

        if (!empty($this->_parameters)) {
            $params = array_merge($params, $this->_parameters);
        }

        $params['oauth_signature'] = $this->_httpUtility->sign(
            $params,
            $this->_consumer->getSignatureMethod(),
            $this->_consumer->getConsumerSecret(),
            $this->_consumer->getLastRequestToken()->getTokenSecret(),
            $this->_preferredRequestMethod,
            $this->_consumer->getAccessTokenUrl()
        );

        return $params;
    }

    /**
     * Generate and return a HTTP Client configured for the Header Request Scheme
     * specified by OAuth, for use in requesting an Access Token.
     *
     * @param  array $params
     * @return Zend\Http\Client
     */
    public function getRequestSchemeHeaderClient(array $params)
    {
        $params      = $this->_cleanParamsOfIllegalCustomParameters($params);
        $headerValue = $this->_toAuthorizationHeader($params);
        $client      = OAuth\OAuth::getHttpClient();

        $client->setUri($this->_consumer->getAccessTokenUrl());
        $client->setHeaders(array('Authorization' =>  $headerValue));
        $client->setMethod($this->_preferredRequestMethod);

        return $client;
    }

    /**
     * Generate and return a HTTP Client configured for the POST Body Request
     * Scheme specified by OAuth, for use in requesting an Access Token.
     *
     * @param  array $params
     * @return Zend\Http\Client
     */
    public function getRequestSchemePostBodyClient(array $params)
    {
        $params = $this->_cleanParamsOfIllegalCustomParameters($params);
        $client = OAuth\OAuth::getHttpClient();
        $client->setUri($this->_consumer->getAccessTokenUrl());
        $client->setMethod($this->_preferredRequestMethod);
        $client->setRawBody(
            $this->_httpUtility->toEncodedQueryString($params)
        );
        $client->setHeaders(array('ContentType' => Http\Client::ENC_URLENCODED));
        return $client;
    }

    /**
     * Generate and return a HTTP Client configured for the Query String Request
     * Scheme specified by OAuth, for use in requesting an Access Token.
     *
     * @param  array $params
     * @param  string $url
     * @return Zend\Http\Client
     */
    public function getRequestSchemeQueryStringClient(array $params, $url)
    {
        $params = $this->_cleanParamsOfIllegalCustomParameters($params);
        return parent::getRequestSchemeQueryStringClient($params, $url);
    }

    /**
     * Access Token requests specifically may not contain non-OAuth parameters.
     * So these should be striped out and excluded. Detection is easy since
     * specified OAuth parameters start with "oauth_", Extension params start
     * with "xouth_", and no other parameters should use these prefixes.
     *
     * xouth params are not currently allowable.
     *
     * @param  array $params
     * @return array
     */
    protected function _cleanParamsOfIllegalCustomParameters(array $params)
    {
        foreach ($params as $key=>$value) {
            if (!preg_match("/^oauth_/", $key)) {
                unset($params[$key]);
            }
        }
        return $params;
    }
}
