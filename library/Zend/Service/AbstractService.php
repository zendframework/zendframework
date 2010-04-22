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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace Zend\Service;

use Zend\HTTP\Client as HTTPClient;

/**
 * @uses       Zend\HTTP\Client
 * @category   Zend
 * @package    Zend_Service
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractService
{
    /**
     * HTTP Client used to query all web services
     *
     * @var Zend\HTTP\Client
     */
    protected static $_defaultHTTPClient = 'Zend\\HTTP\\Client';

    /**
     * @var Zend\HTTP\Client
     */
    protected $_httpClient = null;

    /**
     * Sets the HTTP client object or client class to use for interacting with 
     * services. If none is set, the default Zend\HTTP\Client will be used.
     *
     * @param string|Zend\HTTP\Client $client
     */
    public static function setDefaultHTTPClient($client)
    {
        if (!is_string($client) && !$client instanceof HTTPClient) {
            throw new Exception('Invalid HTTP client provided');
        }
        self::$_defaultHTTPClient = $client;
    }


    /**
     * Gets the default HTTP client object.
     *
     * @return Zend_Http_Client
     */
    public static function getDefaultHTTPClient()
    {
        if (is_string(self::$_defaultHTTPClient)) {
            if (!class_exists(self::$_defaultHTTPClient)) {
                throw new Exception('Default HTTP client class provided does not exist');
            }
            self::$_defaultHTTPClient = new self::$_defaultHTTPClient();
        }

        if (!self::$_defaultHTTPClient instanceof HTTPClient) {
            throw new Exception('Default HTTP client provided must extend Zend\\HTTP\\Client');
        }

        return self::$_defaultHTTPClient;
    }

    /**
     * Set HTTP client instance to use with this service instance
     * 
     * @param  Zend\HTTP\Client $client 
     * @return Zend\Service\AbstractService
     */
    public function setHTTPClient(HTTPClient $client)
    {
        $this->_httpClient = $client;
        return $this;
    }

    /**
     * Get the HTTP client instance registered with this service instance
     *
     * If none set, will check for a default instance.
     * 
     * @return Zend\HTTP\Client
     */
    public function getHTTPClient()
    {
        if (null === $this->_httpClient) {
            $this->_httpClient = self::getDefaultHTTPClient();
        }
        return $this->_httpClient;
    }
}

