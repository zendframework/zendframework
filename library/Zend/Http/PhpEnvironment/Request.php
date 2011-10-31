<?php

namespace Zend\Http\PhpEnvironment;

use Zend\Http\Request as HttpRequest,
    Zend\Uri\Http as HttpUri,
    Zend\Http\Header\Cookie,
    Zend\Stdlib\Parameters,
    Zend\Stdlib\ParametersDescription;

class Request extends HttpRequest
{
    public function __construct()
    {
        $this->setEnv(new Parameters($_ENV));
        $this->setPost(new Parameters($_POST));
        $this->setQuery(new Parameters($_GET));
        $this->setServer(new Parameters($_SERVER));
        if ($_COOKIE) {
            $this->setCookies($_COOKIE);
        }
        if ($_FILES) {
            $this->setFile(new Parameters($_FILES));
        }
    }

    public function setCookies($cookie)
    {
        $this->headers()->addHeader(new Cookie((array) $cookie));
        return $this;
    }

    /**
     * Provide an alternate Parameter Container implementation for server parameters in this object, (this is NOT the
     * primary API for value setting, for that see server())
     *
     * @param \Zend\Stdlib\ParametersDescription $server
     * @return Request
     */
    public function setServer(ParametersDescription $server)
    {
        $this->serverParams = $server;

        $this->headers()->addHeaders($this->serverToHeaders($this->serverParams));
        if (isset($this->serverParams['REQUEST_METHOD'])) {
            $this->setMethod($this->serverParams['REQUEST_METHOD']);
        }

        if (isset($this->serverParams['SERVER_PROTOCOL']) 
            && strpos($this->serverParams['SERVER_PROTOCOL'], '1.0') !== false) {
            $this->setVersion('1.0');
        }

        $uri = new HttpUri();

        if (isset($this->serverParams['HTTPS']) && $this->serverParams['HTTPS'] == 'on') { 
            $uri->setScheme('https');
        } else {
            $uri->setScheme('http');
        }

        if (isset($this->serverParams['QUERY_STRING'])) {
            $uri->setQuery($this->serverParams['QUERY_STRING']);
        }

        if (isset($this->serverParams['REQUEST_URI'])) {
            $uri->setPath(substr($this->serverParams['REQUEST_URI'], 0, strpos($this->serverParams['REQUEST_URI'], '?') ?: strlen($this->serverParams['REQUEST_URI'])));
        }

        if ($this->headers()->get('host')) {
            $uri->setHost($this->headers()->get('host')->getFieldValue());
        } elseif (isset($this->serverParams['SERVER_NAME'])) {
            $uri->setHost($this->serverParams['SERVER_NAME']);
        }

        $this->setUri($uri);

        return $this;
    }

    protected function serverToHeaders($server)
    {
        $headers = array();
        foreach ($server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0 && $value) {
                $header = substr($key, 5);
                $headers[substr($key, 5)] = $value;
            } elseif (in_array($key, array('CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE')) && $value) {
                $header = $key;
            } else {
                continue;
            }
            $headers[$header] = $server[$key];
        }
        return $headers;
    }
}
