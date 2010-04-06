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
 * @uses       Zend\HTTP\Client
 * @uses       Zend\OAuth\OAuth
 * @uses       Zend\OAuth\Config\StandardConfig
 * @uses       Zend\OAuth\Exception
 * @uses       Zend\OAuth\HTTP\Utility
 * @category   Zend
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Client extends \Zend\HTTP\Client
{
    /**
     * Flag to indicate that the client has detected the server as supporting
     * OAuth 1.0a
     */
    public static $supportsRevisionA = false;

    /**
     * Holds the current OAuth Configuration set encapsulated in an instance
     * of Zend_OAuth_Config; it's not a Zend_Config instance since that level
     * of abstraction is unnecessary and doesn't let me escape the accessors
     * and mutators anyway!
     *
     * @var Zend\OAuth\Config
     */
    protected $_config = null;

    /**
     * Constructor; creates a new HTTP Client instance which itself is
     * just a typical Zend_HTTP_Client subclass with some OAuth icing to
     * assist in automating OAuth parameter generation, addition and
     * cryptographioc signing of requests.
     *
     * @param  array $oauthOptions
     * @param  string $uri
     * @param  array|\Zend\Config\Config $config
     * @return void
     */
    public function __construct(array $oauthOptions, $uri = null, $config = null)
    {
        parent::__construct($uri, $config);
        $this->_config = new Config\StandardConfig;
        if (!is_null($oauthOptions)) {
            if ($oauthOptions instanceof \Zend\Config\Config) {
                $oauthOptions = $oauthOptions->toArray();
            }
            $this->_config->setOptions($oauthOptions);
        }
    }

    /**
     * Same as Zend_HTTP_Client::setMethod() except it also creates an
     * OAuth specific reference to the method type.
     * Might be defunct and removed in a later iteration.
     *
     * @param  string $method
     * @return Zend\HTTP\Client
     */
    public function setMethod($method = self::GET)
    {
        if ($method == self::GET) {
            $this->setRequestMethod(self::GET);
        } elseif($method == self::POST) {
            $this->setRequestMethod(self::POST);
        } elseif($method == self::PUT) {
            $this->setRequestMethod(self::PUT);
        }  elseif($method == self::DELETE) {
            $this->setRequestMethod(self::DELETE);
        }   elseif($method == self::HEAD) {
            $this->setRequestMethod(self::HEAD);
        }
        return parent::setMethod($method);
    }

    /**
     * Same as Zend_HTTP_Client::request() except just before the request is
     * executed, we automatically append any necessary OAuth parameters and
     * sign the request using the relevant signature method.
     *
     * @param  string $method
     * @return Zend\HTTP\Response\Response
     */
    public function request($method = null)
    {
        if (!is_null($method)) {
            $this->setMethod($method);
        }
        $this->prepareOAuth();
        return parent::request();
    }

    /**
     * Performs OAuth preparation on the request before sending.
     *
     * This primarily means taking a request, correctly encoding and signing
     * all parameters, and applying the correct OAuth scheme to the method
     * being used.
     *
     * @return void
     * @throws Zend\OAuth\Exception If POSTBODY scheme requested, but GET request method used; or if invalid request scheme provided
     */
    public function prepareOAuth()
    {
        $requestScheme = $this->getRequestScheme();
        $requestMethod = $this->getRequestMethod();
        $query = null;
        if ($requestScheme == OAuth::REQUEST_SCHEME_HEADER) {
            $params = array();
            if (!empty($this->paramsGet)) {
                $params = array_merge($params, $this->paramsGet);
                $query  = $this->getToken()->toQueryString(
                    $this->getUri(true), $this->_config, $params
                );
            }
            if (!empty($this->paramsPost)) {
                $params = array_merge($params, $this->paramsPost);
                $query  = $this->getToken()->toQueryString(
                    $this->getUri(true), $this->_config, $params
                );
            }
            $oauthHeaderValue = $this->getToken()->toHeader(
                $this->getUri(true), $this->_config, $params
            );
            $this->setHeaders('Authorization', $oauthHeaderValue);
        } elseif ($requestScheme == OAuth::REQUEST_SCHEME_POSTBODY) {
            if ($requestMethod == self::GET) {
                throw new Exception(
                    'The client is configured to'
                    . ' pass OAuth parameters through a POST body but request method'
                    . ' is set to GET'
                );
            }
            $raw = $this->getToken()->toQueryString(
                $this->getUri(true), $this->_config, $this->paramsPost
            );
            $this->setRawData($raw);
            $this->paramsPost = array();
        } elseif ($requestScheme == OAuth::REQUEST_SCHEME_QUERYSTRING) {
            $params = array();
            $query = $this->getUri()->getQuery();
            if ($query) {
                $queryParts = split('&', $this->getUri()->getQuery());
                foreach ($queryParts as $queryPart) {
                    $kvTuple = split('=', $queryPart);
                    $params[$kvTuple[0]] = 
                        (array_key_exists(1, $kvTuple) ? $kvTuple[1] : NULL);
                }
            }

            $query = $this->getToken()->toQueryString(
                $this->getUri(true), $this->_config, $params
            );
            $this->getUri()->setQuery($query);
            $this->paramsGet = array();
        } else {
            throw new Exception('Invalid request scheme: ' . $requestScheme);
        }
    }

    /**
     * Simple Proxy to the current Zend_OAuth_Config method. It's that instance
     * which holds all configuration methods and values this object also presents
     * as it's API.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Zend\OAuth\Exception if method does not exist in config object
     */
    public function __call($method, array $args)
    {
        if (!method_exists($this->_config, $method)) {
            throw new Exception('Method does not exist: ' . $method);
        }
        return call_user_func_array(array($this->_config,$method), $args);
    }
}
