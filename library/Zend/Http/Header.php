<?php

namespace Zend\Http;

use ArrayObject;

class Header implements HttpHeader
{
    /** @var string */
    protected $type;

    /** @var string */
    protected $value;

    /** @var bool */
    protected $replaceFlag;

    /**
     * Constructor
     * 
     * @param  string $type 
     * @param  string|array $value 
     * @param  bool $replace 
     * @return void
     */
    public function __construct($header, $value = null, $replace = false)
    {
        if (is_array($header) || $header instanceof ArrayObject) {
            $type    = $header['type']   ?: false;
            $value   = $header['value'] ?: '';
            $replace = (bool) ($header['replace'] ?: false);
            $header  = $type;
        }

        $this->setType($header);
        $this->setValue($value);
        $this->replace($replace);
    }

    /* mutators */

    /**
     * Set header type
     * 
     * @param  string $type 
     * @return Header
     */
    public function setType($type)
    {
        if (!is_scalar($type)) {
            throw new Exception\InvalidArgumentException('Header type must be scalar');
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
     * Set header value
     * 
     * @param  string|array $value 
     * @return Header
     */
    public function setValue($value, $separator = '; ')
    {
        if (is_array($value)) {
            $value = implode($separator, $value);
        }
        $value = (string) $value;
        if (empty($value) || preg_match('/^\s+$/', $value)) {
            $value = '';
        }
        $this->value = $value;
        return $this;
    }

    /**
     * Retrieve or set "replace" flag
     *
     * Used by the Headers class when sending headers.
     *
     * If a null flag is passed (or no argument passed), returns the value of 
     * the flag; otherwise, sets it.
     * 
     * @param  null|bool $flag 
     * @return Header|bool
     */
    public function replace($flag = null)
    {
        if (null === $flag) {
            return $this->replaceFlag;
        }
        $this->replaceFlag = (bool) $flag;
        return $this;
    }

    /* accessors */

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
     * Retrieve header value
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /* behavior */

    /**
     * Send header
     *
     * Proxies to __toString() to format header appropriately (and trims it), 
     * and uses value of replace flag as second argument for header().
     * 
     * @return void
     */
    public function send()
    {
        header(trim($this->__toString()), $this->replace());
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
}
