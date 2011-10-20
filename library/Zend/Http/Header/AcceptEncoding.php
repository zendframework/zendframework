<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3
 */
class AcceptEncoding implements HeaderDescription
{

    public static function fromString($headerLine)
    {
        $acceptEncodingHeader = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'accept-encoding') {
            throw new Exception\InvalidArgumentException('Invalid header line for accept header string');
        }

        // @todo implementation details
        $acceptEncodingHeader->value= $value;
        
        return $acceptEncodingHeader;
    }

    public function getFieldName()
    {
        return 'Accept-Encoding';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Accept-Encoding: ' . $this->getFieldValue();
    }
    
}
