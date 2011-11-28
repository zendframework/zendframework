<?php

namespace Zend\Mail\Header;

class ContentType implements HeaderDescription
{
    /**
     * @var string
     */
    protected $type;

    /**
     * Factory: create Content-Type header object from string
     * 
     * @param  string $headerLine 
     * @return ContentType
     */
    public static function fromString($headerLine)
    {
        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'content-type') {
            throw new Exception\InvalidArgumentException('Invalid header line for Content-Type string');
        }

        if (!preg_match('#[a-z_-]/[a-z_-]#i', $value)) {
            throw new Exception\Invalid('Invalid value for Content-Type header (' . $value . ')');
        }

        $header = new static();
        $header->type= $value;
        
        return $header;
    }

    /**
     * Get header name
     * 
     * @return string
     */
    public function getFieldName()
    {
        return 'Content-Type';
    }

    /**
     * Get header value
     * 
     * @return string
     */
    public function getFieldValue()
    {
        return $this->type;
    }

    /**
     * Set content-type
     * 
     * @param  string $type 
     * @return ContentType
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Retrieve current content-type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Serialize header to string
     * 
     * @return string
     */
    public function toString()
    {
        return 'Content-Type: ' . $this->getFieldValue();
    }
}
