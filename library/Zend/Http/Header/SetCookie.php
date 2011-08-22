<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.ietf.org/rfc/rfc2109.txt
 * @see http://www.w3.org/Protocols/rfc2109/rfc2109
 */
class SetCookie implements MultipleHeaderDescription
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
     * @var boolean
     */
    protected $secure = null;

    /**
     * @var true
     */
    protected $httponly = null;

    /*
    public static function fromStringMultipleHeaders($headerLine)
    {
        $headers = array();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'set-cookie') {
            throw new Exception\InvalidArgumentException('Invalid header line for Set-Cookie string');
        }

        $singleHeaderLines = preg_split('#(Sun|Mon|Tue|Wed|Thu|Fri|Sat),#', $headerLine);

        die();
        foreach ($singleHeaderLines as $singleHeaderLine) {
            $headers[] = self::fromString($singleHeaderLine, true);
        }

        return $headers;
    }
    */

    /**
     * @static
     * @throws Exception\InvalidArgumentException
     * @param $headerLine
     * @param bool $bypassHeaderFieldName
     * @return array|SetCookie
     */
    public static function fromString($headerLine, $bypassHeaderFieldName = false)
    {
        static $setCookieProcessor = null;

        if ($setCookieProcessor === null) {
            $setCookieClass = get_called_class();
            $setCookieProcessor = function($headerLine) use ($setCookieClass) {
                $header = new $setCookieClass;
                $keyValuePairs = preg_split('#;\s*#', $headerLine);
                foreach ($keyValuePairs as $keyValue) {
                    if (strpos($keyValue, '=')) {
                        list($headerKey, $headerValue) = preg_split('#=\s*#', $keyValue, 2);
                    } else {
                        $headerKey = $keyValue;
                        $headerValue = null;
                    }

                    switch (str_replace(array('-', '_'), '', strtolower($headerKey))) {
                        case 'expires':  $header->setExpires($headerValue); break;
                        case 'domain':   $header->setDomain($headerValue); break;
                        case 'path':     $header->setPath($headerValue); break;
                        case 'secure':   $header->setSecure(true); break;
                        case 'httponly': $header->setHttponly(true); break;
                        default:
                            $header->setName($headerKey);
                            $header->setValue($headerValue);
                    }
                }


                return $header;
            };
        }

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'set-cookie') {
            throw new Exception\InvalidArgumentException('Invalid header line for Set-Cookie string');
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
     * @param string $domain
     * @param int $expires
     * @param string $path
     * @param bool $secure
     */
    public function __construct($name = null, $value = null, $domain = null, $expires = null, $path = null, $secure = false, $httponly = true)
    {
        $this->type = 'Cookie';

        if ($name) {
            $this->setName($name);
        }

        if ($value) {
            $this->setValue($value); // in parent
        }

        if ($domain) {
            $this->setDomain($domain);
        }

        if ($expires) {
            $this->setExpires($expires);
        }

        if ($secure) {
            $this->setSecure($secure);
        }
    }

    public function getFieldName()
    {
        return 'Set-Cookie';
    }

    public function getFieldValue()
    {
        if ($this->getName() == '') {
            throw new Exception\RuntimeException('A cookie name is required to generate a field value for this cookie');
        }
        $fieldValue = $this->getName() . '=' . urlencode($this->getValue());
        if (($expires = $this->getExpires())) {
            $fieldValue .= '; Expires=' . $expires;
        }
        if (($domain = $this->getDomain())) {
            $fieldValue .= '; Domain=' . $domain;
        }
        if (($path = $this->getPath())) {
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
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @param int $expires
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }

    /**
     * @return int
     */
    public function getExpires()
    {
        return $this->expires;
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
     * @param boolean $secure
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * @return boolean
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @param \Zend\Http\Header\true $httponly
     */
    public function setHttponly($httponly)
    {
        $this->httponly = $httponly;
    }

    /**
     * @return \Zend\Http\Header\true
     */
    public function isHttponly()
    {
        return $this->httponly;
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
