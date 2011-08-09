<?php

namespace Zend\Http;

use Iterator,
    ArrayAccess,
    Countable,
    ArrayObject;

/**
 * Basic HTTP headers collection functionality
 *
 * Handles aggregation of headers
 */
abstract class Headers implements Iterator, Countable
{

    /**
     * @var array key value pairs of header name and handling class
     */
    protected static $headerClasses = array();

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
     * @param  string $string
     * @return Headers
     */
    public static function fromString($string)
    {
        $class = get_called_class();
        $headers = new $class();
        $current = array();

        // iterate the header lines, some might be continuations
        foreach (preg_split('\r\n', $string) as $line) {

            // check if a header name is present
            if (preg_match('/^(?P<name>[^()><@,;:\"\\/\[\]?=}{ \t]+):.*$/', $line, $matches)) {
                if ($current) {
                    // a header name was present, then store the current complete line
                    $headers->headerKeys[] = str_replace(array('-', '_'), '', strtolower($current['name']));
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
            $headers->headerKeys[] = str_replace(array('-', '_'), '', strtolower($current['name']));
            $headers->headers[] = $current;
        }
        return $headers;
    }

    public static function createHeadersFromString($name, $line)
    {
        /* @var $headerClass Header\HeaderDescription */
        $headerClass = static::getHeaderClassForName($name);
        $headers = $headerClass::fromString($line);
        return $headers;
    }

    /**
     * Add many headers at once
     *
     * Expects an array (or Traversable object) of type/value pairs.
     *
     * @param  array|Traversable $headers
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
            $this->addHeader($name, $value);
        }

        return $this;
    }

    /**
     * Add a header onto the queue
     * 
     * @param  Header $header
     * @param  string $content
     * @return Headers
     */
    public function addHeader($header, $content = null)
    {
        if (!$header instanceof Header\HeaderDescription) {
            $className= self::getHeaderClassForName($header);
            $header= new $className($header,$content);
        }

        $key = str_replace(array('-', '_'), '', strtolower($header->getName()));
        
        if (!array_key_exists($key, static::$headerClasses)) {
            throw new Exception\InvalidArgumentException('Provided header is not valid in this header container');
        }
        
        $this->headersKeys[] = $key;
        $this->headers[] = $header;
        return $this;
    }

    public function removeHeader($header)
    {
        $index = array_search($this->headers, $header, true);
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
     * @param  string $name
     * @return false|Header\HeaderDescription|\ArrayIterator
     */
    public function get($name)
    {
        $key = str_replace(array('-', '_'), '', strtolower($name));
        if (!in_array($name, $this->headersKeys)) {
            return false;
        }

        if (!isset(static::$headerClasses[$key])) {
            throw new Exception\InvalidArgumentException('This header collection does not have a header named ' . $name);
        }

        $class = static::$headerClasses[$key];

        if (in_array('Zend\Http\Header\MultipleHeaderDescription', class_implements($class, true))) {
            $headers = array();
            foreach (array_keys($this->headersKeys, $key) as $index) {
                $headers[] = $this->headers[$index];
            }
            return new \ArrayIterator($headers);
        } else {
            return $this->headers[array_search($key, $this->headersKeys)];
        }
    }

    /**
     * Test for existence of a type of header
     * 
     * @param  string $name
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
            $headers = static::createHeadersFromString($current['name'], $current['line']);
            if (is_array($headers)) {
                $current = array_shift($headers);
                foreach ($headers as $header) {
                    $this->headers[] = $header;
                }
            } else {
                $current = $headers;
            }
            $this->headers[key($this->headers)] = $current;
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
        $content = '';
        foreach ($this as $header) {
            $content .= $header->toString();
        }
        return $content;
    }

    public function toArray()
    {
        $headers= array();
        foreach ($this as $header) {
            $headers[$header->getName()]= $header->getValue();
        }
        return $headers;
    }
    protected static function getHeaderClassForName($name)
    {
        $headerName = str_replace(array('-', '_'), '', strtolower($name));
        if (array_key_exists($headerName, static::$headerClasses)) {
            return static::$headerClasses[$headerName];
        } else {
            return 'Zend\Http\Header\Header';
        }
    }

}
