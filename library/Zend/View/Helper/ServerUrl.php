<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper;

/**
 * Helper for returning the current server URL (optionally with request URI)
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class ServerUrl extends AbstractHelper
{
    /**
     * Scheme
     *
     * @var string
     */
    protected $scheme;

    /**
     * Host (including port)
     *
     * @var string
     */
    protected $host;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        switch (true) {
            case (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] === true)):
            case (isset($_SERVER['HTTP_SCHEME']) && ($_SERVER['HTTP_SCHEME'] == 'https')):
            case (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 443)):
                $scheme = 'https';
                break;
            default:
            $scheme = 'http';
        }
        $this->setScheme($scheme);

        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
            if (strpos($host, ',') !== false) {
                $hosts = explode(',', $host);
                $host = trim(array_pop($hosts));
            }
            $this->setHost($host);
        } elseif (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
            $this->setHost($_SERVER['HTTP_HOST']);
        } elseif (isset($_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'])) {
            $name = $_SERVER['SERVER_NAME'];
            $port = $_SERVER['SERVER_PORT'];

            if (($scheme == 'http' && $port == 80) ||
                ($scheme == 'https' && $port == 443)) {
                $this->setHost($name);
            } else {
                $this->setHost($name . ':' . $port);
            }
        }
    }

    /**
     * View helper entry point:
     * Returns the current host's URL like http://site.com
     *
     * @param  string|boolean $requestUri  [optional] if true, the request URI
     *                                     found in $_SERVER will be appended
     *                                     as a path. If a string is given, it
     *                                     will be appended as a path. Default
     *                                     is to not append any path.
     * @return string                      server url
     */
    public function __invoke($requestUri = null)
    {
        if ($requestUri === true) {
            $path = $_SERVER['REQUEST_URI'];
        } elseif (is_string($requestUri)) {
            $path = $requestUri;
        } else {
            $path = '';
        }

        return $this->getScheme() . '://' . $this->getHost() . $path;
    }

    /**
     * Returns host
     *
     * @return string  host
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets host
     *
     * @param  string $host                new host
     * @return \Zend\View\Helper\ServerUrl  fluent interface, returns self
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Returns scheme (typically http or https)
     *
     * @return string  scheme (typically http or https)
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Sets scheme (typically http or https)
     *
     * @param  string $scheme              new scheme (typically http or https)
     * @return \Zend\View\Helper\ServerUrl  fluent interface, returns self
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }
}
