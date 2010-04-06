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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\OAuth;

/**
 * @uses       Zend\OAuth\OAuth
 * @uses       Zend\OAuth\Exception
 * @uses       Zend\OAuth\HTTP\Utility
 * @uses       Zend\URI\URL
 * @category   Zend
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HTTP
{
    /**
     * Array of all custom service parameters to be sent in the HTTP request
     * in addition to the usual OAuth parameters.
     *
     * @var array
     */
    protected $_parameters = array();

    /**
     * Reference to the Zend_OAuth_Consumer instance in use.
     *
     * @var string
     */
    protected $_consumer = null;

    /**
     * OAuth specifies three request methods, this holds the current preferred
     * one which by default uses the Authorization Header approach for passing
     * OAuth parameters, and a POST body for non-OAuth custom parameters.
     *
     * @var string
     */
    protected $_preferredRequestScheme = null;

    /**
     * Request Method for the HTTP Request.
     *
     * @var string
     */
    protected $_preferredRequestMethod = OAuth::POST;

    /**
     * Instance of the general Zend\OAuth\Http\Utility class.
     *
     * @var Zend\OAuth\HTTP\Utility
     */
    protected $_httpUtility = null;

    /**
     * Constructor
     *
     * @param  Zend\OAuth\Consumer $consumer
     * @param  null|array $parameters
     * @param  null|Zend\OAuth\HTTP\Utility $utility
     * @return void
     */
    public function __construct(Consumer $consumer, array $parameters = null, HTTP\Utility $utility = null) 
    {
        $this->_consumer = $consumer;
        $this->_preferredRequestScheme = $this->_consumer->getRequestScheme();
        if (!is_null($parameters)) {
            $this->setParameters($parameters);
        }
        if (!is_null($utility)) {
            $this->_httpUtility = $utility;
        } else {
            $this->_httpUtility = new HTTP\Utility;
        }
    }

    /**
     * Set a preferred HTTP request method.
     *
     * @param  string $method
     * @return Zend\OAuth\HTTP
     */
    public function setMethod($method)
    {
        if (!in_array($method, array(OAuth::POST, OAuth::GET))) {
            throw new Exception('invalid HTTP method: ' . $method);
        }
        $this->_preferredRequestMethod = $method;
        return $this;
    }

    /**
     * Preferred HTTP request method accessor.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_preferredRequestMethod;
    }

    /**
     * Mutator to set an array of custom parameters for the HTTP request.
     *
     * @param  array $customServiceParameters
     * @return Zend\OAuth\HTTP
     */
    public function setParameters(array $customServiceParameters)
    {
        $this->_parameters = $customServiceParameters;
        return $this;
    }

    /**
     * Accessor for an array of custom parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Return the Consumer instance in use.
     *
     * @return Zend\OAuth\Consumer
     */
    public function getConsumer()
    {
        return $this->_consumer;
    }

    /**
     * Commence a request cycle where the current HTTP method and OAuth
     * request scheme set an upper preferred HTTP request style and where
     * failures generate a new HTTP request style further down the OAuth
     * preference list for OAuth Request Schemes.
     * On success, return the Request object that results for processing.
     *
     * @todo   Remove cycling?; Replace with upfront do-or-die configuration
     * @param  array $params
     * @return Zend\HTTP\Response\Response
     * @throws Zend\OAuth\Exception on HTTP request errors
     */
    public function startRequestCycle(array $params)
    {
        $response = null;
        $body     = null;
        $status   = null;
        try {
            $response = $this->_attemptRequest($params);
        } catch (\Zend\HTTP\Client\Exception $e) {
            throw new Exception('Error in HTTP request', null, $e);
        }
        if (!is_null($response)) {
            $body   = $response->getBody();
            $status = $response->getStatus();
        }
        if (is_null($response) // Request failure/exception
            || $status == 500  // Internal Server Error
            || $status == 400  // Bad Request
            || $status == 401  // Unauthorized
            || empty($body)    // Missing token
        ) {
            $this->_assessRequestAttempt($response);
            $response = $this->startRequestCycle($params);
        }
        return $response;
    }

    /**
     * Return an instance of Zend_Http_Client configured to use the Query
     * String scheme for an OAuth driven HTTP request.
     *
     * @param array $params
     * @param string $url
     * @return Zend\HTTP\Client
     */
    public function getRequestSchemeQueryStringClient(array $params, $url)
    {
        $client = OAuth::getHttpClient();
        $client->setUri($url);
        $client->getUri()->setQuery(
            $this->_httpUtility->toEncodedQueryString($params)
        );
        $client->setMethod($this->_preferredRequestMethod);
        return $client;
    }

    /**
     * Manages the switch from OAuth request scheme to another lower preference
     * scheme during a request cycle.
     *
     * @param  Zend\HTTP\Response\Response
     * @return void
     * @throws Zend\OAuth\Exception if unable to retrieve valid token response
     */
    protected function _assessRequestAttempt(\Zend\HTTP\Response\Response $response = null)
    {
        switch ($this->_preferredRequestScheme) {
            case OAuth::REQUEST_SCHEME_HEADER:
                $this->_preferredRequestScheme = OAuth\OAuth::REQUEST_SCHEME_POSTBODY;
                break;
            case OAuth::REQUEST_SCHEME_POSTBODY:
                $this->_preferredRequestScheme = OAuth\OAuth::REQUEST_SCHEME_QUERYSTRING;
                break;
            default:
                throw new Exception(
                    'Could not retrieve a valid Token response from Token URL:'
                    . (!is_null($response) 
                        ? PHP_EOL . $response->getBody()
                        : ' No body - check for headers')
                );
        }
    }

    /**
     * Generates a valid OAuth Authorization header based on the provided
     * parameters and realm.
     *
     * @param  array $params
     * @param  string $realm
     * @return string
     */
    protected function _toAuthorizationHeader(array $params, $realm = null)
    {
        $headerValue = array();
        $headerValue[] = 'OAuth realm="' . $realm . '"';
        foreach ($params as $key => $value) {
            if (!preg_match("/^oauth_/", $key)) {
                continue;
            }
            $headerValue[] = HTTP\Utility::urlEncode($key)
                           . '="'
                           . HTTP\Utility::urlEncode($value)
                           . '"';
        }
        return implode(",", $headerValue);
    }
}
