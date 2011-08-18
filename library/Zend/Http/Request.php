<?php

namespace Zend\Http;

use Zend\Stdlib\RequestDescription,
    Zend\Stdlib\Message,
    Zend\Stdlib\ParametersDescription,
    Zend\Stdlib\Parameters,
    Zend\Uri\Http as HttpUri;

class Request extends Message implements RequestDescription
{

    /**#@+
     * @const string METHOD constant names
     */
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';
    /**#@-*/

    /**#@+
     * @const string Version constant numbers
     */
    const VERSION_11 = '1.1';
    const VERSION_10 = '1.0';
    /**#@-*/
    
    /**
     * @var string
     */
    protected $method = self::METHOD_GET;

    /**
     * @var string|HttpUri
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
     * @param string $string
     * @return \Zend\Http\Request
     */
    public static function fromString($string)
    {
        $request = new static();

        $lines = preg_split('/\r\n/', $string);

        // first line must be Method/Uri/Version string
        $matches = null;
        $methods = implode('|', array(
            self::METHOD_OPTIONS, self::METHOD_GET, self::METHOD_HEAD, self::METHOD_POST,
            self::METHOD_PUT, self::METHOD_DELETE, self::METHOD_TRACE, self::METHOD_CONNECT
        ));
        $regex = '^(?P<method>' . $methods . ')\s(?<uri>[^ ]*)(?:\sHTTP\/(?<version>\d+\.\d+)){0,1}';
        $firstLine = array_shift($lines);
        if (!preg_match('#' . $regex . '#', $firstLine, $matches)) {
            throw new Exception\InvalidArgumentException('A valid request line was not found in the provided string');
        }

        $request->setMethod($matches['method']);
        $request->setUri($matches['uri']);

        if ($matches['version']) {
            $request->setVersion($matches['version']);
        }

        if (count($lines) == 0) {
            return $request;
        }

        $isHeader = true;
        $headers = $rawBody = array();
        while ($lines) {
            $nextLine = array_shift($lines);
            if ($nextLine == '') {
                $isHeader = false;
                continue;
            }
            if ($isHeader) {
                $headers[] .= $nextLine;
            } else {
                $rawBody[] .= $nextLine;
            }
        }

        if ($headers) {
            $request->headers = implode("\r\n", $headers);
        }

        if ($rawBody) {
            $request->setRawBody(implode("\r\n", $rawBody));
        }

        return $request;
    }

    /**
     * @param string $method
     * @return Request
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @throws Exception\InvalidArgumentException
     * @param string|HttpUri $uri
     * @return Request
     */
    public function setUri($uri)
    {
        if (is_string($uri) || $uri instanceof HttpUri) {
            $this->uri = $uri;
        } else {
            throw new Exception\InvalidArgumentException('URI must be an instance of Zend\Uri\Http or a string');
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        if ($this->uri instanceof HttpUri) {
            return $this->uri->toString();
        }
        return $this->uri;
    }

    /**
     * @return HttpUri
     */
    public function uri()
    {
        if ($this->uri === null || is_string($this->uri)) {
            $this->uri = new \Zend\Uri\Http($this->uri);
        }
        return $this->uri;
    }

    /**
     * @throws Exception\InvalidArgumentException
     * @param string $version (Must be 1.0 or 1.1)
     * @return Request
     */
    public function setVersion($version)
    {
        if (!in_array($version, array(self::VERSION_10, self::VERSION_11))) {
            throw new Exception\InvalidArgumentException('Version provided is not a valid version for this HTTP request object');
        }
        $this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param ParametersDescription $query
     * @return Request
     */
    public function setQuery(ParametersDescription $query)
    {
        $this->queryParams = $query;
        return $this;
    }

    /**
     * @return ParametersDescription
     */
    public function query()
    {
        if ($this->queryParams === null) {
            $this->queryParams = new Parameters();
        }
        return $this->queryParams;
    }
    

    /**
     * @param ParametersDescription $post
     * @return Request
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
     * @return \Zend\Stdlib\ParametersDescription
     */
    public function cookie()
    {
        return $this->headers()->get('Cookie');
    }

    /**
     * @param ParametersDescription $files
     * @return Request
     */
    public function setFile(ParametersDescription $files)
    {
        $this->fileParams = $files;
        return $this;
    }
    
    /**
     * @return ParametersDescription
     */
    public function file()
    {
        if ($this->fileParams === null) {
            $this->fileParams = new Parameters();
        }

        return $this->fileParams;
    }

    /** 
     * @param ParametersDescription
     * @return Request
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
     * @return string
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }

    /**
     * @return bool
     */
    public function isOptions()
    {
        return ($this->method === self::METHOD_OPTIONS);
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return ($this->method === self::METHOD_GET);
    }

    /**
     * @return bool
     */
    public function isHead()
    {
        return ($this->method === self::METHOD_HEAD);
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return ($this->method === self::METHOD_POST);
    }

    /**
     * @return bool
     */
    public function isPut()
    {
        return ($this->method === self::METHOD_PUT);
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return ($this->method === self::METHOD_DELETE);
    }

    /**
     * @return bool
     */
    public function isTrace()
    {
        return ($this->method === self::METHOD_TRACE);
    }

    /**
     * @return bool
     */
    public function isConnect()
    {
        return ($this->method === self::METHOD_CONNECT);
    }

    /**
     * @return string
     */
    public function renderRequestLine()
    {
        return $this->method . ' ' . (string) $this->uri . ' HTTP/' . $this->version;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $str = $this->renderRequestLine() . "\r\n";
        if ($this->headers) {
            $str .= $this->headers->toString();
        }
        $str .= "\r\n";
        $str .= $this->getContent();
        return $str;
    }

}
