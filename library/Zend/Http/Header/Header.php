<?php

namespace Zend\Http\Header;

class Header implements HeaderDescription
{
    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $value = null;

    public static function fromString($header)
    {
        list($name, $values) = explode(': ', $header, 1);
        $headers = array();
        foreach (explode(',', $values) as $value) {
            $headers[] = new static($name, $value);
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
    public function __construct($name = null, $value = null)
    {
        if ($name) {
            $this->setName($name);
        }
        if ($value) {
            $this->setValue($value);
        }

    }

    /**
     * Set header name
     * 
     * @param  string $type
     * @return Header
     */
    public function setName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new Exception\InvalidArgumentException('Header name must be a string');
        }

        // Pre-filter to normalize valid characters
        $name = $this->normalizeHeaderName((string) $name);

        // Validate what we have
        if (!preg_match('/^[a-z][a-z0-9-]*$/i', $name)) {
            throw new Exception\InvalidArgumentException('Header name must start with a letter, and consist of only letters, numbers, and dashes');
        }

        $this->name= $name;
        return $this;
    }

    /**
     * Retrieve header name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
    protected function normalizeHeaderName($string)
    {
        $type = str_replace(array('_', '-'), ' ', $string);
        $type = ucwords($type);

        return str_replace(' ', '-', $type);
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
        $name  = $this->getName();
        $value = $this->getValue();

        return $name. ': ' . $value . "\r\n";
    }

}
