<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace Zend\Http\PhpEnvironment;

use Zend\Http\Request as HttpRequest;
use Zend\Uri\Http as HttpUri;
use Zend\Http\Header\Cookie;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\ParametersInterface;

/**
 * HTTP Request for current PHP environment
 *
 * @category   Zend
 * @package    Zend_Http
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
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

    /**
     * Construct
     * Instantiates request.
     */
    public function __construct()
    {
        $this->setEnv(new Parameters($_ENV));
        $this->setPost(new Parameters($_POST));
        $this->setQuery(new Parameters($_GET));
        $this->setServer(new Parameters($_SERVER));
        if ($_COOKIE) {
            $this->setCookies(new Parameters($_COOKIE));
        }

        if ($_FILES) {
            $this->setFile(new Parameters($_FILES));
        }

        $requestBody = file_get_contents('php://input');
        if(strlen($requestBody) > 0){
            $this->setContent($requestBody);
        }
    }

    /**
     * Set cookies
     *
     * Instantiate and set cookies.
     *
     * @param $cookie
     * @return Request
     */
    public function setCookies($cookie)
    {
        $this->getHeaders()->addHeader(new Cookie((array) $cookie));
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
            $this->setRequestUri($this->detectRequestUri());
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
            $this->setBaseUrl($this->detectBaseUrl());
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
            $this->setBasePath($this->detectBasePath());
        }

        return $this->basePath;
    }

    /**
     * Provide an alternate Parameter Container implementation for server parameters in this object, (this is NOT the
     * primary API for value setting, for that see server())
     *
     * @param ParametersInterface $server
     * @return Request
     */
    public function setServer(ParametersInterface $server)
    {
        $this->serverParams = $server;

        // This seems to be the only way to get the Authorization header on Apache
        if (function_exists('apache_request_headers')) {
            $apacheRequestHeaders = apache_request_headers();
            if (isset($apacheRequestHeaders['Authorization'])) {
                if (!$this->serverParams->get('HTTP_AUTHORIZATION')) {
                    $this->serverParams->set('HTTP_AUTHORIZATION', $apacheRequestHeaders['Authorization']);
                }
            }
        }

        $this->getHeaders()->addHeaders($this->serverToHeaders($this->serverParams));

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

        if ($this->getHeaders()->get('host')) {
            //TODO handle IPv6 with port
            if (preg_match('|^([^:]+):([^:]+)$|', $this->getHeaders()->get('host')->getFieldValue(), $match)) {
                $uri->setHost($match[1]);
                $uri->setPort($match[2]);
            } else {
                $uri->setHost($this->getHeaders()->get('host')->getFieldValue());
            }
        } elseif (isset($this->serverParams['SERVER_NAME'])) {
            $uri->setHost($this->serverParams['SERVER_NAME']);
            if (isset($this->serverParams['SERVER_PORT'])) {
                $uri->setPort($this->serverParams['SERVER_PORT']);
            }
        }

        $requestUri = $this->getRequestUri();
        $uri->setPath(substr($requestUri, 0, strpos($requestUri, '?') ?: strlen($requestUri)));

        return $this;
    }

    /**
     * Grab headers from array or Traversable
     *
     * @param array|\Traversable $server
     * @return array
     */
    protected function serverToHeaders($server)
    {
        $headers = array();

        foreach ($server as $key => $value) {
            if ($value && strpos($key, 'HTTP_') === 0) {
                if (strpos($key, 'HTTP_COOKIE') === 0) {
                    // Cookies are handled using the $_COOKIE superglobal
                    continue;
                }
                $name = strtr(substr($key, 5), '_', ' ');
                $name = strtr(ucwords(strtolower($name)), ' ', '-');
            } elseif ($value && strpos($key, 'CONTENT_') === 0) {
                $name = substr($key, 8);
                $name = 'Content-' . (($name == 'MD5') ? $name : ucfirst(strtolower($name)));
            } else {
                continue;
            }

            $headers[$name] = $value;
        }

        return $headers;
    }

    /**
     * Detect the base URI for the request
     *
     * Looks at a variety of criteria in order to attempt to autodetect a base
     * URI, including rewrite URIs, proxy URIs, etc.
     *
     * @return string
     */
    protected function detectRequestUri()
    {
        $requestUri = null;

        // Check this first so IIS will catch.
        $httpXRewriteUrl = $this->getServer()->get('HTTP_X_REWRITE_URL');
        if ($httpXRewriteUrl !== null) {
            $requestUri = $httpXRewriteUrl;
        }

        // Check for IIS 7.0 or later with ISAPI_Rewrite
        $httpXOriginalUrl = $this->getServer()->get('HTTP_X_ORIGINAL_URL');
        if ($httpXOriginalUrl !== null) {
            $requestUri = $httpXOriginalUrl;
        }

        // IIS7 with URL Rewrite: make sure we get the unencoded url
        // (double slash problem).
        $iisUrlRewritten = $this->getServer()->get('IIS_WasUrlRewritten');
        $unencodedUrl    = $this->getServer()->get('UNENCODED_URL', '');
        if ('1' == $iisUrlRewritten && '' !== $unencodedUrl) {
            return $unencodedUrl;
        }

        // HTTP proxy requests setup request URI with scheme and host
        // [and port] + the URL path, only use URL path.
        if (!$httpXRewriteUrl) {
            $requestUri = $this->getServer()->get('REQUEST_URI');
        }
        if ($requestUri !== null) {
            $schemeAndHttpHost = $this->getUri()->getScheme() . '://' . $this->getUri()->getHost();

            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
            return $requestUri;
        }

        // IIS 5.0, PHP as CGI.
        $origPathInfo = $this->getServer()->get('ORIG_PATH_INFO');
        if ($origPathInfo !== null) {
            $queryString = $this->getServer()->get('QUERY_STRING', '');
            if ($queryString !== '') {
                $origPathInfo .= '?' . $queryString;
            }
            return $origPathInfo;
        }

        return '/';
    }

    /**
     * Auto-detect the base path from the request environment
     *
     * Uses a variety of criteria in order to detect the base URL of the request
     * (i.e., anything additional to the document root).
     *
     * The base URL includes the schema, host, and port, in addition to the path.
     *
     * @return string
     */
    protected function detectBaseUrl()
    {
        $baseUrl        = '';
        $filename       = $this->getServer()->get('SCRIPT_FILENAME', '');
        $scriptName     = $this->getServer()->get('SCRIPT_NAME');
        $phpSelf        = $this->getServer()->get('PHP_SELF');
        $origScriptName = $this->getServer()->get('ORIG_SCRIPT_NAME');

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

            $basename = basename($filename);
            $path = ($phpSelf ? trim($phpSelf, '/') : '');
            $baseUrl = '/'. substr($path, 0, strpos($path, $basename)) . $basename;
        }

        // Does the base URL have anything in common with the request URI?
        $requestUri = $this->getRequestUri();

        // Full base URL matches.
        if (0 === strpos($requestUri, $baseUrl)) {
            return $baseUrl;
        }

        // Directory portion of base path matches.
        $baseDir = str_replace('\\', '/', dirname($baseUrl));
        if (0 === strpos($requestUri, $baseDir)) {
            return $baseDir;
        }

        $truncatedRequestUri = $requestUri;

        if (false !== ($pos = strpos($requestUri, '?'))) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);

        // No match whatsoever
        if (empty($basename) || false === strpos($truncatedRequestUri, $basename)) {
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of the base path. $pos !== 0 makes sure it is not matching a
        // value from PATH_INFO or QUERY_STRING.
        if (strlen($requestUri) >= strlen($baseUrl)
            && (false !== ($pos = strpos($requestUri, $baseUrl)) && $pos !== 0)
        ) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return $baseUrl;
    }

    /**
     * Autodetect the base path of the request
     *
     * Uses several criteria to determine the base path of the request.
     *
     * @return string
     */
    protected function detectBasePath()
    {
        $filename = basename($this->getServer()->get('SCRIPT_FILENAME', ''));
        $baseUrl  = $this->getBaseUrl();

        // Empty base url detected
        if ($baseUrl === '') {
            return '';
        }

        // basename() matches the script filename; return the directory
        if (basename($baseUrl) === $filename) {
            return str_replace('\\', '/', dirname($baseUrl));
        }

        // Base path is identical to base URL
        return $baseUrl;
    }
}
