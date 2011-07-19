<?php

namespace Zend\Http;

interface HttpRequestHeaders extends HttpHeaders
{
    /* Request-Line-related accessors/mutators */
    public function getMethod();        // GET, POST, PUT, etc.
    public function getUri();
    public function setMethod($method); // GET, POST, PUT, etc.
    public function setUri($uri);
    public function renderRequestLine(); // render the "request line" portion of the header
}
