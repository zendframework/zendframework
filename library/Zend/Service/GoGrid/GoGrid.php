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
 * @package    Zend\Service
 * @subpackage GoGrid
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\GoGrid;

use Zend\Http\Client as HttpClient;

abstract class GoGrid
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
     * @var Zend\Http\Client
     */
    protected $_httpClient;
    /**
     * @var Zend\Http\Response
     */
    protected $_lastResponse;
    /**
     * Construct
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
        $this->setApiKey($key);
        $this->setSecret($secret);
        $this->setApiVersion($apiVer);
    }
    /**
     * get the HttpClient static instance
     * 
     * @return Zend\Http\Client
     */
    public function getHttpClient()
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
    public function setApiVersion($ver)
    {
        if (!empty($ver) && $ver < self::VERSION_API) {
            $this->_apiVersion = $ver;
        }
    }
    /**
     * Get the API version
     * 
     * @return string 
     */
    public function getApiVersion()
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
        $client = $this->getHttpClient();
        
        $paramGet= array (
            'format'  => self::FORMAT_API,
            'api_key' => $this->_apiKey,
            'sig'     => $this->_computeSignature(),
            'v'       => $this->_apiVersion
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
        
        $this->_lastResponse = $client->send();
        return json_decode($this->_lastResponse->getBody(), true);
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
        return $this->_lastResponse->getStatusCode();
    }
}