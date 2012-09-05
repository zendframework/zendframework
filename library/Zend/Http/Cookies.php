<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace Zend\Http;

use Zend\Http\Header\Cookie;
use Zend\Http\Response;
use Zend\Uri;

/**
 * A Zend_Http_CookieJar object is designed to contain and maintain HTTP cookies, and should
 * be used along with Zend_Http_Client in order to manage cookies across HTTP requests and
 * responses.
 *
 * The class contains an array of Zend\Http\Header\Cookie objects. Cookies can be added
 * automatically from a request or manually. Then, the Cookies class can find and return the
 * cookies needed for a specific HTTP request.
 *
 * A special parameter can be passed to all methods of this class that return cookies: Cookies
 * can be returned either in their native form (as Zend\Http\Header\Cookie objects) or as strings -
 * the later is suitable for sending as the value of the "Cookie" header in an HTTP request.
 * You can also choose, when returning more than one cookie, whether to get an array of strings
 * (by passing Zend\Http\Client\Cookies::COOKIE_STRING_ARRAY) or one unified string for all cookies
 * (by passing Zend\Http\Client\Cookies::COOKIE_STRING_CONCAT).
 *
 * @link       http://wp.netscape.com/newsref/std/cookie_spec.html for some specs.
 *
 * @category   Zend
 * @package    Zend\Http\Client
 */
class Cookies extends Headers
{
    /**
     * @var Headers
     */
    protected $headers = null;

    /**
     * @var $_rawCookies
     */
    protected $rawCookies;

    /**
     * @static
     * @throws Exception\RuntimeException
     * @param $string
     * @return void
     */
    public static function fromString($string)
    {
        throw new Exception\RuntimeException(
            __CLASS__ . '::' . __FUNCTION__ . ' should not be used as a factory, use '
            . __NAMESPACE__ . '\Headers::fromtString() instead.'
        );
    }

    public function __construct(Headers $headers, $context = self::CONTEXT_REQUEST)
    {
        $this->headers = $headers;
        parent::__construct();
    }

    /**
     * Add a cookie to the class. Cookie should be passed either as a Zend\Http\Header\Cookie object
     * or as a string - in which case an object is created from the string.
     *
     * @param Cookie|string $cookie
     * @param Uri\Uri|string    $ref_uri Optional reference URI (for domain, path, secure)
     */
    public function addCookie(Cookie $cookie, $ref_uri = null)
    {
        if (is_string($cookie)) {
            $cookie = Cookie::fromString($cookie, $ref_uri);
        }

        if ($cookie instanceof Cookie) {
            $domain = $cookie->getDomain();
            $path   = $cookie->getPath();
            if (!isset($this->cookies[$domain])) {
                $this->cookies[$domain] = array();
            }
            if (!isset($this->cookies[$domain][$path])) {
                $this->cookies[$domain][$path] = array();
            }
            $this->cookies[$domain][$path][$cookie->getName()] = $cookie;
            $this->rawCookies[] = $cookie;
        } else {
            throw new Exception\InvalidArgumentException('Supplient argument is not a valid cookie string or object');
        }
    }

    /**
     * Parse an HTTP response, adding all the cookies set in that response
     *
     * @param Response $response
     * @param Uri\Uri|string $ref_uri Requested URI
     */
    public function addCookiesFromResponse(Response $response, $ref_uri)
    {
        $cookie_hdrs = $response->getHeaders()->get('Set-Cookie');

        if (is_array($cookie_hdrs)) {
            foreach ($cookie_hdrs as $cookie) {
                $this->addCookie($cookie, $ref_uri);
            }
        } elseif (is_string($cookie_hdrs)) {
            $this->addCookie($cookie_hdrs, $ref_uri);
        }
    }

    /**
     * Get all cookies in the cookie jar as an array
     *
     * @param int $ret_as Whether to return cookies as objects of \Zend\Http\Header\Cookie or as strings
     * @return array|string
     */
    public function getAllCookies($ret_as = self::COOKIE_OBJECT)
    {
        $cookies = $this->_flattenCookiesArray($this->cookies, $ret_as);
        return $cookies;
    }

    /**
     * Return an array of all cookies matching a specific request according to the request URI,
     * whether session cookies should be sent or not, and the time to consider as "now" when
     * checking cookie expiry time.
     *
     * @param string|Uri\Uri $uri URI to check against (secure, domain, path)
     * @param boolean $matchSessionCookies Whether to send session cookies
     * @param int $ret_as Whether to return cookies as objects of \Zend\Http\Header\Cookie or as strings
     * @param int $now Override the current time when checking for expiry time
     * @return array|string
     */
    public function getMatchingCookies($uri, $matchSessionCookies = true,
        $ret_as = self::COOKIE_OBJECT, $now = null)
    {
        if (is_string($uri)) {
            $uri = Uri\UriFactory::factory($uri, 'http');
        } elseif (!$uri instanceof Uri\Uri) {
            throw new Exception\InvalidArgumentException("Invalid URI string or object passed");
        }

        $host = $uri->getHost();
        if (empty($host)) {
            throw new Exception\InvalidArgumentException('Invalid URI specified; does not contain a host');
        }

        // First, reduce the array of cookies to only those matching domain and path
        $cookies = $this->_matchDomain($host);
        $cookies = $this->_matchPath($cookies, $uri->getPath());
        $cookies = $this->_flattenCookiesArray($cookies, self::COOKIE_OBJECT);

        // Next, run Cookie->match on all cookies to check secure, time and session matching
        $ret = array();
        foreach ($cookies as $cookie)
            if ($cookie->match($uri, $matchSessionCookies, $now))
                $ret[] = $cookie;

        // Now, use self::_flattenCookiesArray again - only to convert to the return format ;)
        $ret = $this->_flattenCookiesArray($ret, $ret_as);

        return $ret;
    }

    /**
     * Get a specific cookie according to a URI and name
     *
     * @param Uri\Uri|string $uri The uri (domain and path) to match
     * @param string $cookie_name The cookie's name
     * @param int $ret_as Whether to return cookies as objects of \Zend\Http\Header\Cookie or as strings
     * @return Cookie|string
     */
    public function getCookie($uri, $cookie_name, $ret_as = self::COOKIE_OBJECT)
    {
        if (is_string($uri)) {
            $uri = Uri\UriFactory::factory($uri, 'http');
        } elseif (!$uri instanceof Uri\Uri) {
            throw new Exception\InvalidArgumentException('Invalid URI specified');
        }

        $host = $uri->getHost();
        if (empty($host)) {
            throw new Exception\InvalidArgumentException('Invalid URI specified; host missing');
        }

        // Get correct cookie path
        $path = $uri->getPath();
        $path = substr($path, 0, strrpos($path, '/'));
        if (! $path) $path = '/';

        if (isset($this->cookies[$uri->getHost()][$path][$cookie_name])) {
            $cookie = $this->cookies[$uri->getHost()][$path][$cookie_name];

            switch ($ret_as) {
                case self::COOKIE_OBJECT:
                    return $cookie;
                    break;

                case self::COOKIE_STRING_ARRAY:
                case self::COOKIE_STRING_CONCAT:
                    return $cookie->__toString();
                    break;

                default:
                    throw new Exception\InvalidArgumentException("Invalid value passed for \$ret_as: {$ret_as}");
                    break;
            }
        } else {
            return false;
        }
    }

    /**
     * Helper function to recursively flatten an array. Should be used when exporting the
     * cookies array (or parts of it)
     *
     * @param \Zend\Http\Header\Cookie|array $ptr
     * @param int $ret_as What value to return
     * @return array|string
     */
    protected function _flattenCookiesArray($ptr, $ret_as = self::COOKIE_OBJECT)
    {
        if (is_array($ptr)) {
            $ret = ($ret_as == self::COOKIE_STRING_CONCAT ? '' : array());
            foreach ($ptr as $item) {
                if ($ret_as == self::COOKIE_STRING_CONCAT) {
                    $ret .= $this->_flattenCookiesArray($item, $ret_as);
                } else {
                    $ret = array_merge($ret, $this->_flattenCookiesArray($item, $ret_as));
                }
            }
            return $ret;
        } elseif ($ptr instanceof Cookie) {
            switch ($ret_as) {
                case self::COOKIE_STRING_ARRAY:
                    return array($ptr->__toString());
                    break;

                case self::COOKIE_STRING_CONCAT:
                    return $ptr->__toString();
                    break;

                case self::COOKIE_OBJECT:
                default:
                    return array($ptr);
                    break;
            }
        }

        return null;
    }

    /**
     * Return a subset of the cookies array matching a specific domain
     *
     * @param string $domain
     * @return array
     */
    protected function _matchDomain($domain)
    {
        $ret = array();

        foreach (array_keys($this->cookies) as $cdom) {
            if (Cookie::matchCookieDomain($cdom, $domain)) {
                $ret[$cdom] = $this->cookies[$cdom];
            }
        }

        return $ret;
    }

    /**
     * Return a subset of a domain-matching cookies that also match a specified path
     *
     * @param array $dom_array
     * @param string $path
     * @return array
     */
    protected function _matchPath($domains, $path)
    {
        $ret = array();

        foreach ($domains as $dom => $paths_array) {
            foreach (array_keys($paths_array) as $cpath) {
                if (Cookie::matchCookiePath($cpath, $path)) {
                    if (! isset($ret[$dom])) {
                        $ret[$dom] = array();
                    }

                    $ret[$dom][$cpath] = $paths_array[$cpath];
                }
            }
        }

        return $ret;
    }

    /**
     * Create a new Cookies object and automatically load into it all the
     * cookies set in an Http_Response object. If $uri is set, it will be
     * considered as the requested URI for setting default domain and path
     * of the cookie.
     *
     * @param Response $response HTTP Response object
     * @param Uri\Uri|string $uri The requested URI
     * @return Cookies
     * @todo Add the $uri functionality.
     */
    public static function fromResponse(Response $response, $ref_uri)
    {
        $jar = new self();
        $jar->addCookiesFromResponse($response, $ref_uri);
        return $jar;
    }

    /**
     * Tells if the array of cookies is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return count($this) == 0;
    }

    /**
     * Empties the cookieJar of any cookie
     *
     * @return Cookies
     */
    public function reset()
    {
        $this->cookies = $this->rawCookies = array();
        return $this;
    }

}
