<?php

namespace Zend\Mail\Header;

/**
 * @throws Exception\InvalidArgumentException
 */
class ContentType implements HeaderDescription
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'content-type') {
            throw new Exception\InvalidArgumentException('Invalid header line for Content-Type string');
        }

        // @todo implementation details
        $header->value= $value;
        
        return $header;
    }

    public function getFieldName()
    {
        return 'Content-Type';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Content-Type: ' . $this->getFieldValue();
    }
    
}
