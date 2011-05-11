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
 * @subpackage Rackspace
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Rackspace;

use Zend\Service\Rackspace\Exception,
 Zend\Http\Client as HttpClient;

abstract class Rackspace
{
    const US_AUTH_URL= 'https://auth.api.rackspacecloud.com/v1.0';
    const UK_AUTH_URL= 'https://lon.auth.api.rackspacecloud.com/v1.0';
    const API_FORMAT= 'json';
    const USER_AGENT= 'Zend\Service\Rackspace';
    const STORAGE_URL= "X-Storage-Url";
    const AUTH_TOKEN= "X-Auth-Token";
    const AUTH_USER_HEADER= "X-Auth-User";
    const AUTH_KEY_HEADER= "X-Auth-Key";
    const AUTH_USER_HEADER_LEGACY= "X-Storage-User";
    const AUTH_KEY_HEADER_LEGACY= "X-Storage-Pass";
    const AUTH_TOKEN_LEGACY= "X-Storage-Token";
    const CDNM_URL= "X-CDN-Management-Url";
    /**
     * Rackspace Key
     *
     * @var string
     */
    protected $_key;
    /**
     * Rackspace account name
     *
     * @var string
     */
    protected $_user;
    /**
     * Token of authentication
     *
     * @var string
     */
    protected $_token;
    /**
     * Authentication URL
     *
     * @var string
     */
    protected $_authUrl;
    /**
     * @var Zend\Http\Client
     */
    protected $_httpClient;
    /**
     * Error Msg
     *
     * @var string
     */
    protected $_errorMsg;
    /**
     * HTTP error status
     *
     * @var string
     */
    protected $_errorStatus;
    /**
     * Storage URL
     *
     * @var string
     */
    protected $_storageUrl;
    /**
     * CDN URL
     *
     * @var string
     */
    protected $_cdnUrl;
    /**
     * __construct()
     *
     * You must pass the account and the Rackspace authentication key.
     * Optional: the authentication url (default is US)
     *
     * @param string $user
     * @param string $key
     * @param string $authUrl
     */
    public function __construct($user, $key, $authUrl=self::US_AUTH_URL)
    {
        if (!isset($user)) {
            throw new Exception\InvalidArgumentException("The user cannot be empty");
        }
        if (!isset($key)) {
            throw new Exception\InvalidArgumentException("The key cannot be empty");
        }
        if (!in_array($authUrl, array(self::US_AUTH_URL, self::UK_AUTH_URL))) {
            throw new Exception\InvalidArgumentException("The authentication URL should be valid");
        }
        $this->setUser($user);
        $this->setKey($key);
        $this->setAuthUrl($authUrl);
    }
    /**
     * Get User account
     *
     * @return string
     */
    public function getUser()
    {
        return $this->_user;
    }
    /**
     * Get user key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }
    /**
     * Get authentication URL
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->_authUrl;
    }
    /**
     * Get the storage URL
     *
     * @return string|boolean
     */
    public function getStorageUrl() {
        if (empty($this->_storageUrl)) {
            if (!$this->authenticate()) {
                return false;
            }
        }
        return $this->_storageUrl;
    }
    /**
     * Get the CDN URL
     *
     * @return string|boolean
     */
    public function getCdnUrl() {
        if (empty($this->_cdnUrl)) {
            if (!$this->authenticate()) {
                return false;
            }
        }
        return $this->_cdnUrl;
    }
    /**
     * Set the user account
     *
     * @param string $user
     * @return void
     */
    public function setUser($user)
    {
        if (!empty($user)) {
            $this->_user = $user;
        }
    }
    /**
     * Set the authentication key
     *
     * @param string $key
     * @return void
     */
    public function setKey($key)
    {
        if (!empty($key)) {
            $this->_key = $key;
        }
    }
    /**
     * Set the Authentication URL
     *
     * @param string $url
     * @return void
     */
    public function setAuthUrl($url)
    {
        if (!empty($url) && in_array($url, array(self::US_AUTH_URL, self::UK_AUTH_URL))) {
            $this->_authUrl = $url;
        } else {
            throw new Exception\InvalidArgumentException("The authentication URL is not valid");
        }
    }
    /**
     * Get the authentication token
     *
     * @return string
     */
    public function getToken()
    {
        if (empty($this->_token)) {
            if (!$this->authenticate()) {
                return false;
            }
        }
        return $this->_token;
    }
    /**
     * Get the error msg of the last REST call
     *
     * @return string
     */
    public function getErrorMsg() {
        return $this->_errorMsg;
    }
    /**
     * Get the error status of the last REST call
     * 
     * @return strig 
     */
    public function getErrorStatus() {
        return $this->_errorStatus;
    }
    /**
     * get the HttpClient instance
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
     * Return true is the last call was successful
     * 
     * @return boolean 
     */
    public function isSuccessful()
    {
        return ($this->_errorMsg=='');
    }
    /**
     * HTTP call
     *
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param array $get
     * @param string $body
     * @return Zend\Http\Response
     */
    protected function _httpCall($url,$method,$headers=array(),$get=array(),$body=null)
    {
        $client = $this->getHttpClient();
        $client->resetParameters(true);
        if (empty($headers[self::AUTH_USER_HEADER])) {
            $headers[self::AUTH_TOKEN]= $this->getToken();
        } 
        $client->setHeaders($headers);
        $client->setMethod($method);
        if (empty($get['format'])) {
            $get['format']= self::API_FORMAT;
        }
        $client->setParameterGet($get);
        if (!empty($body)) {
            $client->setRawData($body);
        }
        $client->setUri($url);
        $this->_errorMsg='';
        $this->_errorStatus='';
        return $client->request();
    }
    /**
     * Authentication
     *
     * @return boolean
     */
    public function authenticate()
    {
        $headers= array (
            self::AUTH_USER_HEADER => $this->_user,
            self::AUTH_KEY_HEADER => $this->_key
        );
        $result= $this->_httpCall($this->_authUrl,HttpClient::GET, $headers);
        if ($result->getStatus()==204) {
            $this->_token= $result->getHeader(self::AUTH_TOKEN);
            $this->_storageUrl= $result->getHeader(self::STORAGE_URL);
            $this->_cdnUrl= $result->getHeader(self::CDNM_URL);
            return true;
        }
        $this->_errorMsg= $result->getBody();
        $this->_errorStatus= $result->getStatus();
        return false;
    } 
}