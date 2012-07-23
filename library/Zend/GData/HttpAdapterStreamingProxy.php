<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData;

use Zend\Http\Client\Adapter;

/**
 * Extends the proxy HTTP adapter to handle streams instead of discrete body
 * strings.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gdata
 */
class HttpAdapterStreamingProxy extends Adapter\Proxy
{
    /**
     * The amount read from a stream source at a time.
     *
     * @var integer
     */
    const CHUNK_SIZE = 1024;

    /**
     * Send request to the proxy server with streaming support
     *
     * @param string        $method
     * @param \Zend\Uri\Uri $uri
     * @param string        $http_ver
     * @param array         $headers
     * @param string        $body
     * @return string Request as string
     */
    public function write($method, $uri, $http_ver = '1.1', $headers = array(), $body = '')
    {
        // If no proxy is set, throw an error
        if (! $this->config['proxy_host']) {
            throw new Adapter\Exception\RuntimeException('No proxy host set!');
        }

        // Make sure we're properly connected
        if (! $this->socket) {
            throw new Adapter\Exception\RuntimeException(
                'Trying to write but we are not connected');
        }

        $host = $this->config['proxy_host'];
        $port = $this->config['proxy_port'];

        if ($this->connected_to[0] != $host || $this->connected_to[1] != $port) {
            throw new Adapter\Exception\RuntimeException(
                'Trying to write but we are connected to the wrong proxy ' .
                'server');
        }

        // Add Proxy-Authorization header
        if ($this->config['proxy_user'] && ! isset($headers['proxy-authorization'])) {
            $headers['proxy-authorization'] = \Zend\Http\Client::encodeAuthHeader(
                $this->config['proxy_user'], $this->config['proxy_pass'], $this->config['proxy_auth']
            );
        }

        // if we are proxying HTTPS, preform CONNECT handshake with the proxy
        if ($uri->getScheme() == 'https' && (! $this->negotiated)) {
            $this->connectHandshake($uri->getHost(), $uri->getPort(), $http_ver, $headers);
            $this->negotiated = true;
        }

        // Save request method for later
        $this->method = $method;

        // Build request headers
        $request = "{$method} {$uri->toString()} HTTP/{$http_ver}\r\n";

        // Add all headers to the request string
        foreach ($headers as $k => $v) {
            if (is_string($k)) $v = "$k: $v";
            $request .= "$v\r\n";
        }

        $request .= "\r\n";

        // Send the request headers
        if (! @fwrite($this->socket, $request)) {
            throw new Adapter\Exception\RuntimeException(
                'Error writing request to proxy server');
        }

        //read from $body, write to socket
        while ($body->hasData()) {
            if (! @fwrite($this->socket, $body->read(self::CHUNK_SIZE))) {
                throw new Adapter\Exception\RuntimeException(
                    'Error writing request to server');
            }
        }
        return 'Large upload, request is not cached.';
    }
}
