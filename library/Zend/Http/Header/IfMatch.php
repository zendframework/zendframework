<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.24
 */
class IfMatch implements HeaderDescription
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'if-match') {
            throw new Exception\InvalidArgumentException('Invalid header line for If-Match string');
        }

        // @todo implementation details
        $header->value= $value;
        
        return $header;
    }

    public function getFieldName()
    {
        return 'If-Match';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'If-Match: ' . $this->getFieldValue();
    }
    
}
