<?php

namespace Zend\Http\Header;

class GenericMultiHeader implements HeaderDescription, MultipleHeaderDescription
{
    /**
     * @var string
     */
    protected $fieldName = null;

    /**
     * @var string
     */
    protected $fieldValue = null;

    public static function fromStringMultipleHeaders($headerLine)
    {
        list($fieldName, $fieldValues) = explode(': ', $headerLine, 2);
        $headers = array();
        foreach (explode(',', $fieldValues) as $fieldValue) {
            $headers[] = new static($fieldName, ltrim($fieldValue));
        }
        return $headers;
    }

    public static function fromString($headerLine)
    {
        list($fieldName, $fieldValue) = explode(': ', $headerLine, 1);
        $header = new static($fieldName, $fieldValue);
        return $header;
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
