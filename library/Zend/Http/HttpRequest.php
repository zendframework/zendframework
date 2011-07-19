<?php

namespace Zend\Http;

use Zend\Stdlib\RequestDescription,
    Zend\Stdlib\Parameters;

interface HttpRequest extends RequestDescription
{
    /* mutators for various superglobals */
    public function setQuery(Parameters $query);
    public function setPost(Parameters $post);
    public function setCookies(Parameters $cookies);
    public function setFiles(Parameters $files); // Maybe separate component for Files?
    public function setServer(Parameters $server);
    public function setEnv(Parameters $env);
    public function setHeaders(HttpRequestHeaders $headers);
    public function setRawBody($string);

    /* accessors for various superglobals */
    public function query($name = null, $default = null);
    public function post($name = null, $default = null);
    public function cookie($name = null, $default = null);
    public function file($name = null);
    public function server($name = null, $default = null);
    public function env($name = null, $default = null);
    public function headers($name = null);

    /* URI decomposition */
    public function getRequestUri();
    public function getScheme();
    public function getHttpHost();
    public function getPort();
    public function getPathInfo();

    /* base path/url/script name info */
    public function getBasePath();
    public function getBaseUrl();
    public function getScriptName();

    /* Capabilities 
     *
     * These may be pushed into an additional interface.
     */
    public function getMethod();
    public function setMethod($method);
    public function getETags();
    public function getPreferredLanguage(array $locales = null);
    public function getLanguages();
    public function getCharsets();
    public function getAcceptableContentTypes();
    public function isNoCache();
    public function isFlashRequest();
    public function isSecure();
    public function isXmlHttpRequest();

    /* potential method tests */
    public function isDelete();
    public function isGet();
    public function isHead();
    public function isOptions();
    public function isPost();
    public function isPut();

    /* creational capabilities */
    public function getUri(); // returns full URI string: scheme, host, port, base URL, path info, and query string
    public static function create($uri, $method = 'get' /** .. more args */);
    public function __clone(); // not sure if this needs to be in interface
}
