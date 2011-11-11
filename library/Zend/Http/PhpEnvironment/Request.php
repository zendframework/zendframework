<?php

namespace Zend\Http\PhpEnvironment;

use Zend\Http\Request as HttpRequest,
    Zend\Uri\Http as HttpUri,
    Zend\Http\Header\Cookie,
    Zend\Stdlib\Parameters,
    Zend\Stdlib\ParametersDescription;

class Request extends HttpRequest
{
    /**
     * Base URL of the application.
     * 
     * @var string
     */
    protected $baseUrl;
    
    /**
     * Base Path of the application.
     *
     * @var string
     */
    protected $basePath;
    
    /**
     * Actual request URI, independent of the platform.
     * 
     * @var string
     */
    protected $requestUri;

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
     * Set the request URI.
     *
     * @param  string $requestUri
     * @return self
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
        return $this;
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function getRequestUri()
    {
        if ($this->requestUri === null) {
            if (($httpXRewriteUrl = $this->server()->get('HTTP_X_REWRITE_URL')) !== null) {
                 // Check this first so IIS will catch.
                $requestUri = $httpXRewriteUrl;
            } elseif (
                $this->server()->get('IIS_WasUrlRewritten') !== null
                && $this->server()->get('IIS_WasUrlRewritten') == '1'
                && ($unencodedUrl = $this->server()->get('UNENCODED_URL', '')) !== ''
            ) {
                // IIS7 with URL Rewrite: make sure we get the unencoded url
                // (double slash problem).
                $requestUri = $unencodedUrl;
            } elseif (($requestUri = $this->server()->get('REQUEST_URI')) !== null) {
                // HTTP proxy requests setup request URI with scheme and host
                // [and port] + the URL path, only use URL path.
                $schemeAndHttpHost = $this->uri()->getScheme() . '://' . $this->uri()->getHost();
                
                if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                    $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
                }
            } elseif (($origPathInfo = $this->server()->get('ORIG_PATH_INFO')) !== null) {
                 // IIS 5.0, PHP as CGI.
                $requestUri = $origPathInfo;
                
                if (($queryString = $this->server()->get('QUERY_STRING', '')) !== '') {
                    $requestUri .= '?' . $queryString;
                }
            } else {
                $requestUri = '/';
            }
            
            $this->requestUri = $requestUri;
        }

        return $this->requestUri;
    }
    
    /**
     * Set the base URL.
     *
     * @param  string $baseUrl
     * @return self
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if ($this->baseUrl === null) {
            $filename       = $this->server()->get('SCRIPT_FILENAME', '');
            $scriptName     = $this->server()->get('SCRIPT_NAME');
            $phpSelf        = $this->server()->get('PHP_SELF');
            $origScriptName = $this->server()->get('ORIG_SCRIPT_NAME');

            if ($scriptName !== null && basename($scriptName) === $filename) {
                $baseUrl = $scriptName;
            } elseif ($phpSelf !== null && basename($phpSelf) === $filename) {
                $baseUrl = $phpSelf;
            } elseif ($origScriptName !== null && basename($origScriptName) === $filename) {
                // 1and1 shared hosting compatibility.
                $baseUrl = $origScriptName;
            } else {
                // Backtrack up the SCRIPT_FILENAME to find the portion
                // matching PHP_SELF.
                $path     = $phpSelf ?: '';
                $segments = array_reverse(explode('/', trim($filename, '/')));
                $index    = 0;
                $last     = count($segments);
                $baseUrl  = '';

                do {
                    $segment  = $segments[$index];
                    $baseUrl = '/' . $segment . $baseUrl;
                    $index++;
                } while ($last > $index && false !== ($pos = strpos($path, $baseUrl)) && 0 !== $pos);
            }

            // Does the base URL have anything in common with the request URI?
            $requestUri = $this->getRequestUri();

            if (0 === strpos($requestUri, $baseUrl)) {
                // Full base URL matches.
                return ($this->baseUrl = $baseUrl);
            }

            if (0 === strpos($requestUri, dirname($baseUrl))) {
                // Directory portion of base path matches.
                return ($this->baseUrl = rtrim(dirname($baseUrl), '/'));
            }

            $truncatedRequestUri = $requestUri;

            if (false !== ($pos = strpos($requestUri, '?'))) {
                $truncatedRequestUri = substr($requestUri, 0, $pos);
            }

            $basename = basename($baseUrl);

            if (empty($basename) || false === strpos($truncatedRequestUri, $basename)) {
                // No match whatsoever; set it blank.
                return ($this->baseUrl = '');
            }

            // If using mod_rewrite or ISAPI_Rewrite strip the script filename
            // out of the base path. $pos !== 0 makes sure it is not matching a
            // value from PATH_INFO or QUERY_STRING.
            if (strlen($requestUri) >= strlen($baseUrl)
                && (false !== ($pos = strpos($requestUri, $baseUrl)) && $pos !== 0))
            {
                $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
            }

            return ($this->baseUrl = rtrim($baseUrl, '/'));
        }
        
        return $this->baseUrl;
    }
    
    /**
     * Set the base path.
     * 
     * @param  string $basePath
     * @return self
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '/');
        return $this;
    }

    /**
     * Get the base path.
     *
     * @return string
     */
    public function getBasePath()
    {
        if ($this->basePath === null) {
            $filename = basename($this->server()->get('SCRIPT_FILENAME', ''));
            $baseUrl  = $this->getBaseUrl();

            if ($baseUrl === '') {
                $basePath = '';
            } elseif (basename($baseUrl) === $filename) {
                $basePath = dirname($baseUrl);
            } else {
                $basePath = $baseUrl;
            }
            
            $this->basePath = rtrim($basePath, '/');
        }
        
        return $this->basePath;
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

        $this->setUri($uri = new HttpUri());

        if (isset($this->serverParams['HTTPS']) && $this->serverParams['HTTPS'] === 'on') { 
            $uri->setScheme('https');
        } else {
            $uri->setScheme('http');
        }

        if (isset($this->serverParams['QUERY_STRING'])) {
            $uri->setQuery($this->serverParams['QUERY_STRING']);
        }

        $requestUri = $this->getRequestUri();
        $uri->setPath(substr($requestUri, 0, strpos($requestUri, '?') ?: strlen($requestUri)));

        if ($this->headers()->get('host')) {
            //TODO handle IPv6 with port
            if (preg_match('|^([^:]+):([^:]+)$|', $this->headers()->get('host')->getFieldValue(), $match)) {
                $uri->setHost($match[1]);
                $uri->setPort($match[2]);
            }
            else {
                $uri->setHost($this->headers()->get('host')->getFieldValue());
            }
        } elseif (isset($this->serverParams['SERVER_NAME'])) {
            $uri->setHost($this->serverParams['SERVER_NAME']);
            if (isset($this->serverParams['SERVER_PORT'])) {
                $uri->setPort($this->serverParams['SERVER_PORT']);
            }
        }

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
