<?php

namespace Zend\Http;

use Iterator,
    ArrayAccess,
    Countable;

/*
 * In most cases, extend SplQueue, and then override where necessary
 */
interface HttpHeaders extends Iterator, ArrayAccess, Countable
{
    /* 
     * General mutators and accessors 
     *
     * These are items that are technically part of the response headers, but
     * not individual headers themselves.
     */
    public function getProtocolVersion(); // HTTP 1.0, 1.1
    public function setProtocolVersion($version);

    /**
     * Adding headers 
     *
     * Also: requires overriding push, unshift to ensure values are of correct 
     * type.
     *
     * Typically, $header will be of type HttpHeader, but this allows addHeader() 
     * to operate as a factory. Suggestion is to allow HttpHeader objects, arrays,
     * or all 3 arguments.
     *
     * @param string|array|HttpHeader $header
     * @param null|string $content
     * @param bool $replace
     * @return HttpHeaders
     */
    public function addHeader($header, $content = null, $replace = false);

    /**
     * Allow adding multiple headers at once
     *
     * Implementation can vary -- could be key/value pairs, array of HttpHeader 
     * objects, array of arrays, etc -- or combination thereof.
     *
     * @param  mixed $headers
     * @return HttpHeaders
     */
    public function addHeaders($headers);

    /*
     * Retrieve named header; returns either false or a queue of headers.
     * has() tests for headers
     */
    public function get($type);
    public function has($type);

    /**
     * Representation of headers as string
     */
    public function __toString();

    /**
     * Populate object and headers from string
     *
     * Accepts text representing all headers, and splits it into either a 
     * status or request line and all provided headers.
     * 
     * @param  string $string 
     * @return HttpHeaders
     */
    public function fromString($string);
}
