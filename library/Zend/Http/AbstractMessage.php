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

use Zend\Stdlib\Message;

/**
 * HTTP standard message (Request/Response)
 *
 * @category  Zend
 * @package   Zend_Http
 * @link      http://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4
 */
abstract class AbstractMessage extends Message
{
    /**#@+
     * @const string Version constant numbers
     */
    const VERSION_11 = '1.1';
    const VERSION_10 = '1.0';
    /**#@-*/

    /**
     * @var string
     */
    protected $version = self::VERSION_11;

    /**
     * @var Headers|string
     */
    protected $headers = null;

    /**
     * Set the HTTP version for this object, one of 1.0 or 1.1 (Request::VERSION_10, Request::VERSION_11)
     *
     * @throws Exception\InvalidArgumentException
     * @param  string $version (Must be 1.0 or 1.1)
     * @return Request
     */
    public function setVersion($version)
    {
        if ($version != self::VERSION_11 && $version != self::VERSION_10) {
            throw new Exception\InvalidArgumentException(
                'Not valid or not supported HTTP version: ' . $version
            );
        }
        $this->version = $version;
        return $this;
    }

    /**
     * Return the HTTP version for this request
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Provide an alternate Parameter Container implementation for headers in this object,
     * (this is NOT the primary API for value setting, for that see headers())
     *
     * @see    headers()
     * @param  Headers $headers
     * @return Request|Response
     */
    public function setHeaders(Headers $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Return the header container responsible for headers
     *
     * @return Headers
     */
    public function getHeaders()
    {
        if ($this->headers === null || is_string($this->headers)) {
            // this is only here for fromString lazy loading
            $this->headers = (is_string($this->headers)) ? Headers::fromString($this->headers) : new Headers();
        }

        return $this->headers;
    }


    /**
     * Allow PHP casting of this object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}