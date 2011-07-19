<?php

namespace Zend\Http;

use SplQueue;

/**
 * Basic HTTP headers collection functionality
 *
 * Handles aggregation of headers and HTTP protocol version.
 */
abstract class Headers extends SplQueue implements HttpHeaders
{
    /**@+
     * Constants containing patterns for parsing HTTP headers from a string
     */
    const PATTERN_HEADER_DELIM       = "/\r\n/";
    const PATTERN_TOKEN              = "(?P<token>[^()><@,;:\"\\/\[\]?=}{ \t]+)";
    const PATTERN_FIELD_CONTENT      = "(?P<content>.*)";
    const PATTERN_FIELD_CONTINUATION = '/^\s+(?P<content>.*)$/';
    /**@-*/

    protected $allowedProtocolVersions = '/^\d+\.\d+$/';

    /**
     * Set of headers sorted by type
     *
     * Used to allow fetching headers by type.
     *
     * @var array Array of SplQueue instances
     */
    protected $headers = array();

    protected $isSent = false;

    protected $protocolVersion = '1.1';

    /**
     * Retrieve HTTP protocol version
     * 
     * @return string|float
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Set HTTP protocol version
     * 
     * @param  string|float $version 
     * @return Headers
     */
    public function setProtocolVersion($version)
    {
        if (!is_scalar($version) || !preg_match($this->allowedProtocolVersions, $version)) {
            $version = is_scalar($version) ? (string) $version : gettype($version);
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid protocol version: "%s"',
                (string) $version
            ));
        }
        $this->protocolVersion = (string) $version;
        return $this;
    }

    /**
     * Add a header onto the queue
     * 
     * @param  HttpHeader $header 
     * @return Headers
     */
    public function addHeader($header, $content = null, $replace = false)
    {
        if (!$header instanceof HttpHeader) {
            if (is_array($header)) {
                $header = new Header($header);
            } else {
                $header = new Header($header, $content, $replace);
            }
        }

        $this->push($header);
        return $this;
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

        foreach ($headers as $type => $content) {
            $this->addHeader($type, $content);
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
        while (count($this)) {
            $this->dequeue();
        }
        $this->headers = array();
    }

    /**
     * Push a header onto the queue
     * 
     * @param  Header $value 
     * @return void
     * @throws Exception\InvalidArgumentException when non-Header object provided
     */
    public function push($value)
    {
        if (!$value instanceof HttpHeader) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Headers may only aggregate Zend\Http\HttpHeader objects; received %s',
                (is_object($value) ? get_class($value) : gettype($value))
            ));
        }

        $type = strtolower($value->getType());
        if (!array_key_exists($type, $this->headers)) {
            $this->headers[$type] = new SplQueue();
        }
        $this->headers[$type]->push($value);

        return parent::push($value);
    }

    /**
     * Unshift a header onto the queue
     * 
     * @param  Header $value 
     * @return void
     * @throws Exception\InvalidArgumentException when non-Header object provided
     */
    public function unshift($value)
    {
        if (!$value instanceof HttpHeader) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Headers may only aggregate Zend\Http\HttpHeader objects; received %s',
                (is_object($value) ? get_class($value) : gettype($value))
            ));
        }

        $type = strtolower($value->getType());
        if (!array_key_exists($type, $this->headers)) {
            $this->headers[$type] = new SplQueue();
        }
        $this->headers[$type]->unshift($value);

        return parent::unshift($value);
    }

    /**
     * Get all headers of a certain name/type
     * 
     * @param  string $type 
     * @return false|SplQueue
     */
    public function get($type)
    {
        if ($this->has($type)) {
            return $this->headers[strtolower($type)];
        }
        return false;
    }

    /**
     * Test for existence of a type of header
     * 
     * @param  string $type 
     * @return bool
     */
    public function has($type)
    {
        return array_key_exists(strtolower($type), $this->headers);
    }

    /**
     * Render all headers at once
     *
     * This method handles the normal iteration of headers; it is up to the 
     * concrete classes to prepend with the appropriate status/request line.
     * 
     * @return string
     */
    public function __toString()
    {
        $content = '';
        foreach ($this as $header) {
            $content .= (string) $header;
        }
        return $content;
    }

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
    public function fromString($string)
    {
        $this->clearHeaders();
        $headers = array();
        $type    = false;
        foreach (preg_split(self::PATTERN_HEADER_DELIM, $string) as $line) {
            if (preg_match('/^' . self::PATTERN_TOKEN . ':' . self::PATTERN_FIELD_CONTENT . '$/', $line, $matches)) {
                $type    = $matches['token'];
                $content = trim($matches['content']);
                if (isset($headers[$type]) && is_string($headers[$type])) {
                    $headers[$type] = array($headers[$type], $content);
                } elseif (isset($headers[$type]) && is_array($headers[$type])) {
                    $headers[$type][] = $content;
                } else {
                    $headers[$type] = $content;
                }
            } elseif (preg_match(self::PATTERN_FIELD_CONTINUATION, $line, $matches)) {
                if ($type) {
                    $headers[$type] .= trim($matches['content']);
                }
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
        foreach ($headers as $type => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $this->addHeader($type, $v);
                }
            } else {
                $this->addHeader($type, $value);
            }
        }

        return $this;
    }
}
