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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service;

use Zend\Http\Client as HTTPClient;

/**
 * @uses       Zend\Http\Client
 * @category   Zend
 * @package    Zend_Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractService
{
    /**
     * HTTP Client used to query all web services
     *
     * @var Zend\Http\Client
     */
    protected static $_defaultHttpClient = 'Zend\\Http\\Client';

    /**
     * @var Zend\Http\Client
     */
    protected $_httpClient = null;

    /**
     * Sets the HTTP client object or client class to use for interacting with 
     * services. If none is set, the default Zend\Http\Client will be used.
     *
     * @param string|Zend\Http\Client $client
     */
    public static function setDefaultHttpClient($client)
    {
        if (!is_string($client) && !$client instanceof HTTPClient) {
            throw new Exception('Invalid HTTP client provided');
        }
        self::$_defaultHttpClient = $client;
    }


    /**
     * Gets the default HTTP client object.
     *
     * @return Zend_Http_Client
     */
    public static function getDefaultHttpClient()
    {
        if (is_string(self::$_defaultHttpClient)) {
            if (!class_exists(self::$_defaultHttpClient)) {
                throw new Exception('Default HTTP client class provided does not exist');
            }
            self::$_defaultHttpClient = new self::$_defaultHttpClient();
        }

        if (!self::$_defaultHttpClient instanceof HTTPClient) {
            throw new Exception('Default HTTP client provided must extend Zend\\Http\\Client');
        }

        return self::$_defaultHttpClient;
    }

    /**
     * Set HTTP client instance to use with this service instance
     * 
     * @param  Zend\Http\Client $client 
     * @return Zend\Service\AbstractService
     */
    public function setHttpClient(HTTPClient $client)
    {
        $this->_httpClient = $client;
        return $this;
    }

    /**
     * Get the HTTP client instance registered with this service instance
     *
     * If none set, will check for a default instance.
     * 
     * @return Zend\Http\Client
     */
    public function getHttpClient()
    {
        if (null === $this->_httpClient) {
            $this->_httpClient = self::getDefaultHttpClient();
        }
        return $this->_httpClient;
    }
}

