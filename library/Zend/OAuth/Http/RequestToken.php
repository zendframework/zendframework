<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OAuth
 */

namespace Zend\OAuth\Http;

use Zend\Http;
use Zend\OAuth;
use Zend\OAuth\Http as HTTPClient;

/**
 * @category   Zend
 * @package    Zend_OAuth
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
        $request->getHeaders()
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
        $request->getHeaders()
                ->addHeaderLine('Content-Type', Http\Client::ENC_URLENCODED);
        return $client;
    }
}
