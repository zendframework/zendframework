<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.37
 */
class RetryAfter implements HeaderInterface
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'retry-after') {
            throw new Exception\InvalidArgumentException('Invalid header line for Retry-After string: "' . $name . '"');
        }

        // @todo implementation details
        $header->value = $value;

        return $header;
    }

    public function getFieldName()
    {
        return 'Retry-After';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Retry-After: ' . $this->getFieldValue();
    }

}
