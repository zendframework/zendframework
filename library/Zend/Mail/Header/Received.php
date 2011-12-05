<?php

namespace Zend\Mail\Header;

/**
 * @todo Allow setting date from DateTime, Zend\Date, or string
 */
class Received implements MultipleHeaderDescription
{
    /**
     * @var string
     */
    protected $value;

    /**
     * Header encoding
     * 
     * @var string
     */
    protected $encoding = 'ASCII';

    /**
     * Factory: create Received header object from string
     * 
     * @param  string $headerLine 
     * @return Received
     * @throws Exception\InvalidArgumentException
     */
    public static function fromString($headerLine)
    {

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'received') {
            throw new Exception\InvalidArgumentException('Invalid header line for Received string');
        }

        $header = new static();
        $header->value= $value;
        
        return $header;
    }

    /**
     * Get header name
     * 
     * @return string
     */
    public function getFieldName()
    {
        return 'Received';
    }

    /**
     * Get header value
     * 
     * @return string
     */
    public function getFieldValue()
    {
        return $this->value;
    }

    /**
     * Set header encoding
     * 
     * @param  string $encoding 
     * @return AbstractAddressList
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
     * Serialize to string
     * 
     * @return string
     */
    public function toString()
    {
        return 'Received: ' . $this->getFieldValue();
    }
    
    /**
     * Serialize collection of Received headers to string
     * 
     * @param  array $headers 
     * @return string
     */
    public function toStringMultipleHeaders(array $headers)
    {
        $strings = array($this->toString());
        foreach ($headers as $header) {
            if (!$header instanceof Received) {
                throw new Exception\RuntimeException(
                    'The Received multiple header implementation can only accept an array of Received headers'
                );
            }
            $strings[] = $header->toString();
        }
        return implode("\r\n", $strings);
    }
}
