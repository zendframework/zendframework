<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace Zend\Http;

use Zend\Http\Client;

/**
 * Http static client
 *
 * @category   Zend
 * @package    Zend\Http
 */
class ClientStatic
{

    protected static $client;

    /**
     * Get the static HTTP client
     *
     * @return Zend\Http\Client
     */
    protected static function getStaticClient()
    {
        if (!isset(self::$client)) {
            self::$client = new Client();
        }
        return self::$client;
    }

    /**
     * HTTP GET METHOD (static)
     *
     * @param  string $url
     * @param  array $query
     * @param  array $headers
     * @return Response|boolean
     */
    public static function get($url, $query=array(), $headers=array(), $body=null)
    {
        if (empty($url)) {
            return false;
        }

        $request= new Request();
        $request->setUri($url);
        $request->setMethod(Request::METHOD_GET);

        if (!empty($query) && is_array($query)) {
            $request->getQuery()->fromArray($query);
        }

        if (!empty($headers) && is_array($headers)) {
            $request->getHeaders()->addHeaders($headers);
        }

        if (!empty($body)) {
            $request->setBody($body);
        }

        return self::getStaticClient()->send($request);
    }
    /**
     * HTTP POST METHOD (static)
     *
     * @param  string $url
     * @param  array $params
     * @param  array $headers
     * @return Response|boolean
     */
    public static function post($url, $params, $headers=array(), $body=null)
    {
        if (empty($url)) {
            return false;
        }

        $request= new Request();
        $request->setUri($url);
        $request->setMethod(Request::METHOD_POST);

        if (!empty($params) && is_array($params)) {
            $request->getPost()->fromArray($params);
        } else {
            throw new Exception\InvalidArgumentException('The array of post parameters is empty');
        }

        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type']= Client::ENC_URLENCODED;
        }

        if (!empty($headers) && is_array($headers)) {
            $request->getHeaders()->addHeaders($headers);
        }

        if (!empty($body)) {
            $request->setContent($body);
        }

        return self::getStaticClient()->send($request);
    }
}
