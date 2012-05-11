<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.25
 */
class IfModifiedSince implements HeaderInterface
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'if-modified-since') {
            throw new Exception\InvalidArgumentException('Invalid header line for If-Modified-Since string: "' . $name . '"');
        }

        // @todo implementation details
        $header->value = $value;

        return $header;
    }

    public function getFieldName()
    {
        return 'If-Modified-Since';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'If-Modified-Since: ' . $this->getFieldValue();
    }

}
