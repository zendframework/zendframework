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
    const USER_AGENT= 'Zend\Service\Rackspace';
    const ACCOUNT_CONTAINER_COUNT= "X-account-container-count";
    const ACCOUNT_BYTES_USED= "X-account-bytes-used";
    const CONTAINER_OBJ_COUNT= "X-account-object-count";
    const CONTAINER_BYTES_USE= "X-Container-Bytes-Used";
    const METADATA_HEADER= "X-Object-Meta-";
    const MANIFEST_HEADER= "X-Object-Manifest";
    const CDN_URI= "X-CDN-URI";
    const CDN_SSL_URI= "X-CDN-SSL-URI";
    const CDN_ENABLED= "X-CDN-Enabled";
    const CDN_LOG_RETENTION= "X-Log-Retention";
    const CDN_ACL_USER_AGENT= "X-User-Agent-ACL";
    const CDN_ACL_REFERRER= "X-Referrer-ACL";
    const CDN_TTL= "X-TTL";
    const CDNM_URL= "X-CDN-Management-Url";
    const STORAGE_URL= "X-Storage-Url";
    const AUTH_TOKEN= "X-Auth-Token";
    const AUTH_USER_HEADER= "X-Auth-User";
    const AUTH_KEY_HEADER= "X-Auth-Key";
    const AUTH_USER_HEADER_LEGACY= "X-Storage-User";
    const AUTH_KEY_HEADER_LEGACY= "X-Storage-Pass";
    const AUTH_TOKEN_LEGACY= "X-Storage-Token";
    const CDN_EMAIL= "X-Purge-Email";
    
    protected $_key;
    protected $_user;
    protected $_token;
    protected $_authUrl;
    /**
     * @var Zend\Http\Client
     */
    protected $_httpClient;
    protected $_errorMsg;
    protected $_storageUrl;
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

    public function getUser()
    {
        return $this->_user;
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function getAuthUrl()
    {
        return $this->_authUrl;
    }
    public function getStorageUrl() {
        if (empty($this->_storageUrl)) {
            if (!$this->authenticate()) {
                return false;
            }
        }
        return $this->_storageUrl;
    }
    public function getCdnUrl() {
        if (empty($this->_cdnUrl)) {
            if (!$this->authenticate()) {
                return false;
            }
        }
        return $this->_cdnUrl();
    }
    public function setUser($user)
    {
        if (!empty($user)) {
            $this->_user = $user;
        }
    }
    public function setKey($key)
    {
        if (!empty($key)) {
            $this->_key = $key;
        }
    }
    public function setAuthUrl($url)
    {
        if (!empty($url) && in_array($url, array(self::US_AUTH_URL, self::UK_AUTH_URL))) {
            $this->_authUrl = $url;
        }
    }
    public function getToken()
    {
        if (empty($this->_token)) {
            if (!$this->authenticate()) {
                return false;
            }
        }
        return $this->_token;
    }
    public function getErrorMsg() {
        return $this->_errorMsg;
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
     * HTTP call
     *
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param array $get
     * @param string $body
     * @return Zend\Http\Response
     */
    protected function _httpCall($url,$method,$headers,$get=null,$body=null)
    {
        $client = $this->getHttpClient();
        $client->resetParameters(true);
        $client->setHeaders($headers);
        $client->setMethod($method);
        if (!empty($get)) {
            $client->setParameterGet($get);
        }
        if (!empty($body)) {
            $client->setRawData($body);
        }
        $client->setUri($url);
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
        } else {
            $this->_errorMsg= $result->getBody();
        }
        return false;
    } 
}