<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace Zend\Http\Header;

use Closure;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.ietf.org/rfc/rfc2109.txt
 * @see http://www.w3.org/Protocols/rfc2109/rfc2109
 */
class SetCookie implements MultipleHeaderInterface
{

    /**
     * Cookie name
     *
     * @var string
     */
    protected $name = null;

    /**
     * Cookie value
     *
     * @var string
     */
    protected $value = null;

    /**
     * Version
     *
     * @var integer
     */
    protected $version = null;

    /**
     * Max Age
     *
     * @var integer
     */
    protected $maxAge = null;

    /**
     * Cookie expiry date
     *
     * @var int
     */
    protected $expires = null;

    /**
     * Cookie domain
     *
     * @var string
     */
    protected $domain = null;

    /**
     * Cookie path
     *
     * @var string
     */
    protected $path = null;

    /**
     * Whether the cookie is secure or not
     *
     * @var bool
     */
    protected $secure = null;

    /**
     * @var bool|null
     */
    protected $httponly = null;

    /**
     * @static
     * @throws Exception\InvalidArgumentException
     * @param  $headerLine
     * @param  bool $bypassHeaderFieldName
     * @return array|SetCookie
     */
    public static function fromString($headerLine, $bypassHeaderFieldName = false)
    {
        /* @var $setCookieProcessor Closure */
        static $setCookieProcessor = null;

        if ($setCookieProcessor === null) {
            $setCookieClass = get_called_class();
            $setCookieProcessor = function ($headerLine) use ($setCookieClass) {
                $header = new $setCookieClass;
                $keyValuePairs = preg_split('#;\s*#', $headerLine);
                foreach ($keyValuePairs as $keyValue) {
                    if (strpos($keyValue, '=')) {
                        list($headerKey, $headerValue) = preg_split('#=\s*#', $keyValue, 2);
                    } else {
                        $headerKey = $keyValue;
                        $headerValue = null;
                    }

                    // First K=V pair is always the cookie name and value
                    if ($header->getName() === NULL) {
                        $header->setName($headerKey);
                        $header->setValue(urldecode($headerValue));
                        continue;
                    }

                    // Process the remaining elements
                    switch (str_replace(array('-', '_'), '', strtolower($headerKey))) {
                        case 'expires' : $header->setExpires($headerValue); break;
                        case 'domain'  : $header->setDomain($headerValue); break;
                        case 'path'    : $header->setPath($headerValue); break;
                        case 'secure'  : $header->setSecure(true); break;
                        case 'httponly': $header->setHttponly(true); break;
                        case 'version' : $header->setVersion((int) $headerValue); break;
                        case 'maxage'  : $header->setMaxAge((int) $headerValue); break;
                        default:
                            // Intentionally omitted
                    }
                }

                return $header;
            };
        }

        list($name, $value) = explode(': ', $headerLine, 2);

        // some sites return set-cookie::value, this is to get rid of the second :
        $name = (strtolower($name) =='set-cookie:') ? 'set-cookie' : $name;

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'set-cookie') {
            throw new Exception\InvalidArgumentException('Invalid header line for Set-Cookie string: "' . $name . '"');
        }

        $multipleHeaders = preg_split('#(?<!Sun|Mon|Tue|Wed|Thu|Fri|Sat),\s*#', $value);

        if (count($multipleHeaders) <= 1) {
            return $setCookieProcessor(array_pop($multipleHeaders));
        } else {
            $headers = array();
            foreach ($multipleHeaders as $headerLine) {
                $headers[] = $setCookieProcessor($headerLine);
            }
            return $headers;
        }
    }

    /**
     * Cookie object constructor
     *
     * @todo Add validation of each one of the parameters (legal domain, etc.)
     *
     * @param string $name
     * @param string $value
     * @param int $expires
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @param string $maxAge
     * @param int $version
     * @return SetCookie
     */
    public function __construct($name = null, $value = null, $expires = null, $path = null, $domain = null, $secure = false, $httponly = false, $maxAge = null, $version = null)
    {
        $this->type = 'Cookie';

        if ($name) {
            $this->setName($name);
        }

        if ($value) {
            $this->setValue($value); // in parent
        }

        if ($version!==null) {
            $this->setVersion($version);
        }

        if ($maxAge!==null) {
            $this->setMaxAge($maxAge);
        }

        if ($domain) {
            $this->setDomain($domain);
        }

        if ($expires) {
            $this->setExpires($expires);
        }

        if ($path) {
            $this->setPath($path);
        }

        if ($secure) {
            $this->setSecure($secure);
        }

        if ($httponly) {
            $this->setHttpOnly($httponly);
        }
    }

    /**
     * @return string 'Set-Cookie'
     */
    public function getFieldName()
    {
        return 'Set-Cookie';
    }

    /**
     * @throws Exception\RuntimeException
     * @return string
     */
    public function getFieldValue()
    {
        if ($this->getName() == '') {
            throw new Exception\RuntimeException('A cookie name is required to generate a field value for this cookie');
        }

        $value = $this->getValue();
        if (strpos($value, '"')!==false) {
            $value = '"'.urlencode(str_replace('"', '', $value)).'"';
        } else {
            $value = urlencode($value);
        }
        $fieldValue = $this->getName() . '=' . $value;

        $version = $this->getVersion();
        if ($version!==null) {
            $fieldValue .= '; Version=' . $version;
        }

        $maxAge = $this->getMaxAge();
        if ($maxAge!==null) {
            $fieldValue .= '; Max-Age=' . $maxAge;
        }

        $expires = $this->getExpires();
        if ($expires) {
            $fieldValue .= '; Expires=' . $expires;
        }

        $domain = $this->getDomain();
        if ($domain) {
            $fieldValue .= '; Domain=' . $domain;
        }

        $path = $this->getPath();
        if ($path) {
            $fieldValue .= '; Path=' . $path;
        }

        if ($this->isSecure()) {
            $fieldValue .= '; Secure';
        }

        if ($this->isHttponly()) {
            $fieldValue .= '; HttpOnly';
        }

        return $fieldValue;
    }

    /**
     * @param string $name
     * @throws Exception\InvalidArgumentException
     * @return SetCookie
     */
    public function setName($name)
    {
        if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
            throw new Exception\InvalidArgumentException("Cookie name cannot contain these characters: =,; \\t\\r\\n\\013\\014 ({$name})");
        }

        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set version
     *
     * @param integer $version
     * @throws Exception\InvalidArgumentException
     */
    public function setVersion($version)
    {
        if (!is_int($version)) {
            throw new Exception\InvalidArgumentException('Invalid Version number specified');
        }
        $this->version = $version;
    }

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set Max-Age
     *
     * @param integer $maxAge
     * @throws Exception\InvalidArgumentException
     */
    public function setMaxAge($maxAge)
    {
        if (!is_int($maxAge) || ($maxAge<0)) {
            throw new Exception\InvalidArgumentException('Invalid Max-Age number specified');
        }
        $this->maxAge = $maxAge;
    }

    /**
     * Get Max-Age
     *
     * @return integer
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }

    /**
     * @param int $expires
     * @throws Exception\InvalidArgumentException
     * @return SetCookie
     */
    public function setExpires($expires)
    {
        if (!empty($expires)) {
            if (is_string($expires)) {
                $expires = strtotime($expires);
            } elseif (!is_int($expires)) {
                throw new Exception\InvalidArgumentException('Invalid expires time specified');
            }
            $this->expires = (int) $expires;
        }
        return $this;
    }

    /**
     * @param bool $inSeconds
     * @return int
     */
    public function getExpires($inSeconds = false)
    {
        if ($this->expires == null) {
            return;
        }
        if ($inSeconds) {
            return $this->expires;
        }
        return gmdate('D, d-M-Y H:i:s', $this->expires) . ' GMT';
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param  bool $secure
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @param  bool $httponly
     */
    public function setHttponly($httponly)
    {
        $this->httponly = $httponly;
    }

    /**
     * @return bool
     */
    public function isHttponly()
    {
        return $this->httponly;
    }

    /**
     * Check whether the cookie has expired
     *
     * Always returns false if the cookie is a session cookie (has no expiry time)
     *
     * @param int $now Timestamp to consider as "now"
     * @return bool
     */
    public function isExpired($now = null)
    {
        if ($now === null) {
            $now = time();
        }

        if (is_int($this->expires) && $this->expires < $now) {
            return true;
        }

        return false;
    }

    /**
     * Check whether the cookie is a session cookie (has no expiry time set)
     *
     * @return bool
     */
    public function isSessionCookie()
    {
        return ($this->expires === null);
    }

    public function isValidForRequest($requestDomain, $path, $isSecure = false)
    {
        if ($this->getDomain() && (strrpos($requestDomain, $this->getDomain()) === false)) {
            return false;
        }

        if ($this->getPath() && (strpos($path, $this->getPath()) !== 0)) {
            return false;
        }

        if ($this->secure && $this->isSecure()!==$isSecure) {
            return false;
        }

        return true;

    }

    public function toString()
    {
        return 'Set-Cookie: ' . $this->getFieldValue();
    }

    public function toStringMultipleHeaders(array $headers)
    {
        $headerLine = $this->toString();
        /* @var $header SetCookie */
        foreach ($headers as $header) {
            if (!$header instanceof SetCookie) {
                throw new Exception\RuntimeException(
                    'The SetCookie multiple header implementation can only accept an array of SetCookie headers'
                );
            }
            $headerLine .= ', ' . $header->getFieldValue();
        }
        return $headerLine;
    }


}
