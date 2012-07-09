<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.26
 */
class IfNoneMatch implements HeaderInterface
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'if-none-match') {
            throw new Exception\InvalidArgumentException('Invalid header line for If-None-Match string: "' . $name . '"');
        }

        // @todo implementation details
        $header->value = $value;

        return $header;
    }

    public function getFieldName()
    {
        return 'If-None-Match';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'If-None-Match: ' . $this->getFieldValue();
    }

}
