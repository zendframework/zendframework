<?php
namespace Zend\Http;

use Zend\Loader\PluginClassLoader,
    Iterator,
    Countable;

/**
* Basic HTTP headers collection functionality
*
* Handles aggregation of headers
*/
class Headers implements Iterator, Countable
{

    /**
* @var PluginClassLoader
*/
    protected $pluginClassLoader = null;

    /**
* @var array key names for $headers array
*/
    protected $headersKeys = array();

    /**
* @var array Array of header array information or Header instances
*/
    protected $headers = array();

    /**
* Populates headers from string representation
*
* Parses a string for headers, and aggregates them, in order, in the
* current instance.
*
* On Request/Response variants, this should look for the first line
* matching the appropriate regex, and then forward the remainder of the
* string on to parent::fromString().
*
* @param string $string
* @return Headers
*/
    public static function fromString($string)
    {
        $class = get_called_class();
        $headers = new $class();
        $current = array();

        // iterate the header lines, some might be continuations
        foreach (preg_split('#\r\n#', $string) as $line) {

            // check if a header name is present
            if (preg_match('/^(?P<name>[^()><@,;:\"\\/\[\]?=}{ \t]+):.*$/', $line, $matches)) {
                if ($current) {
                    // a header name was present, then store the current complete line
                    $headers->headersKeys[] = str_replace(array('-', '_'), '', strtolower($current['name']));
                    $headers->headers[] = $current;
                }
                $current = array(
                    'name' => $matches['name'],
                    'line' => trim($line)
                );
            } elseif (preg_match('/^\s+.*$/', $line, $matches)) {
                // continuation: append to current line
                $current['line'] .= trim($line);
            } elseif (preg_match('/^\s*$/', $line)) {
                // empty line indicates end of headers
                break;
            } else {
                // Line does not match header format!
                throw new Exception\RuntimeException(sprintf(
                    'Line "%s"does not match header format!',
                    $line
                ));
            }
        }
        if ($current) {
            $headers->headersKeys[] = str_replace(array('-', '_'), '', strtolower($current['name']));
            $headers->headers[] = $current;
        }
        return $headers;
    }

    public function setPluginLoader(PluginClassLoader $pluginLoader)
    {
        $this->pluginClassLoader = $pluginLoader;
        return $this;
    }

    /**
* @return PluginClassLoader
*/
    public function getPluginClassLoader()
    {
        if ($this->pluginClassLoader === null) {
            $this->pluginClassLoader = new \Zend\Loader\PluginClassLoader(array(
                'accept' => 'Zend\Http\Header\Accept',
                'acceptcharset' => 'Zend\Http\Header\AcceptCharset',
                'acceptencoding' => 'Zend\Http\Header\AcceptEncoding',
                'acceptlanguage' => 'Zend\Http\Header\AcceptLanguage',
                'acceptranges' => 'Zend\Http\Header\AcceptRanges',
                'age' => 'Zend\Http\Header\Age',
                'allow' => 'Zend\Http\Header\Allow',
                'authenticationinfo' => 'Zend\Http\Header\AuthenticationInfo',
                'authorization' => 'Zend\Http\Header\Authorization',
                'cachecontrol' => 'Zend\Http\Header\CacheControl',
                'connection' => 'Zend\Http\Header\Connection',
                'contentdisposition' => 'Zend\Http\Header\ContentDisposition',
                'contentencoding' => 'Zend\Http\Header\ContentEncoding',
                'contentlanguage' => 'Zend\Http\Header\ContentLanguage',
                'contentlength' => 'Zend\Http\Header\ContentLength',
                'contentlocation' => 'Zend\Http\Header\ContentLocation',
                'contentmd5' => 'Zend\Http\Header\ContentMD5',
                'contentrange' => 'Zend\Http\Header\ContentRange',
                'contenttype' => 'Zend\Http\Header\ContentType',
                'cookie' => 'Zend\Http\Header\Cookie',
                'date' => 'Zend\Http\Header\Date',
                'etag' => 'Zend\Http\Header\Etag',
                'expect' => 'Zend\Http\Header\Expect',
                'expires' => 'Zend\Http\Header\Expires',
                'from' => 'Zend\Http\Header\From',
                'host' => 'Zend\Http\Header\Host',
                'ifmatch' => 'Zend\Http\Header\IfMatch',
                'ifmodifiedsince' => 'Zend\Http\Header\IfModifiedSince',
                'ifnonematch' => 'Zend\Http\Header\IfNoneMatch',
                'ifrange' => 'Zend\Http\Header\IfRange',
                'ifunmodifiedsince' => 'Zend\Http\Header\IfUnmodifiedSince',
                'keepalive' => 'Zend\Http\Header\KeepAlive',
                'lastmodified' => 'Zend\Http\Header\LastModified',
                'location' => 'Zend\Http\Header\Location',
                'maxforwards' => 'Zend\Http\Header\MaxForwards',
                'pragma' => 'Zend\Http\Header\Pragma',
                'proxyauthenticate' => 'Zend\Http\Header\ProxyAuthenticate',
                'proxyauthorization' => 'Zend\Http\Header\ProxyAuthorization',
                'range' => 'Zend\Http\Header\Range',
                'referer' => 'Zend\Http\Header\Referer',
                'refresh' => 'Zend\Http\Header\Refresh',
                'retryafter' => 'Zend\Http\Header\RetryAfter',
                'server' => 'Zend\Http\Header\Server',
                'setcookie' => 'Zend\Http\Header\SetCookie',
                'te' => 'Zend\Http\Header\TE',
                'trailer' => 'Zend\Http\Header\Trailer',
                'transferencoding' => 'Zend\Http\Header\TransferEncoding',
                'upgrade' => 'Zend\Http\Header\Upgrade',
                'useragent' => 'Zend\Http\Header\UserAgent',
                'vary' => 'Zend\Http\Header\Vary',
                'via' => 'Zend\Http\Header\Via',
                'warning' => 'Zend\Http\Header\Warning',
                'wwwauthenticate' => 'Zend\Http\Header\WWWAuthenticate'
            ));
        }
        return $this->pluginClassLoader;
    }

    /**
* Add many headers at once
*
* Expects an array (or Traversable object) of type/value pairs.
*
* @param array|Traversable $headers
* @return Headers
*/
    public function addHeaders($headers)
    {
        if (!is_array($headers) && !$headers instanceof \Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected array or Traversable; received "%s"',
                (is_object($headers) ? get_class($headers) : gettype($headers))
            ));
        }

        foreach ($headers as $name => $value) {
            if (is_int($name)) {
                if (is_string($value)) {
                    $this->addHeaderLine($value);
                } elseif (is_array($value) && count($value) == 1) {
                    $this->addHeaderLine(key($value), current($value));
                } elseif (is_array($value) && count($value) == 2) {
                    $this->addHeaderLine($value[0], $value[1]);
                } elseif ($value instanceof Header\HeaderDescription) {
                    $this->addHeader($value);
                }
            } elseif (is_string($name)) {
                $this->addHeaderLine($name, $value);
            }

        }

        return $this;
    }

    public function addHeaderLine($headerFieldNameOrLine, $fieldValue = null)
    {
        $matches = null;
        if (preg_match('/^(?P<name>[^()><@,;:\"\\/\[\]?=}{ \t]+):.*$/', $headerFieldNameOrLine, $matches)) {
            // is a header
            $headerName = $matches['name'];
            $headerKey = str_replace(array('-', '_'), '', strtolower($matches['name']));
            $line = $headerFieldNameOrLine;
        } elseif ($fieldValue === null) {
            throw new Exception\InvalidArgumentException('A field name was provided without a field value');
        } else {
            $headerName = $headerFieldNameOrLine;
            $headerKey = str_replace(array('-', '_'), '', strtolower($headerFieldNameOrLine));
            $line = $headerFieldNameOrLine . ': ' . $fieldValue;
        }

        $this->headersKeys[] = $headerKey;
        $this->headers[] = array('name' => $headerName, 'line' => $line);
        return $this;
    }

    /**
* Add a header onto the queue
*
* @param Header\HeaderDescription $header
* @param string $content
* @return Headers
*/
    public function addHeader(Header\HeaderDescription $header)
    {
        $key = str_replace(array('-', '_'), '', strtolower($header->getFieldName()));

        $this->headersKeys[] = $key;
        $this->headers[] = $header;
        return $this;
    }

    public function removeHeader($header)
    {
        $index = array_search($header, $this->headers, true);
        if ($index !== false) {
            unset($this->headersKeys[$index]);
            unset($this->headers[$index]);
        }
        return $this;
    }

    /**
* Clear all headers
*
* Removes all headers from queue
*
* @return void
*/
    public function clearHeaders()
    {
        $this->headers = $this->headersKeys = array();
    }

    /**
* Get all headers of a certain name/type
*
* @param string $name
* @return false|Header\HeaderDescription|\ArrayIterator
*/
    public function get($name)
    {
        $key = str_replace(array('-', '_'), '', strtolower($name));
        if (!in_array($key, $this->headersKeys)) {
            return false;
        }

        $class = ($this->getPluginClassLoader()->load($key)) ?: 'Zend\Http\Header\GenericHeader';

        if (in_array('Zend\Http\Header\MultipleHeaderDescription', class_implements($class, true))) {
            $headers = array();
            foreach (array_keys($this->headersKeys, $key) as $index) {
                if (is_array($this->headers[$index])) {
                    $this->lazyLoadHeader($index);
                }
            }
            foreach (array_keys($this->headersKeys, $key) as $index) {
                $headers[] = $this->headers[$index];
            }
            return new \ArrayIterator($headers);
        } else {
            $index = array_search($key, $this->headersKeys);
            if ($index === false) {
                return false;
            }
            if (is_array($this->headers[$index])) {
                return $this->lazyLoadHeader($index);
            } else {
                return $this->headers[$index];
            }
        }
    }

    /**
* Test for existence of a type of header
*
* @param string $name
* @return bool
*/
    public function has($name)
    {
        $name = str_replace(array('-', '_'), '', strtolower($name));
        return (in_array($name, $this->headersKeys));
    }

    public function next()
    {
        next($this->headers);
    }

    public function key()
    {
        return (key($this->headers));
    }

    public function valid()
    {
        return (current($this->headers) !== false);
    }

    public function rewind()
    {
        reset($this->headers);
    }

    /**
* @return Header\HeaderDescription
*/
    public function current()
    {
        $current = current($this->headers);
        if (is_array($current)) {
            $current = $this->lazyLoadHeader(key($this->headers));
        }
        return $current;
    }

    public function count()
    {
        return count($this->headers);
    }

    /**
* Render all headers at once
*
* This method handles the normal iteration of headers; it is up to the
* concrete classes to prepend with the appropriate status/request line.
*
* @return string
*/
    public function toString()
    {
        $headers = '';
        /* @var $header Header\HeaderDescription */
        foreach ($this->toArray() as $fieldName => $fieldValue) {
            $headers .= $fieldName . ': ' . $fieldValue . "\r\n";
        }
        return $headers;
    }

    public function toArray()
    {
        $headers = array();
        /* @var $header Header\HeaderDescription */
        foreach ($this->headers as $header) {
            if ($header instanceof Header\HeaderDescription) {
                $headers[$header->getFieldName()]= $header->getFieldValue();
            } else {
                $matches = null;
                preg_match('/^(?P<name>[^()><@,;:\"\\/\[\]?=}{ \t]+):\s*(?P<value>.*)$/', $header['line'], $matches);
                if ($matches) {
                    $headers[$matches['name']] = $matches['value'];
                }
            }
        }
        return $headers;
    }

    protected function lazyLoadHeader($index)
    {
        $current = $this->headers[$index];

        $key = $this->headersKeys[$index];
        /* @var $class Header\HeaderDescription */
        $class = ($this->getPluginClassLoader()->load($key)) ?: 'Zend\Http\Header\GenericHeader';

        if (in_array('Zend\Http\Header\MultipleHeaderDescription', class_implements($class, true))) {
            $headers = $class::fromStringMultipleHeaders($current['line']);
            $this->headers[$index] = $current = array_shift($headers);
            foreach ($headers as $header) {
                $this->headersKeys[] = $key;
                $this->headers[] = $header;
            }
        } else {
            $this->headers[$index] = $current = $class::fromString($current['line']);
        }

        return $current;
    }

}
