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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\OAuth\Http;

use Zend\OAuth\Http as HTTPClient,
    Zend\OAuth,
    Zend\Http;

/**
 * @uses       Zend\Http\Client
 * @uses       Zend\OAuth\OAuth
 * @uses       Zend\OAuth\Http
 * @uses       Zend\OAuth\Token\Request
 * @category   Zend
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RequestToken extends HTTPClient
{
    /**
     * Singleton instance if required of the HTTP client
     *
     * @var Http\Client
     */
    protected $_httpClient = null;

    /**
     * Initiate a HTTP request to retrieve a Request Token.
     *
     * @return Zend\OAuth\Token\Request
     */
    public function execute()
    {
        $params   = $this->assembleParams();
        $response = $this->startRequestCycle($params);
        $return   = new OAuth\Token\Request($response);
        return $return;
    }

    /**
     * Assemble all parameters for an OAuth Request Token request.
     *
     * @return array
     */
    public function assembleParams()
    {
        $params = array(
            'oauth_consumer_key'     => $this->_consumer->getConsumerKey(),
            'oauth_nonce'            => $this->_httpUtility->generateNonce(),
            'oauth_timestamp'        => $this->_httpUtility->generateTimestamp(),
            'oauth_signature_method' => $this->_consumer->getSignatureMethod(),
            'oauth_version'          => $this->_consumer->getVersion(),
        );

        // indicates we support 1.0a
        if ($this->_consumer->getCallbackUrl()) {
            $params['oauth_callback'] = $this->_consumer->getCallbackUrl();
        } else {
            $params['oauth_callback'] = 'oob';
        }

        if (!empty($this->_parameters)) {
            $params = array_merge($params, $this->_parameters);
        }

        $params['oauth_signature'] = $this->_httpUtility->sign(
            $params,
            $this->_consumer->getSignatureMethod(),
            $this->_consumer->getConsumerSecret(),
            null,
            $this->_preferredRequestMethod,
            $this->_consumer->getRequestTokenUrl()
        );

        return $params;
    }

    /**
     * Generate and return a HTTP Client configured for the Header Request Scheme
     * specified by OAuth, for use in requesting a Request Token.
     *
     * @param array $params
     * @return Http\Client
     */
    public function getRequestSchemeHeaderClient(array $params)
    {
        $headerValue = $this->_httpUtility->toAuthorizationHeader(
            $params
        );
        $client = OAuth\OAuth::getHttpClient();
        $client->setUri($this->_consumer->getRequestTokenUrl());

        $request = $client->getRequest();
        $request->headers()
                ->addHeaderLine('Authorization', $headerValue);
        $rawdata = $this->_httpUtility->toEncodedQueryString($params, true);
        if (!empty($rawdata)) {
            $request->setContent($rawdata);
        }
        $client->setMethod($this->_preferredRequestMethod);
        return $client;
    }

    /**
     * Generate and return a HTTP Client configured for the POST Body Request
     * Scheme specified by OAuth, for use in requesting a Request Token.
     *
     * @param  array $params
     * @return Http\Client
     */
    public function getRequestSchemePostBodyClient(array $params)
    {
        $client = OAuth\OAuth::getHttpClient();
        $client->setUri($this->_consumer->getRequestTokenUrl());
        $client->setMethod($this->_preferredRequestMethod);
        $request = $client->getRequest();
        $request->setContent(
            $this->_httpUtility->toEncodedQueryString($params)
        );
        $request->headers()
                ->addHeaderLine('Content-Type', Http\Client::ENC_URLENCODED);
        return $client;
    }

    /**
     * Attempt a request based on the current configured OAuth Request Scheme and
     * return the resulting HTTP Response.
     *
     * @param  array $params
     * @return Zend\Http\Response
     */
    protected function _attemptRequest(array $params)
    {
        switch ($this->_preferredRequestScheme) {
            case OAuth\OAuth::REQUEST_SCHEME_HEADER:
                $httpClient = $this->getRequestSchemeHeaderClient($params);
                break;
            case OAuth\OAuth::REQUEST_SCHEME_POSTBODY:
                $httpClient = $this->getRequestSchemePostBodyClient($params);
                break;
            case OAuth\OAuth::REQUEST_SCHEME_QUERYSTRING:
                $httpClient = $this->getRequestSchemeQueryStringClient($params,
                    $this->_consumer->getRequestTokenUrl());
                break;
        }
        return $httpClient->send();
    }
}
