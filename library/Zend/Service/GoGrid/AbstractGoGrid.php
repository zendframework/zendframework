<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\GoGrid;

use Zend\Http\Client as HttpClient;

abstract class AbstractGoGrid
{
    const URL_API                = 'https://api.gogrid.com/api/';
    const FORMAT_API             = 'json';
    const VERSION_API            = '1.8';
    const ILLEGAL_ARGUMENT_ERROR = 400;
    const UNHAUTHORIZED_ERROR    = 401;
    const AUTHENTICATION_FAILED  = 403;
    const NOT_FOUND_ERROR        = 404;
    const UNEXPECTED_ERROR       = 500;

    /**
     * GoGrid API key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * GoGrid secret
     *
     * @var string
     */
    protected $secret;

    /**
     * GoGrid API version
     *
     * @var string
     */
    protected $apiVersion = self::VERSION_API;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var \Zend\Http\Response
     */
    protected $lastResponse;

    /**
     * Construct
     *
     * @param string $key
     * @param string $secret
     * @param string $apiVer
     */
    public function __construct($key, $secret, $apiVer = null, HttpClient $httpClient = null)
    {
        if (!isset($key)) {
            throw new Exception\InvalidArgumentException("The key cannot be empty");
        }
        if (!isset($secret)) {
            throw new Exception\InvalidArgumentException("The secret cannot be empty");
        }
        $this->setApiKey($key);
        $this->setSecret($secret);
        $this->setApiVersion($apiVer);
        $this->setHttpClient($httpClient ?: new HttpClient);
    }

    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * get the HttpClient static instance
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Set the API secret
     *
     * @param string $secret
     */
    public function setSecret($secret)
    {
        if (!empty($secret)) {
            $this->secret = (string) $secret;
        }
    }

    /**
     * Set the API key
     *
     * @param string $key
     */
    public function setApiKey($key)
    {
        if (!empty($key)) {
            $this->apiKey = (string) $key;
        }
    }

    /**
     * Set the API version
     *
     * @param string $ver
     */
    public function setApiVersion($ver)
    {
        if (!empty($ver) && $ver < self::VERSION_API) {
            $this->apiVersion = $ver;
        }
    }

    /**
     * Get the API version
     *
     * @return string
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * Compute the signature for the API call
     * This signature is valid in a window of 10 min with the localtime of the server
     *
     * @return string
     */
    private function _computeSignature()
    {
        return md5($this->apiKey . $this->secret . time());
    }

    /**
     *
     * @param string $method
     * @param array $options
     * @return array|boolean
     */
    protected function _call($method, $options=null)
    {
        if (!empty($options) && !is_array($options)) {
            throw new Exception\InvalidArgumentException("The options must be an array");
        }
        $client = $this->getHttpClient();

        $paramGet= array (
            'format'  => self::FORMAT_API,
            'api_key' => $this->apiKey,
            'sig'     => $this->_computeSignature(),
            'v'       => $this->apiVersion
        );

        if (!empty($options)) {
            $get='';
            foreach ($options as $key=>$value) {
                if (is_array($value)) {
                    foreach ($value as $val) {
                        $get.= $key.'='.urlencode($val).'&';
                    }
                } else {
                    $paramGet[$key]= $value;
                }
            }
        }
        $client->setParameterGet($paramGet);

        if (!empty($get)) {
            $client->setUri(self::URL_API . $method.'?'.$get);
        } else {
            $client->setUri(self::URL_API . $method);
        }

        $this->lastResponse = $client->send();
        return json_decode($this->lastResponse->getBody(), true);
    }

    /**
     * Get the last HTTP response
     *
     * @return string
     */
    public function getLastResponse()
    {
        return $this->getHttpClient()->getLastRawResponse();
    }

    /**
     * Get the last HTTP request
     *
     * @return string
     */
    public function getLastRequest()
    {
        return $this->getHttpClient()->getLastRawRequest();
    }

    /**
     * Get the last error type
     *
     * @return integer
     */
    public function getHttpStatus()
    {
        return $this->lastResponse->getStatusCode();
    }

}
