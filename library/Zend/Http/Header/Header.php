<?php

namespace Zend\Http;

class Header implements HeaderDescription
{
    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var string
     */
    protected $value = null;

    public static function fromString($header)
    {
        list($type, $values) = explode(': ', $header, 1);
        $headers = array();
        foreach (explode(',', $values) as $value) {
            $headers[] = new static($type, $value);
        }
        return $headers;
    }

    /**
     * Constructor
     * 
     * @param  string $name
     * @param  string|array $value
     * @return void
     */
    public function __construct($type = null, $value = null)
    {
        if ($type) {
            $this->setType($type);
        }
        if ($value) {
            $this->setValue($value);
        }

    }

    /**
     * Set header type
     * 
     * @param  string $type
     * @return Header
     */
    public function setType($type)
    {
        if (!is_string($type) || empty($type)) {
            throw new Exception\InvalidArgumentException('Header type must be a string');
        }

        // Pre-filter to normalize valid characters
        $type = $this->normalizeHeaderType((string) $type);

        // Validate what we have
        if (!preg_match('/^[a-z][a-z0-9-]*$/i', $type)) {
            throw new Exception\InvalidArgumentException('Header type must start with a letter, and consist of only letters, numbers, and dashes');
        }

        $this->type = $type;
        return $this;
    }

    /**
     * Retrieve header type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set header value
     * 
     * @param  string|array $value
     * @return Header
     */
    public function setValue($value)
    {
        $value = (string) $value;

        if (empty($value) || preg_match('/^\s+$/', $value)) {
            $value = '';
        }

        $this->value = $value;
        return $this;
    }

    /**
     * Retrieve header value
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Normalize the header string
     * 
     * @param  string $string 
     * @return string
     */
    protected function normalizeHeaderType($string)
    {
        $type = str_replace(array('_', '-'), ' ', $string);
        $type = ucwords($type);

        return str_replace(' ', '-', $type);
    }

    /**
     * Cast to string
     *
     * Returns in form of "TYPE: VALUE\r\n"
     *
     * @return string
     */
    public function __toString()
    {
        $type  = $this->getType();
        $value = $this->getValue();

        return $type . ': ' . $value . "\r\n";
    }

}
