<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.13
 */
class ContentLength implements HeaderDescription
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'content-length') {
            throw new Exception\InvalidArgumentException('Invalid header line for Content-Length string');
        }

        // @todo implementation details
        $header->value= $value;
        
        return $header;
    }

    public function getFieldName()
    {
        return 'Content-Length';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Content-Length: ' . $this->getFieldValue();
    }
    
}
