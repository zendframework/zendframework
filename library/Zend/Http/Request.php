<?php

namespace Zend\Http;

use Zend\Stdlib\RequestDescription,
    Zend\Stdlib\Message,
    Zend\Stdlib\ParametersDescription,
    Zend\Stdlib\Parameters;

class Request extends Message implements RequestDescription
{
    const PATTERN_REQUEST_LINE = '^%token\s(?<uri>[^ ]+) HTTP\/(?<version>\d+(\.\d+)?)$';

    const SCHEME_HTTP = 'HTTP';
    const SCHEME_HTTPS = 'HTTPS';
    
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';

    const VERSION_11 = '1.1';
    const VERSION_10 = '1.0';

    /**
     * @var string
     */
    protected $scheme = self::SCHEME_HTTP;

    /**
     * @var string
     */
    protected $method = self::METHOD_GET;

    /**
     * @var string|\Zend\Uri\Uri
     */
    protected $uri = null;

    /**
     * @var string
     */
    protected $version = self::VERSION_11;

    /**
     * @var \Zend\Stdlib\ParametersDescription
     */
    protected $queryParams = null;
    
    /**
     * @var \Zend\Stdlib\ParametersDescription
     */
    protected $postParams = null;
    
    /**
     * @var \Zend\Stdlib\ParametersDescription
     */
    protected $cookieParams = null;
    
    /**
     * @var \Zend\Stdlib\ParametersDescription
     */
    protected $fileParams = null;
    
    /**
     * @var \Zend\Stdlib\ParametersDescription
     */
    protected $serverParams = null;
    
    /**
     * @var \Zend\Stdlib\ParametersDescription
     */
    protected $envParams = null;

    /**
     * @var string|\Zend\Http\Headers
     */
    protected $headers = null;
    
    /**
     * @var string
     */
    protected $rawBody = null;

    /**
     *
     *
     * @param $string
     * @return \Zend\Http\Request
     */
    public static function fromString($string)
    {
        $request = new static();

        $segments = preg_split("/\r\n\r\n/", $string, 2);

        // first line must be Method/Uri/Version string
        $matches = null;
        if (!preg_match('#' . self::PATTERN_REQUEST_LINE . '\r\n#', $segments[0], $matches)) {
            throw new Exception\InvalidArgumentException('A valid request line was not found in the provided string');
        }

        // @todo stuff the values into the object

        // Populate headers
        $request->headers = $segments[0];

        // Populate raw body, if content found
        if (2 === count($segments)) {
            $request->setRawBody($segments[1]);
        } else {
            $request->setRawBody('');
        }

        return $request;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }


    public function setUri($uri)
    {
        if (is_string($uri) || $uri instanceof \Zend\Uri\Uri) {
            $this->uri = $uri;
        } else {
            throw new Exception\InvalidArgumentException('URI must be an instance of Zend\Uri\Uri or a string');
        }

        return $this;
    }

    public function getUri()
    {
        if ($this->uri instanceof \Zend\Uri\Uri) {
            return $this->uri->toString();
        }
        return $this->uri;
    }

    public function uri()
    {
        if ($this->uri === null || is_string($this->uri)) {
            $this->uri = new Uri($this->uri);
        }
        return $this->uri;
    }

    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param \Zend\Stdlib\ParametersDescription $query
     * @return \Zend\Http\Request
     */
    public function setQuery(ParametersDescription $query)
    {
        $this->queryParams = $query;
        return $this;
    }

    /**
     * @return \Zend\Stdlib\ParametersDescription
     */
    public function query()
    {
        if ($this->queryParams === null) {
            $this->queryParams = new Parameters();
        }
        return $this->queryParams;
    }
    
    /**
     * @param \Zend\Stdlib\ParametersDescription $query
     * @return \Zend\Http\Request
     */
    public function setPost(ParametersDescription $post)
    {
        $this->postParams = $post;
        return $this;
    }

    /**
     * @return \Zend\Stdlib\ParametersDescription
     */
    public function post()
    {
        if ($this->postParams === null) {
            $this->postParams = new Parameters();
        }

        return $this->postParams;
    }

    /**
     * @param \Zend\Stdlib\ParametersDescription $query
     * @return \Zend\Http\Request
     */
    public function setCookie(ParametersDescription $cookies)
    {
        $this->cookieParams = $cookies;
        return $this;
    }
    
    /**
     * @return \Zend\Stdlib\ParametersDescription
     */
    public function cookie()
    {
        if ($this->cookieParams === null) {
            $this->cookieParams = new Parameters();
        }

        return $this->cookieParams;
    }

    /**
     * Set files parameters
     * 
     * @param  Zend\Stdlib\ParametersDescription $files 
     * @return \Zend\Http\Request
     */
    public function setFile(ParametersDescription $files)
    {
        $this->fileParams = $files;
        return $this;
    }
    
    /**
     * @return \Zend\Stdlib\ParametersDescription
     */
    public function file()
    {
        if ($this->fileParams === null) {
            $this->fileParams = new Parameters();
        }

        return $this->fileParams;
    }

    /** 
     * @param \Zend\Stdlib\ParametersDescription
     * @return \Zend\Http\Request
     */
    public function setServer(ParametersDescription $server)
    {
        $this->serverParams = $server;
        return $this;
    }
    
    /**
     * @return \Zend\Stdlib\ParametersDescription
     */
    public function server()
    {
        if ($this->serverParams === null) {
            $this->serverParams = new Parameters();
        }

        return $this->serverParams;
    }

    /**
     * @param \Zend\Stdlib\ParametersDescription $env
     * @return \Zend\Http\Request
     */
    public function setEnv(ParametersDescription $env)
    {
        $this->envParams = $env;
        return $this;
    }

    /**
     * @return \Zend\Stdlib\ParametersDescription
     */
    public function env()
    {
        if ($this->envParams === null) {
            $this->envParams = new Parameters();
        }

        return $this->envParams;
    }

    /**
     *
     * @param \Zend\Http\RequestHeaders $headers
     * @return \Zend\Http\Request
     */
    public function setHeaders(RequestHeaders $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     *
     * @return \Zend\Http\RequestHeaders
     */
    public function headers()
    {
        if ($this->headers === null || is_string($this->headers)) {
            $this->headers = (is_string($this->headers)) ? RequestHeaders::fromString($this->headers) : new RequestHeaders();
        }

        return $this->headers;
    }

    /**
     * @param string $string
     * @return \Zend\Http\Request
     */
    public function setRawBody($string)
    {
        $this->rawBody = $string;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }

    public function isOptions()
    {
        return ($this->method === self::METHOD_OPTIONS);
    }

    public function isGet()
    {
        return ($this->method === self::METHOD_GET);
    }

    public function isHead()
    {
        return ($this->method === self::METHOD_HEAD);
    }

    public function isPost()
    {
        return ($this->method === self::METHOD_POST);
    }

    public function isPut()
    {
        return ($this->method === self::METHOD_PUT);
    }

    public function isDelete()
    {
        return ($this->method === self::METHOD_DELETE);
    }

    public function isTrace()
    {
        return ($this->method === self::METHOD_TRACE);
    }

    public function isConnect()
    {
        return ($this->method === self::METHOD_CONNECT);
    }

    public function toString()
    {
        return $this->method . ' ' . (string) $this->uri . ' HTTP/' . $this->version . "\r\n"
            . $this->headers()->toString() . "\r\n"
            . $this->getContent();
    }

}
