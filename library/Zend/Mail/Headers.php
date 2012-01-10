<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mail;

use ArrayIterator,
    Iterator,
    Countable,
    Traversable,
    Zend\Loader\PluginClassLoader,
    Zend\Loader\PluginClassLocator;

/**
 * Basic mail headers collection functionality
 *
 * Handles aggregation of headers
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
     * Header encoding; defaults to ASCII
     * 
     * @var string
     */
    protected $encoding = 'ASCII';

    /**
     * Populates headers from string representation
     *
     * Parses a string for headers, and aggregates them, in order, in the
     * current instance, primarily as strings until they are needed (they
     * will be lazy loaded)
     *
     * @param  string $string
     * @return Headers
     */
    public static function fromString($string)
    {
        $headers = new static();
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

    /**
     * Set an alternate implementation for the PluginClassLoader
     *
     * @param  PluginClassLocator $pluginClassLoader
     * @return Headers
     */
    public function setPluginClassLoader(PluginClassLocator $pluginClassLoader)
    {
        $this->pluginClassLoader = $pluginClassLoader;
        return $this;
    }

    /**
     * Return an instance of a PluginClassLocator, lazyload and inject map if necessary
     *
     * @return PluginClassLocator
     */
    public function getPluginClassLoader()
    {
        if ($this->pluginClassLoader === null) {
            $this->pluginClassLoader = new PluginClassLoader(array(
                'bcc'          => 'Zend\Mail\Header\Bcc',
                'cc'           => 'Zend\Mail\Header\Cc',
                'contenttype'  => 'Zend\Mail\Header\ContentType',
                'content_type' => 'Zend\Mail\Header\ContentType',
                'content-type' => 'Zend\Mail\Header\ContentType',
                'date'         => 'Zend\Mail\Header\Date',
                'from'         => 'Zend\Mail\Header\From',
                'mimeversion'  => 'Zend\Mail\Header\MimeVersion',
                'mime_version' => 'Zend\Mail\Header\MimeVersion',
                'mime-version' => 'Zend\Mail\Header\MimeVersion',
                'received'     => 'Zend\Mail\Header\Received',
                'replyto'      => 'Zend\Mail\Header\ReplyTo',
                'reply_to'     => 'Zend\Mail\Header\ReplyTo',
                'reply-to'     => 'Zend\Mail\Header\ReplyTo',
                'sender'       => 'Zend\Mail\Header\Sender',
                'subject'      => 'Zend\Mail\Header\Subject',
                'to'           => 'Zend\Mail\Header\To',
            ));
        }
        return $this->pluginClassLoader;
    }

    /**
     * Set the header encoding
     * 
     * @param  string $encoding 
     * @return Headers
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        foreach ($this as $header) {
            $header->setEncoding($encoding);
        }
        return $this;
    }

    /**
     * Get the header encoding
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Add many headers at once
     *
     * Expects an array (or Traversable object) of type/value pairs.
     *
     * @param  array|Traversable $headers
     * @return Headers
     */
    public function addHeaders($headers)
    {
        if (!is_array($headers) && !$headers instanceof Traversable) {
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
                } elseif ($value instanceof Header) {
                    $this->addHeader($value);
                }
            } elseif (is_string($name)) {
                $this->addHeaderLine($name, $value);
            }

        }

        return $this;
    }

    /**
     * Add a raw header line, either in name => value, or as a single string 'name: value'
     *
     * This method allows for lazy-loading in that the parsing and instantiation of Header object
     * will be delayed until they are retrieved by either get() or current()
     *
     * @throws Exception\InvalidArgumentException
     * @param  string $headerFieldNameOrLine
     * @param  string $fieldValue optional
     * @return Headers
     */
    public function addHeaderLine($headerFieldNameOrLine, $fieldValue = null)
    {
        if (!is_string($headerFieldNameOrLine)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects its first argument to be a string; received "%s"',
                (is_object($headerFieldNameOrLine) ? get_class($headerFieldNameOrLine) : gettype($headerFieldNameOrLine))
            ));
        }

        $matches = null;
        if (preg_match('/^(?P<name>[^()><@,;:\"\\/\[\]?=}{ \t]+):.*$/', $headerFieldNameOrLine, $matches)
            && $fieldValue === null
        ) {
            // is a header
            $headerName = $matches['name'];
            $headerKey  = str_replace(array('-', '_', ' ', '.'), '', strtolower($matches['name']));
            $line       = $headerFieldNameOrLine;
        } elseif ($fieldValue === null) {
            throw new Exception\InvalidArgumentException('A field name was provided without a field value');
        } else {
            $headerName = $headerFieldNameOrLine;
            $headerKey  = str_replace(array('-', '_', ' ', '.'), '', strtolower($headerFieldNameOrLine));
            $line       = $headerFieldNameOrLine . ': ' . $fieldValue;
        }

        $this->headersKeys[] = $headerKey;
        $this->headers[]     = array('name' => $headerName, 'line' => $line);
        return $this;
    }

    /**
     * Add a Header to this container, for raw values @see addHeaderLine() and addHeaders()
     * 
     * @param  Header $header
     * @return Headers
     */
    public function addHeader(Header $header)
    {
        $key = $this->normalizeFieldName($header->getFieldName());

        $this->headersKeys[] = $key;
        $this->headers[] = $header;
        $header->setEncoding($this->getEncoding());
        return $this;
    }

    /**
     * Remove a Header from the container
     *
     * @param Header $header
     * @return bool
     */
    public function removeHeader(Header $header)
    {
        $index = array_search($header, $this->headers, true);
        if ($index !== false) {
            unset($this->headersKeys[$index]);
            unset($this->headers[$index]);
            return true;
        }
        return false;
    }

    /**
     * Clear all headers
     *
     * Removes all headers from queue
     * 
     * @return Headers
     */
    public function clearHeaders()
    {
        $this->headers = $this->headersKeys = array();
        return $this;
    }

    /**
     * Get all headers of a certain name/type
     * 
     * @param  string $name
     * @return false|Header|ArrayIterator
     */
    public function get($name)
    {
        $key = $this->normalizeFieldName($name);
        if (!in_array($key, $this->headersKeys)) {
            return false;
        }

        $class = ($this->getPluginClassLoader()->load($key)) ?: 'Zend\Mail\Header\GenericHeader';

        if (in_array('Zend\Mail\Header\MultipleHeaderDescription', class_implements($class, true))) {
            $headers = array();
            foreach (array_keys($this->headersKeys, $key) as $index) {
                if (is_array($this->headers[$index])) {
                    $this->lazyLoadHeader($index);
                }
            }
            foreach (array_keys($this->headersKeys, $key) as $index) {
                $headers[] = $this->headers[$index];
            }
            return new ArrayIterator($headers);
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
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        $name = $this->normalizeFieldName($name);
        return (in_array($name, $this->headersKeys));
    }

    /**
     * Advance the pointer for this object as an interator
     *
     * @return void
     */
    public function next()
    {
        next($this->headers);
    }

    /**
     * Return the current key for this object as an interator
     *
     * @return mixed
     */
    public function key()
    {
        return (key($this->headers));
    }

    /**
     * Is this iterator still valid?
     *
     * @return bool
     */
    public function valid()
    {
        return (current($this->headers) !== false);
    }

    /**
     * Reset the internal pointer for this object as an iterator
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->headers);
    }

    /**
     * Return the current value for this iterator, lazy loading it if need be
     *
     * @return Header
     */
    public function current()
    {
        $current = current($this->headers);
        if (is_array($current)) {
            $current = $this->lazyLoadHeader(key($this->headers));
        }
        return $current;
    }

    /**
     * Return the number of headers in this contain, if all headers have not been parsed, actual count could
     * increase if MultipleHeader objects exist in the Request/Response.  If you need an exact count, iterate
     *
     * @return int count of currently known headers
     */
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
        foreach ($this->toArray() as $fieldName => $fieldValue) {
            if (is_array($fieldValue)) {
                // Handle multi-value headers
                foreach ($fieldValue as $value) {
                    $headers .= $fieldName . ': ' . $value . "\r\n";
                }
                continue;
            }
            // Handle single-value headers
            $headers .= $fieldName . ': ' . $fieldValue . "\r\n";
        }
        return $headers;
    }

    /**
     * Return the headers container as an array
     *
     * @todo determine how to produce single line headers, if they are supported
     * @return array
     */
    public function toArray()
    {
        $headers = array();
        /* @var $header Header */
        foreach ($this->headers as $header) {
            if ($header instanceof Header\MultipleHeaderDescription) {
                $name = $header->getFieldName();
                if (!isset($headers[$name])) {
                    $headers[$name] = array();
                }
                $headers[$name][] = $header->getFieldValue();
            } elseif ($header instanceof Header) {
                $headers[$header->getFieldName()] = $header->getFieldValue();
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

    /**
     * By calling this, it will force parsing and loading of all headers, after this count() will be accurate
     *
     * @return bool
     */
    public function forceLoading()
    {
        foreach ($this as $item) {
            // $item should now be loaded
        }
        return true;
    }

    /**
     * @param $index
     * @return mixed|void
     */
    protected function lazyLoadHeader($index)
    {
        $current = $this->headers[$index];

        $key = $this->headersKeys[$index];
        /* @var $class Header */
        $class = ($this->getPluginClassLoader()->load($key)) ?: 'Zend\Mail\Header\GenericHeader';

        $encoding = $this->getEncoding();
        $headers  = $class::fromString($current['line']);
        if (is_array($headers)) {
            $current = array_shift($headers);
            $current->setEncoding($encoding);
            $this->headers[$index] = $current;
            foreach ($headers as $header) {
                $header->setEncoding($encoding);
                $this->headersKeys[] = $key;
                $this->headers[]     = $header;
            }
            return $current;
        }

        $current = $headers;
        $current->setEncoding($encoding);
        $this->headers[$index] = $current;
        return $current;
    }

    /**
     * Normalize a field name
     * 
     * @param  string $fieldName 
     * @return string
     */
    protected function normalizeFieldName($fieldName)
    {
        return str_replace(array('-', '_', ' ', '.'), '', strtolower($fieldName));
    }
}
