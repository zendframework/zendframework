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

namespace Zend\OAuth;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Client extends HttpClient
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
     * @var Config\StandardConfig
     */
    protected $_config = null;

    /**
     * True if this request is being made with data supplied by
     * a stream object instead of a raw encoded string.
     *
     * @var bool
     */
    protected $_streamingRequest = null;

    /**
     * Constructor; creates a new HTTP Client instance which itself is
     * just a typical Zend_HTTP_Client subclass with some OAuth icing to
     * assist in automating OAuth parameter generation, addition and
     * cryptographic signing of requests.
     *
     * @param  array|Traversable $oauthOptions
     * @param  string $uri
     * @param  array|Traversable $options
     */
    public function __construct($oauthOptions, $uri = null, $config = null)
    {
        parent::__construct($uri, $config);
        $this->_config = new Config\StandardConfig;
        if ($oauthOptions !== null) {
            if ($oauthOptions instanceof Traversable) {
                $oauthOptions = ArrayUtils::iteratorToArray($oauthOptions);
            }
            $this->_config->setOptions($oauthOptions);
        }
    }

    /**
     * Return the current connection adapter
     *
     * @return \Zend\Http\Client\Adapter\AdapterInterface|string $adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

   /**
     * Load the connection adapter
     *
     * @param \Zend\Http\Client\Adapter\AdapterInterface $adapter
     * @return void
     */
    public function setAdapter($adapter)
    {
        if ($adapter == null) {
            $this->adapter = $adapter;
        } else {
              parent::setAdapter($adapter);
        }
    }

    /**
     * Set the streamingRequest variable which controls whether we are
     * sending the raw (already encoded) POST data from a stream source.
     *
     * @param boolean $value The value to set.
     * @return void
     */
    public function setStreamingRequest($value)
    {
        $this->_streamingRequest = $value;
    }

    /**
     * Check whether the client is set to perform streaming requests.
     *
     * @return boolean True if yes, false otherwise.
     */
    public function getStreamingRequest()
    {
        if ($this->_streamingRequest) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Prepare the request body (for POST and PUT requests)
     *
     * @return string
     * @throws \Zend\Http\Client\Exception\RuntimeException
     */
    protected function _prepareBody()
    {
        if($this->_streamingRequest) {
            $this->setHeaders(array('Content-Length' =>
                $this->raw_post_data->getTotalSize()));
            return $this->raw_post_data;
        }
        else {
            return parent::prepareBody();
        }
    }

    /**
     * Clear all custom parameters we set.
     *
     * @return HttpClient
     */
    public function resetParameters($clearAll = false)
    {
        $this->_streamingRequest = false;
        return parent::resetParameters($clearAll);
    }

    /**
     * Set the raw (already encoded) POST data from a stream source.
     *
     * This is used to support POSTing from open file handles without
     * caching the entire body into memory. It is a wrapper around
     * Zend\Http\Client::setRawBody().
     *
     * @param string $data The request data
     * @param string $enctype The encoding type
     * @return HttpClient
     */
    public function setRawDataStream($data, $enctype = null)
    {
        $this->_streamingRequest = true;
        $this->setEncType($enctype);
        return $this->setRawBody($data);
    }

    /**
     * Same as Zend_HTTP_Client::setMethod() except it also creates an
     * OAuth specific reference to the method type.
     * Might be defunct and removed in a later iteration.
     *
     * @param  string $method
     * @return HttpClient
     */
    public function setMethod($method = HttpRequest::METHOD_GET)
    {
        if ($method == HttpRequest::METHOD_GET) {
            $this->setRequestMethod(HttpRequest::METHOD_GET);
        } elseif($method == HttpRequest::METHOD_POST) {
            $this->setRequestMethod(HttpRequest::METHOD_POST);
        } elseif($method == HttpRequest::METHOD_PUT) {
            $this->setRequestMethod(HttpRequest::METHOD_PUT);
        }  elseif($method == HttpRequest::METHOD_DELETE) {
            $this->setRequestMethod(HttpRequest::METHOD_DELETE);
        }   elseif($method == HttpRequest::METHOD_HEAD) {
            $this->setRequestMethod(HttpRequest::METHOD_HEAD);
        }
        return parent::setMethod($method);
    }

    /**
     * Same as Zend\HTTP\Client::send() except just before the request is
     * executed, we automatically append any necessary OAuth parameters and
     * sign the request using the relevant signature method.
     *
     * @param  null|Zend\Http\Request $method
     * @return HttpResponse
     */
    public function send(HttpRequest $request = null)
    {
        $this->prepareOAuth();
        return parent::send($request);
    }

    /**
     * Performs OAuth preparation on the request before sending.
     *
     * This primarily means taking a request, correctly encoding and signing
     * all parameters, and applying the correct OAuth scheme to the method
     * being used.
     *
     * @return void
     * @throws \Zend\OAuth\Exception\RuntimeException If POSTBODY scheme requested, but GET request method used; or if invalid request scheme provided
     */
    public function prepareOAuth()
    {
        $requestScheme = $this->getRequestScheme();
        switch ($requestScheme) {
            case OAuth::REQUEST_SCHEME_HEADER:
                $oauthHeaderValue = $this->getToken()->toHeader(
                    $this->getRequest()->getUriString(),
                    $this->_config,
                    $this->_getSignableParameters()
                );
                $this->setHeaders(array('Authorization' => $oauthHeaderValue));
                break;
            case OAuth::REQUEST_SCHEME_POSTBODY:
                if ($this->getRequestMethod() == HttpRequest::METHOD_GET) {
                    throw new Exception\RuntimeException(
                        'The client is configured to'
                            . ' pass OAuth parameters through a POST body but request method'
                            . ' is set to GET'
                    );
                }
                $query  = $this->getToken()->toQueryString(
                    $this->getRequest()->getUriString(),
                    $this->_config,
                    $this->_getSignableParameters()
                );

                $this->setRawBody($query);
                break;
            case OAuth::REQUEST_SCHEME_QUERYSTRING:
                $query  = $this->getToken()->toQueryString(
                    $this->getRequest()->getUriString(),
                    $this->_config,
                    $this->_getSignableParameters()
                );

                $this->getUri()->setQuery($query);
                break;
            default:
                throw new Exception\RuntimeException('Invalid request scheme: ' . $requestScheme);
        }
    }

    /**
     * Collect all signable parameters into a single array across query string
     * and POST body.
     *
     * @return array
     */
    protected function _getSignableParameters()
    {
        $params = array();
        if ($this->getRequest()->getQuery()->count() > 0) {
            $params = array_merge($params, $this->getRequest()->getQuery()->toArray());
        }

        if ($this->getRequest()->getPost()->count() > 0) {
            $params = array_merge($params, $this->getRequest()->getPost()->toArray());
        }

        return $params;
    }

    /**
     * Simple Proxy to the current Zend_OAuth_Config method. It's that instance
     * which holds all configuration methods and values this object also presents
     * as it's API.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Exception\BadMethodCallException if method does not exist in config object
     */
    public function __call($method, array $args)
    {
        if (!method_exists($this->_config, $method)) {
            throw new Exception\BadMethodCallException('Method does not exist: ' . $method);
        }
        return call_user_func_array(array($this->_config,$method), $args);
    }
}
