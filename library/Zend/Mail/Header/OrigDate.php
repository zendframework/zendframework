<?php

namespace Zend\Mail\Header;

/**
 * @todo Add accessors for setting date from DateTime, Zend\Date, or a string
 */
class OrigDate implements HeaderDescription
{
    /**
     * @var string
     */
    protected $value;

    /**
     * Factory: create header object from string
     * 
     * @param  string $headerLine 
     * @return OrigDate
     * @throws Exception\InvalidArgumentException
     */
    public static function fromString($headerLine)
    {
        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'orig-date') {
            throw new Exception\InvalidArgumentException('Invalid header line for Orig-Date string');
        }

        $header = new static();
        $header->value= $value;
        
        return $header;
    }

    /**
     * Get the header name 
     * 
     * @return string
     */
    public function getFieldName()
    {
        return 'Orig-Date';
    }

    /**
     * Get the header value
     * 
     * @return string
     */
    public function getFieldValue()
    {
        return $this->value;
    }

    /**
     * Serialize header to string
     * 
     * @return string
     */
    public function toString()
    {
        return 'Orig-Date: ' . $this->getFieldValue();
    }
}
