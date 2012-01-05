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
 * @package    Zend_Feed_Pubsubhubbub
 * @subpackage Callback
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Feed\PubSubHubbub;

/**
 * @uses       \Zend\Feed\PubSubHubbub\Callback
 * @uses       \Zend\Feed\PubSubHubbub\Exception
 * @uses       \Zend\Feed\PubSubHubbub\HttpResponse
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @subpackage Callback
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractCallback implements Callback
{
    /**
     * An instance of Zend_Feed_Pubsubhubbub_Model_SubscriptionPersistence used 
     * to background save any verification tokens associated with a subscription
     * or other.
     *
     * @var \Zend\Feed\PubSubHubbub\Model\SubscriptionPersistence
     */
    protected $_storage = null;

    /**
     * An instance of a class handling Http Responses. This is implemented in
     * Zend\Feed\Pubsubhubbub\HttpResponse which shares an unenforced interface with
     * (i.e. not inherited from) Zend\Controller\Response\Http.
     *
     * @var Zend_Feed_Pubsubhubbub_HttpResponse|\Zend\Controller\Response\Http
     */
    protected $_httpResponse = null;

    /**
     * The number of Subscribers for which any updates are on behalf of.
     *
     * @var int
     */
    protected $_subscriberCount = 1;

    /**
     * Constructor; accepts an array or Zend\Config instance to preset
     * options for the Subscriber without calling all supported setter
     * methods in turn.
     *
     * @param array|\Zend\Config\Config $options Options array or \Zend\Config\Config instance
     */
    public function __construct($config = null)
    {
        if ($config !== null) {
            $this->setConfig($config);
        }
    }

    /**
     * Process any injected configuration options
     *
     * @param  array|\Zend\Config\Config $options Options array or \Zend\Config\Config instance
     * @return \Zend\Feed\PubSubHubbub\AbstractCallback
     */
    public function setConfig($config)
    {
        if ($config instanceof \Zend\Config\Config) {
            $config = $config->toArray();
        } elseif (!is_array($config)) {
            throw new Exception('Array or Zend_Config object'
            . 'expected, got ' . gettype($config));
        }
        if (array_key_exists('storage', $config)) {
            $this->setStorage($config['storage']);
        }
        return $this;
    }

    /**
     * Send the response, including all headers.
     * If you wish to handle this via Zend_Controller, use the getter methods
     * to retrieve any data needed to be set on your HTTP Response object, or
     * simply give this object the HTTP Response instance to work with for you!
     *
     * @return void
     */
    public function sendResponse()
    {
        $this->getHttpResponse()->sendResponse();
    }

    /**
     * Sets an instance of Zend\Feed\Pubsubhubbub\Model\SubscriptionPersistence used
     * to background save any verification tokens associated with a subscription
     * or other.
     *
     * @param  \Zend\Feed\PubSubHubbub\Model\SubscriptionPersistence $storage
     * @return \Zend\Feed\PubSubHubbub\AbstractCallback
     */
    public function setStorage(Model\SubscriptionPersistence $storage)
    {
        $this->_storage = $storage;
        return $this;
    }

    /**
     * Gets an instance of Zend\Feed\Pubsubhubbub\Model\SubscriptionPersistence used
     * to background save any verification tokens associated with a subscription
     * or other.
     *
     * @return \Zend\Feed\PubSubHubbub\Model\SubscriptionPersistence
     */
    public function getStorage()
    {
        if ($this->_storage === null) {
            throw new Exception('No storage object has been'
                . ' set that subclasses Zend\Feed\Pubsubhubbub\Model\SubscriptionPersistence');
        }
        return $this->_storage;
    }

    /**
     * An instance of a class handling Http Responses. This is implemented in
     * Zend\Feed\Pubsubhubbub\HttpResponse which shares an unenforced interface with
     * (i.e. not inherited from) Zend\Controller\Response\Http.
     *
     * @param  Zend\Feed\Pubsubhubbub\HttpResponse|\Zend\Controller\Response\Http $httpResponse
     * @return \Zend\Feed\PubSubHubbub\AbstractCallback
     */
    public function setHttpResponse($httpResponse)
    {
        if (!is_object($httpResponse)
            || (!$httpResponse instanceof HttpResponse
                && !$httpResponse instanceof \Zend\Controller\Response\Http)
        ) {
            throw new Exception('HTTP Response object must'
                . ' implement one of Zend\Feed\Pubsubhubbub\HttpResponse or'
                . ' Zend\Controller\Response\Http');
        }
        $this->_httpResponse = $httpResponse;
        return $this;
    }

    /**
     * An instance of a class handling Http Responses. This is implemented in
     * Zend\Feed\Pubsubhubbub\HttpResponse which shares an unenforced interface with
     * (i.e. not inherited from) Zend\Controller\Response\Http.
     *
     * @return Zend\Feed\Pubsubhubbub\HttpResponse|\Zend\Controller\Response\Http
     */
    public function getHttpResponse()
    {
        if ($this->_httpResponse === null) {
            $this->_httpResponse = new HttpResponse;
        }
        return $this->_httpResponse;
    }

    /**
     * Sets the number of Subscribers for which any updates are on behalf of.
     * In other words, is this class serving one or more subscribers? How many?
     * Defaults to 1 if left unchanged.
     *
     * @param  string|int $count
     * @return \Zend\Feed\PubSubHubbub\AbstractCallback
     */
    public function setSubscriberCount($count)
    {
        $count = intval($count);
        if ($count <= 0) {
            throw new Exception('Subscriber count must be'
                . ' greater than zero');
        }
        $this->_subscriberCount = $count;
        return $this;
    }

    /**
     * Gets the number of Subscribers for which any updates are on behalf of.
     * In other words, is this class serving one or more subscribers? How many?
     *
     * @return int
     */
    public function getSubscriberCount()
    {
        return $this->_subscriberCount;
    }

    /**
     * Attempt to detect the callback URL (specifically the path forward)
     */
    protected function _detectCallbackUrl()
    {
        $callbackUrl = '';
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $callbackUrl = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $callbackUrl = $_SERVER['REQUEST_URI'];
            $scheme = 'http';
            if ($_SERVER['HTTPS'] == 'on') {
                $scheme = 'https';
            }
            $schemeAndHttpHost = $scheme . '://' . $this->_getHttpHost();
            if (strpos($callbackUrl, $schemeAndHttpHost) === 0) {
                $callbackUrl = substr($callbackUrl, strlen($schemeAndHttpHost));
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            $callbackUrl= $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $callbackUrl .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        return $callbackUrl;
    }

    /**
     * Get the HTTP host
     *
     * @return string
     */
    protected function _getHttpHost()
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }
        $scheme = 'http';
        if ($_SERVER['HTTPS'] == 'on') {
            $scheme = 'https';
        }
        $name = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];
        if (($scheme == 'http' && $port == 80)
            || ($scheme == 'https' && $port == 443)
        ) {
            return $name;
        } else {
            return $name . ':' . $port;
        }
    }

    /**
     * Retrieve a Header value from either $_SERVER or Apache
     *
     * @param string $header
     */
    protected function _getHeader($header)
    {
        $temp = strtoupper(str_replace('-', '_', $header));
        if (!empty($_SERVER[$temp])) {
            return $_SERVER[$temp];
        }
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        if (!empty($_SERVER[$temp])) {
            return $_SERVER[$temp];
        }
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (!empty($headers[$header])) {
                return $headers[$header];
            }
        }
        return false;
    }

    /**
     * Return the raw body of the request
     *
     * @return string|false Raw body, or false if not present
     */
    protected function _getRawBody()
    {
        $body = file_get_contents('php://input');
        if (strlen(trim($body)) == 0 && isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $body = $GLOBALS['HTTP_RAW_POST_DATA'];
        }
        if (strlen(trim($body)) > 0) {
            return $body;
        }
        return false;
    }
}
