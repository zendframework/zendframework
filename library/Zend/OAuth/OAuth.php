<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OAuth
 */

namespace Zend\OAuth;

use Zend\Http\Client as HTTPClient;

/**
 * @category   Zend
 * @package    Zend_OAuth
 */
class OAuth
{
    const REQUEST_SCHEME_HEADER      = 'header';
    const REQUEST_SCHEME_POSTBODY    = 'postbody';
    const REQUEST_SCHEME_QUERYSTRING = 'querystring';
    const GET                        = 'GET';
    const POST                       = 'POST';
    const PUT                        = 'PUT';
    const DELETE                     = 'DELETE';
    const HEAD                       = 'HEAD';

    /**
     * Singleton instance if required of the HTTP client
     *
     * @var Zend\Http\Client
     */
    protected static $httpClient = null;

    /**
     * Allows the external environment to make Zend_OAuth use a specific
     * Client instance.
     *
     * @param Zend\Http\Client $httpClient
     * @return void
     */
    public static function setHttpClient(HTTPClient $httpClient)
    {
        self::$httpClient = $httpClient;
    }

    /**
     * Return the singleton instance of the HTTP Client. Note that
     * the instance is reset and cleared of previous parameters and
     * Authorization header values.
     *
     * @return Zend\Http\Client
     */
    public static function getHttpClient()
    {
        if (!isset(self::$httpClient)) {
            self::$httpClient = new HTTPClient;
        } else {
            $request = self::$httpClient->getRequest();
            $headers = $request->getHeaders();
            if ($headers->has('Authorization')) {
                $auth = $headers->get('Authorization');
                $headers->removeHeader($auth);
            }
            self::$httpClient->resetParameters();
        }
        return self::$httpClient;
    }

    /**
     * Simple mechanism to delete the entire singleton HTTP Client instance
     * which forces an new instantiation for subsequent requests.
     *
     * @return void
     */
    public static function clearHttpClient()
    {
        self::$httpClient = null;
    }
}
