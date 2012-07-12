<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service;

use InvalidArgumentException;
use Zend\Http\Client as HttpClient;

/**
 * @category   Zend
 * @package    Zend_Service
 */
abstract class AbstractService
{
    /**
     * HTTP Client used to query all web services
     *
     * @var string
     */
    protected static $_defaultHttpClient = 'Zend\\Http\\Client';

    /**
     * @var HttpClient
     */
    protected $_httpClient = null;

    /**
     * Sets the HTTP client object or client class to use for interacting with
     * services. If none is set, the default Zend\Http\Client will be used.
     *
     * @param string|HttpClient $client
     * @throws InvalidArgumentException
     */
    public static function setDefaultHttpClient($client)
    {
        if (!is_string($client) && !$client instanceof HttpClient) {
            throw new InvalidArgumentException('Invalid HTTP client provided');
        }
        self::$_defaultHttpClient = $client;
    }


    /**
     * Gets the default HTTP client object.
     *
     * @return HttpClient
     * @throws InvalidArgumentException
     */
    public static function getDefaultHttpClient()
    {
        if (is_string(self::$_defaultHttpClient)) {
            if (!class_exists(self::$_defaultHttpClient)) {
                throw new InvalidArgumentException('Default HTTP client class provided does not exist');
            }
            self::$_defaultHttpClient = new self::$_defaultHttpClient();
        }

        if (!self::$_defaultHttpClient instanceof HttpClient) {
            throw new InvalidArgumentException('Default HTTP client provided must extend Zend\\Http\\Client');
        }

        return self::$_defaultHttpClient;
    }

    /**
     * Set HTTP client instance to use with this service instance
     *
     * @param  HttpClient $client
     * @return AbstractService
     */
    public function setHttpClient(HttpClient $client)
    {
        $this->_httpClient = $client;
        return $this;
    }

    /**
     * Get the HTTP client instance registered with this service instance
     *
     * If none set, will check for a default instance.
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (null === $this->_httpClient) {
            $this->_httpClient = self::getDefaultHttpClient();
        }
        return $this->_httpClient;
    }
}

