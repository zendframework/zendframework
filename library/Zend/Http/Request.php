<?php

namespace Zend\Http;

use Zend\Stdlib\RequestInterface,
    Zend\Stdlib\Message,
    Zend\Stdlib\ParametersInterface,
    Zend\Stdlib\Parameters,
    Zend\Uri\Http as HttpUri,
    Zend\Uri\Exception as ExceptionUri;

class Request extends Message implements RequestInterface
{

    /**#@+
     * @const string METHOD constant names
     */
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET     = 'GET';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_TRACE   = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_PATCH   = 'PATCH';
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
     * @var \Zend\Stdlib\ParametersInterface
     */
    protected $queryParams = null;

    /**
     * @var \Zend\Stdlib\ParametersInterface
     */
    protected $postParams = null;

    /**
     * @var \Zend\Stdlib\ParametersInterface
     */
    protected $fileParams = null;

    /**
     * @var \Zend\Stdlib\ParametersInterface
     */
    protected $serverParams = null;

    /**
     * @var \Zend\Stdlib\ParametersInterface
     */
    protected $envParams = null;

    /**
     * @var string|\Zend\Http\Headers
     */
    protected $headers = null;

    /**
     * A factory that produces a Request object from a well-formed Http Request string
     *
     * @param string $string
     * @return \Zend\Http\Request
     */
    public static function fromString($string)
    {
        $request = new static();

        $lines = explode("\r\n", $string);

        // first line must be Method/Uri/Version string
        $matches = null;
        $methods = implode('|', array(
            self::METHOD_OPTIONS, self::METHOD_GET, self::METHOD_HEAD, self::METHOD_POST,
            self::METHOD_PUT, self::METHOD_DELETE, self::METHOD_TRACE, self::METHOD_CONNECT,
            self::METHOD_PATCH
        ));
        $regex = '^(?P<method>' . $methods . ')\s(?P<uri>[^ ]*)(?:\sHTTP\/(?P<version>\d+\.\d+)){0,1}';
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
            $request->setContent(implode("\r\n", $rawBody));
        }

        return $request;
    }

    /**
     * Set the method for this request
     *
     * @param string $method
     * @return Request
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);
        if (!defined('static::METHOD_'.$method)) {
            throw new Exception\InvalidArgumentException('Invalid HTTP method passed');
        }
        $this->method = $method;
        return $this;
    }

    /**
     * Return the method for this request
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the URI/URL for this request, this can be a string or an instance of Zend\Uri\Http
     *
     * @throws Exception\InvalidArgumentException
     * @param string|HttpUri $uri
     * @return Request
     */
    public function setUri($uri)
    {
        if (is_string($uri)) {
            try {
                $uri = new HttpUri($uri);
            } catch (ExceptionUri\InvalidUriPartException $e) {
                throw new Exception\InvalidArgumentException(
                        sprintf('Invalid URI passed as string (%s)', (string) $uri),
                        $e->getCode(),
                        $e
                );
            }
        } elseif (!($uri instanceof HttpUri)) {
            throw new Exception\InvalidArgumentException('URI must be an instance of Zend\Uri\Http or a string');
        }
        $this->uri = $uri;

        return $this;
    }

    /**
     * Return the URI for this request object
     *
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
     * Return the URI for this request object as an instance of Zend\Uri\Http
     *
     * @return HttpUri
     */
    public function uri()
    {
        if ($this->uri === null || is_string($this->uri)) {
            $this->uri = new HttpUri($this->uri);
        }
        return $this->uri;
    }

    /**
     * Set the HTTP version for this object, one of 1.0 or 1.1 (Request::VERSION_10, Request::VERSION_11)
     *
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
     * Return the HTTP version for this request
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Provide an alternate Parameter Container implementation for query parameters in this object, (this is NOT the
     * primary API for value setting, for that see query())
     *
     * @param \Zend\Stdlib\ParametersInterface $query
     * @return Request
     */
    public function setQuery(ParametersInterface $query)
    {
        $this->queryParams = $query;
        return $this;
    }

    /**
     * Return the parameter container responsible for query parameters
     *
     * @return \Zend\Stdlib\ParametersInterface
     */
    public function query()
    {
        if ($this->queryParams === null) {
            $this->queryParams = new Parameters();
        }

        return $this->queryParams;
    }

    /**
     * Provide an alternate Parameter Container implementation for post parameters in this object, (this is NOT the
     * primary API for value setting, for that see post())
     *
     * @param \Zend\Stdlib\ParametersInterface $post
     * @return Request
     */
    public function setPost(ParametersInterface $post)
    {
        $this->postParams = $post;
        return $this;
    }

    /**
     * Return the parameter container responsible for post parameters
     *
     * @return \Zend\Stdlib\ParametersInterface
     */
    public function post()
    {
        if ($this->postParams === null) {
            $this->postParams = new Parameters();
        }

        return $this->postParams;
    }

    /**
     * Return the Cookie header, this is the same as calling $request->headers()->get('Cookie');
     *
     * @convenience $request->headers()->get('Cookie');
     * @return Header\Cookie
     */
    public function cookie()
    {
        return $this->headers()->get('Cookie');
    }

    /**
     * Provide an alternate Parameter Container implementation for file parameters in this object, (this is NOT the
     * primary API for value setting, for that see file())
     *
     * @param \Zend\Stdlib\ParametersInterface $files
     * @return Request
     */
    public function setFile(ParametersInterface $files)
    {
        $this->fileParams = $files;
        return $this;
    }

    /**
     * Return the parameter container responsible for file parameters
     *
     * @return ParametersInterface
     */
    public function file()
    {
        if ($this->fileParams === null) {
            $this->fileParams = new Parameters();
        }

        return $this->fileParams;
    }

    /**
     * Provide an alternate Parameter Container implementation for server parameters in this object, (this is NOT the
     * primary API for value setting, for that see server())
     *
     * @param \Zend\Stdlib\ParametersInterface $server
     * @return Request
     */
    public function setServer(ParametersInterface $server)
    {
        $this->serverParams = $server;
        return $this;
    }

    /**
     * Return the parameter container responsible for server parameters
     *
     * @see http://www.faqs.org/rfcs/rfc3875.html
     * @return \Zend\Stdlib\ParametersInterface
     */
    public function server()
    {
        if ($this->serverParams === null) {
            $this->serverParams = new Parameters();
        }

        return $this->serverParams;
    }

    /**
     * Provide an alternate Parameter Container implementation for env parameters in this object, (this is NOT the
     * primary API for value setting, for that see env())
     *
     * @param \Zend\Stdlib\ParametersInterface $env
     * @return \Zend\Http\Request
     */
    public function setEnv(ParametersInterface $env)
    {
        $this->envParams = $env;
        return $this;
    }

    /**
     * Return the parameter container responsible for env parameters
     *
     * @return \Zend\Stdlib\ParametersInterface
     */
    public function env()
    {
        if ($this->envParams === null) {
            $this->envParams = new Parameters();
        }

        return $this->envParams;
    }

    /**
     * Provide an alternate Parameter Container implementation for headers in this object, (this is NOT the
     * primary API for value setting, for that see headers())
     *
     * @param \Zend\Http\Headers $headers
     * @return \Zend\Http\Request
     */
    public function setHeaders(Headers $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Return the header container responsible for headers
     *
     * @return \Zend\Http\Headers
     */
    public function headers()
    {
        if ($this->headers === null || is_string($this->headers)) {
            // this is only here for fromString lazy loading
            $this->headers = (is_string($this->headers)) ? Headers::fromString($this->headers) : new Headers();
        }

        return $this->headers;
    }

    /**
     * Is this an OPTIONS method request?
     *
     * @return bool
     */
    public function isOptions()
    {
        return ($this->method === self::METHOD_OPTIONS);
    }

    /**
     * Is this a GET method request?
     *
     * @return bool
     */
    public function isGet()
    {
        return ($this->method === self::METHOD_GET);
    }

    /**
     * Is this a HEAD method request?
     *
     * @return bool
     */
    public function isHead()
    {
        return ($this->method === self::METHOD_HEAD);
    }

    /**
     * Is this a POST method request?
     *
     * @return bool
     */
    public function isPost()
    {
        return ($this->method === self::METHOD_POST);
    }

    /**
     * Is this a PUT method request?
     *
     * @return bool
     */
    public function isPut()
    {
        return ($this->method === self::METHOD_PUT);
    }

    /**
     * Is this a DELETE method request?
     *
     * @return bool
     */
    public function isDelete()
    {
        return ($this->method === self::METHOD_DELETE);
    }

    /**
     * Is this a TRACE method request?
     *
     * @return bool
     */
    public function isTrace()
    {
        return ($this->method === self::METHOD_TRACE);
    }

    /**
     * Is this a CONNECT method request?
     *
     * @return bool
     */
    public function isConnect()
    {
        return ($this->method === self::METHOD_CONNECT);
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        $header = $this->headers()->get('X_REQUESTED_WITH');
        return false !== $header && $header->getFieldValue() == 'XMLHttpRequest';
    }

    /**
     * Is this a Flash request?
     *
     * @return boolean
     */
    public function isFlashRequest()
    {
        $header = $this->headers()->get('USER_AGENT');
        return false !== $header && stristr($header->getFieldValue(), ' flash');

    }

    /*
     * Is this a PATCH method request?
     *
     * @return bool
     */
    public function isPatch()
    {
        return ($this->method === self::METHOD_PATCH);
    }

    /**
     * Return the formatted request line (first line) for this http request
     *
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
