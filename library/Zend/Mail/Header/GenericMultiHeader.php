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

namespace Zend\Mail\Header;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GenericMultiHeader implements MultipleHeaderDescription
{
    /**
     * @var string
     */
    protected $fieldName = null;

    /**
     * @var string
     */
    protected $fieldValue = null;

    /**
     * Header encoding
     * 
     * @var string
     */
    protected $encoding = 'ASCII';

    /**
     * Deserialize from a string
     * 
     * @param  string $headerLine 
     * @return GenericMultiHeader
     */
    public static function fromString($headerLine)
    {
        $headerLine = iconv_mime_decode($headerLine, ICONV_MIME_DECODE_CONTINUE_ON_ERROR);
        list($fieldName, $fieldValue) = explode(': ', $headerLine, 2);

        if (strpos($fieldValue, ',')) {
            $headers = array();
            foreach (explode(',', $fieldValue) as $multiValue) {
                $headers[] = new static($fieldName, $multiValue);
            }
            return $headers;
        } else {
            $header = new static($fieldName, $fieldValue);
            return $header;
        }


    }

    /**
     * Constructor
     * 
     * @param null|string $fieldName
     * @param null|string $fieldValue
     */
    public function __construct($fieldName = null, $fieldValue = null)
    {
        if ($fieldName) {
            $this->setFieldName($fieldName);
        }

        if ($fieldValue) {
            $this->setFieldValue($fieldValue);
        }
    }

    /**
     * Set header name
     * 
     * @param  string $fieldName
     * @return GenericHeader
     */
    public function setFieldName($fieldName)
    {
        if (!is_string($fieldName) || empty($fieldName)) {
            throw new Exception\InvalidArgumentException('Header name must be a string');
        }

        // Pre-filter to normalize valid characters, change underscore to dash
        $fieldName = str_replace(' ', '-', ucwords(str_replace(array('_', '-'), ' ', $fieldName)));

        // Validate what we have
        if (!preg_match('/^[a-z][a-z0-9-]*$/i', $fieldName)) {
            throw new Exception\InvalidArgumentException('Header name must start with a letter, and consist of only letters, numbers, and dashes');
        }

        $this->fieldName = $fieldName;
        return $this;
    }

    /**
     * Retrieve header name
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Set header value
     * 
     * @param  string|array $fieldValue
     * @return GenericHeader
     */
    public function setFieldValue($fieldValue)
    {
        $fieldValue = (string) $fieldValue;

        if (empty($fieldValue) || preg_match('/^\s+$/', $fieldValue)) {
            $fieldValue = '';
        }

        $this->fieldValue = $fieldValue;
        return $this;
    }

    /**
     * Retrieve header value
     * 
     * @return string
     */
    public function getFieldValue()
    {
        return $this->fieldValue;
    }

    /**
     * Set header encoding
     * 
     * @param  string $encoding 
     * @return GenericMultiHeader
     */
    public function setEncoding($encoding) 
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Get header encoding
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Cast to string
     *
     * Returns in form of "NAME: VALUE\r\n"
     *
     * @return string
     */
    public function toString()
    {
        $name  = $this->getFieldName();
        $value = $this->getFieldValue();

        return $name. ': ' . $value . "\r\n";
    }

    /**
     * Cast multiple header objectss to a single string header
     * 
     * @param  array $headers 
     * @return string
     */
    public function toStringMultipleHeaders(array $headers)
    {
        $name  = $this->getFieldName();
        $values = array($this->getFieldValue());
        foreach ($headers as $header) {
            if (!$header instanceof static) {
                throw new Exception\InvalidArgumentException('This method toStringMultipleHeaders was expecting an array of headers of the same type');
            }
            $values[] = $header->getFieldValue();
        }
        return $name. ': ' . implode(',', $values) . "\r\n";
    }
}
