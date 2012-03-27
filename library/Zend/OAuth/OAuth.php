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
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\OAuth;

use Zend\Http\Client as HTTPClient;

/**
 * @uses       Zend\Http\Client
 * @category   Zend
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
            $headers = $request->headers();
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
