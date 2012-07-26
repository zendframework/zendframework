<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace Zend\Mail\Storage;

use RecursiveIterator;
use Zend\Mail\Headers;
use Zend\Mail\Header\HeaderInterface;
use Zend\Mime;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Storage
 */
class Part implements RecursiveIterator, Part\PartInterface
{
    /**
     * Headers of the part
     * @var Headers|null
     */
    protected $_headers;

    /**
     * raw part body
     * @var null|string
     */
    protected $_content;

    /**
     * toplines as fetched with headers
     * @var string
     */
    protected $_topLines = '';

    /**
     * parts of multipart message
     * @var array
     */
    protected $_parts = array();

    /**
     * count of parts of a multipart message
     * @var null|int
     */
    protected $_countParts;

    /**
     * current position of iterator
     * @var int
     */
    protected $_iterationPos = 1;

    /**
     * mail handler, if late fetch is active
     * @var null|AbstractStorage
     */
    protected $_mail;

    /**
     * message number for mail handler
     * @var int
     */
    protected $_messageNum = 0;

    /**
     * Public constructor
     *
     * Part supports different sources for content. The possible params are:
     * - handler    an instance of AbstractStorage for late fetch
     * - id         number of message for handler
     * - raw        raw content with header and body as string
     * - headers    headers as array (name => value) or string, if a content part is found it's used as toplines
     * - noToplines ignore content found after headers in param 'headers'
     * - content    content as string
     * - strict     strictly parse raw content
     *
     * @param   array $params  full message with or without headers
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(array $params)
    {
        if (isset($params['handler'])) {
            if (!$params['handler'] instanceof AbstractStorage) {
                throw new Exception\InvalidArgumentException('handler is not a valid mail handler');
            }
            if (!isset($params['id'])) {
                throw new Exception\InvalidArgumentException('need a message id with a handler');
            }

            $this->_mail       = $params['handler'];
            $this->_messageNum = $params['id'];
        }
        
        $params['strict'] = isset($params['strict']) ? $params['strict'] : false;

        if (isset($params['raw'])) {
            Mime\Decode::splitMessage($params['raw'], $this->_headers, $this->_content, Mime\Mime::LINEEND, $params['strict']);
        } elseif (isset($params['headers'])) {
            if (is_array($params['headers'])) {
                $this->_headers = new Headers();
                $this->_headers->addHeaders($params['headers']);
            } else {
                if (empty($params['noToplines'])) {
                    Mime\Decode::splitMessage($params['headers'], $this->_headers, $this->_topLines);
                } else {
                    $this->_headers = Headers::fromString($params['headers']);
                }
            }

            if (isset($params['content'])) {
                $this->_content = $params['content'];
            }
        }
    }

    /**
     * Check if part is a multipart message
     *
     * @return bool if part is multipart
     */
    public function isMultipart()
    {
        try {
            return stripos($this->contentType, 'multipart/') === 0;
        } catch(Exception\ExceptionInterface $e) {
            return false;
        }
    }


    /**
     * Body of part
     *
     * If part is multipart the raw content of this part with all sub parts is returned
     *
     * @throws Exception\RuntimeException
     * @return string body
     */
    public function getContent()
    {
        if ($this->_content !== null) {
            return $this->_content;
        }

        if ($this->_mail) {
            return $this->_mail->getRawContent($this->_messageNum);
        }

        throw new Exception\RuntimeException('no content');
    }

    /**
     * Return size of part
     *
     * Quite simple implemented currently (not decoding). Handle with care.
     *
     * @return int size
     */
    public function getSize()
    {
        return strlen($this->getContent());
    }


    /**
     * Cache content and split in parts if multipart
     *
     * @throws Exception\RuntimeException
     * @return null
     */
    protected function _cacheContent()
    {
        // caching content if we can't fetch parts
        if ($this->_content === null && $this->_mail) {
            $this->_content = $this->_mail->getRawContent($this->_messageNum);
        }

        if (!$this->isMultipart()) {
            return;
        }

        // split content in parts
        $boundary = $this->getHeaderField('content-type', 'boundary');
        if (!$boundary) {
            throw new Exception\RuntimeException('no boundary found in content type to split message');
        }
        $parts = Mime\Decode::splitMessageStruct($this->_content, $boundary);
        if ($parts === null) {
            return;
        }
        $counter = 1;
        foreach ($parts as $part) {
            $this->_parts[$counter++] = new self(array('headers' => $part['header'], 'content' => $part['body']));
        }
    }

    /**
     * Get part of multipart message
     *
     * @param  int $num number of part starting with 1 for first part
     * @throws Exception\RuntimeException
     * @return Part wanted part
     */
    public function getPart($num)
    {
        if (isset($this->_parts[$num])) {
            return $this->_parts[$num];
        }

        if (!$this->_mail && $this->_content === null) {
            throw new Exception\RuntimeException('part not found');
        }

        if ($this->_mail && $this->_mail->hasFetchPart) {
            // TODO: fetch part
            // return
        }

        $this->_cacheContent();

        if (!isset($this->_parts[$num])) {
            throw new Exception\RuntimeException('part not found');
        }

        return $this->_parts[$num];
    }

    /**
     * Count parts of a multipart part
     *
     * @return int number of sub-parts
     */
    public function countParts()
    {
        if ($this->_countParts) {
            return $this->_countParts;
        }

        $this->_countParts = count($this->_parts);
        if ($this->_countParts) {
            return $this->_countParts;
        }

        if ($this->_mail && $this->_mail->hasFetchPart) {
            // TODO: fetch part
            // return
        }

        $this->_cacheContent();

        $this->_countParts = count($this->_parts);
        return $this->_countParts;
    }

    /**
     * Access headers collection
     *
     * Lazy-loads if not already attached.
     *
     * @return Headers
     */
    public function getHeaders()
    {
        if (null === $this->_headers) {
            if ($this->_mail) {
                $part = $this->_mail->getRawHeader($this->_messageNum);
                $this->_headers = Headers::fromString($part);
            } else {
                $this->_headers = new Headers();
            }
        }

        return $this->_headers;
    }

    /**
     * Get a header in specified format
     *
     * Internally headers that occur more than once are saved as array, all other as string. If $format
     * is set to string implode is used to concat the values (with Mime::LINEEND as delim).
     *
     * @param  string $name   name of header, matches case-insensitive, but camel-case is replaced with dashes
     * @param  string $format change type of return value to 'string' or 'array'
     * @throws Exception\InvalidArgumentException
     * @return string|array|HeaderInterface|\ArrayIterator value of header in wanted or internal format
     */
    public function getHeader($name, $format = null)
    {
        $header = $this->getHeaders()->get($name);
        if ($header === false) {
            $lowerName = strtolower(preg_replace('%([a-z])([A-Z])%', '\1-\2', $name));
            $header = $this->getHeaders()->get($lowerName);
            if ($header === false) {
                throw new Exception\InvalidArgumentException(
                    "Header with Name $name or $lowerName not found"
                );
            }
        }

        switch ($format) {
            case 'string':
                if ($header instanceof HeaderInterface) {
                    $return = $header->getFieldValue(HeaderInterface::FORMAT_RAW);
                } else {
                    $return = '';
                    foreach ($header as $h) {
                        $return .= $h->getFieldValue(HeaderInterface::FORMAT_RAW)
                                 . Mime\Mime::LINEEND;
                    }
                    $return = trim($return, Mime\Mime::LINEEND);
                }
                break;
            case 'array':
                if ($header instanceof HeaderInterface) {
                    $return = array($header->getFieldValue());
                } else {
                    $return = array();
                    foreach ($header as $h) {
                        $return[] = $h->getFieldValue(HeaderInterface::FORMAT_RAW);
                    }
                }
                break;
            default:
                $return = $header;
        }

        return $return;
    }

    /**
     * Get a specific field from a header like content type or all fields as array
     *
     * If the header occurs more than once, only the value from the first header
     * is returned.
     *
     * Throws an Exception if the requested header does not exist. If
     * the specific header field does not exist, returns null.
     *
     * @param  string $name       name of header, like in getHeader()
     * @param  string $wantedPart the wanted part, default is first, if null an array with all parts is returned
     * @param  string $firstName  key name for the first part
     * @return string|array wanted part or all parts as array($firstName => firstPart, partname => value)
     * @throws \Zend\Mime\Exception\RuntimeException
     */
    public function getHeaderField($name, $wantedPart = '0', $firstName = '0')
    {
        return Mime\Decode::splitHeaderField(current($this->getHeader($name, 'array')), $wantedPart, $firstName);
    }


    /**
     * Getter for mail headers - name is matched in lowercase
     *
     * This getter is short for Part::getHeader($name, 'string')
     *
     * @see Part::getHeader()
     *
     * @param  string $name header name
     * @return string value of header
     * @throws Exception\ExceptionInterface
     */
    public function __get($name)
    {
        return $this->getHeader($name, 'string');
    }

    /**
     * Isset magic method proxy to hasHeader
     *
     * This method is short syntax for Part::hasHeader($name);
     *
     * @see Part::hasHeader
     *
     * @param  string
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->getHeaders()->has($name);
    }

    /**
     * magic method to get content of part
     *
     * @return string content
     */
    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * implements RecursiveIterator::hasChildren()
     *
     * @return bool current element has children/is multipart
     */
    public function hasChildren()
    {
        $current = $this->current();
        return $current && $current instanceof Part && $current->isMultipart();
    }

    /**
     * implements RecursiveIterator::getChildren()
     *
     * @return Part same as self::current()
     */
    public function getChildren()
    {
        return $this->current();
    }

    /**
     * implements Iterator::valid()
     *
     * @return bool check if there's a current element
     */
    public function valid()
    {
        if ($this->_countParts === null) {
            $this->countParts();
        }
        return $this->_iterationPos && $this->_iterationPos <= $this->_countParts;
    }

    /**
     * implements Iterator::next()
     */
    public function next()
    {
        ++$this->_iterationPos;
    }

    /**
     * implements Iterator::key()
     *
     * @return string key/number of current part
     */
    public function key()
    {
        return $this->_iterationPos;
    }

    /**
     * implements Iterator::current()
     *
     * @return Part current part
     */
    public function current()
    {
        return $this->getPart($this->_iterationPos);
    }

    /**
     * implements Iterator::rewind()
     */
    public function rewind()
    {
        $this->countParts();
        $this->_iterationPos = 1;
    }
}
