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
 * @package    Zend_Service
 * @subpackage GoGrid
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\GoGrid;

use Zend\Service\GoGrid\Exception,
 Zend\Http\Client as HttpClient;

abstract class GoGrid
{
    const URL_API= 'https://api.gogrid.com/api/';
    const FORMAT_API= 'json';
    const VERSION_API= '1.8';
    const ILLEGAL_ARGUMENT_ERROR= 400;
    const UNHAUTHORIZED_ERROR= 401;
    const AUTHENTICATION_FAILED= 403;
    const NOT_FOUND_ERROR= 404;
    const UNEXPECTED_ERROR= 500;
    /**
     * GoGrid API key
     *
     * @var string
     */
    protected $_apiKey;
    /**
     * GoGrid secret
     * 
     * @var string 
     */
    protected $_secret;
    /**
     * GoGrid API version
     *
     * @var string
     */
    protected $_apiVersion = self::VERSION_API;
    /**
     * HttpClient
     *
     * @var Zend\Http\Client
     */
    private $_httpClient;

    /**
     * __construct
     *
     * @param string $key
     * @param string $secret
     * @param string $apiVer
     */
    public function __construct($key, $secret, $apiVer = null)
    {
        if (!isset($key)) {
            throw new Exception\InvalidArgumentException("The key cannot be empty");
        }
        if (!isset($secret)) {
            throw new Exception\InvalidArgumentException("The secret cannot be empty");
        }
        $this->_apiKey = (string) $key;
        $this->_secret = (string) $secret;
        if (!empty($apiVer)) {
            $this->_apiVersion = (string) $apiVer;
        }
    }

    /**
     * get the HttpClient static instance
     * 
     * @return Zend\Http\Client
     */
    private function _getHttpClient()
    {
        if (empty($this->_httpClient)) {
            $this->_httpClient = new HttpClient();
        }
        return $this->_httpClient;
    }

    /**
     * Set the API secret
     * 
     * @param string $secret 
     */
    public function setSecret($secret)
    {
        if (!empty($secret)) {
            $this->_secret = (string) $secret;
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
            $this->_apiKey = (string) $key;
        }
    }

    /**
     * Set the API version
     *
     * @param string $ver
     */
    public function setVersion($ver)
    {
        if (!empty($ver) && $ver < self::API_VER) {
            $this->_apiVersion = $ver;
        }
    }

    /**
     * Get the API version
     * 
     * @return string 
     */
    public function getVersion()
    {
        return $this->_apiVersion;
    }

    /**
     * Compute the signature for the API call
     * This signature is valid in a window of 10 min with the localtime of the server
     *
     * @return string
     */
    private function _computeSignature()
    {
        return md5($this->_apiKey . $this->_secret . time());
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
        $client = $this->_getHttpClient();
        $client->setUri(self::URL_API . $method);
        $client->setParameterGet('format', self::FORMAT_API);
        $client->setParameterGet('api_key', $this->_apiKey);
        $client->setParameterGet('sig', $this->_computeSignature());
        $client->setParameterGet('v', $this->_apiVersion);
        if (!empty($options)) {
            $client->setParameterGet($options);
        }
        $this->_error= false;
        $this->_errorType= null;
        $response = $client->request();
        if ($response->isSuccessful()) {
            return json_decode($response->getBody(), true);
        } 
        $this->_error= true;
        $this->_errorType= $response->getStatus();
        return false;
    }

    /**
     * Get the last HTTP response
     *
     * @return string
     */
    public function getLastResponse()
    {
        return $this->_getHttpClient()->getLastResponse();
    }
    /**
     * Get the last HTTP request
     * 
     * @return string
     */
    public function getLastRequest()
    {
        return $this->_getHttpClient()->getLastRequest();
    }
    /**
     * Check if the last request was successful
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return ($this->_error===false);
    }
    /**
     * Get the last error type
     * 
     * @return integer
     */
    public function getLastError()
    {
        return $this->_errorType;
    }
}