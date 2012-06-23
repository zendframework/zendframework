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
 * @package    Zend\Http
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Http;

use Zend\Http\Client;

/**
 * Http static client
 *
 * @category   Zend
 * @package    Zend\Http
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
            $request->query()->fromArray($query);
        }
        
        if (!empty($headers) && is_array($headers)) {
            $request->headers()->addHeaders($headers);
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
            $request->post()->fromArray($params);
        } else {
            throw new Exception\InvalidArgumentException('The array of post parameters is empty');
        }
        
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type']= Client::ENC_URLENCODED;
        }
        
        if (!empty($headers) && is_array($headers)) {
            $request->headers()->addHeaders($headers);
        }
        
        if (!empty($body)) {
            $request->setContent($body);
        }
        
        return self::getStaticClient()->send($request);
    }
}
