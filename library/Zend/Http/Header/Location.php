<?php

namespace Zend\Http\Header;

use Zend\Uri\Uri;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.30
 */
class Location implements HeaderInterface
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = explode(':', $headerLine, 2);
        $value = trim($value);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'location') {
            throw new Exception\InvalidArgumentException('Invalid header line for Location string: "' . $name . '"');
        }

        if (!Uri::validateHost($value)) {
            throw new Exception\InvalidArgumentException('Invalid URI value for Location: "' . $value . '"');
        }
        // @todo implementation details
        $header->value = $value;

        return $header;
    }

    public function getFieldName()
    {
        return 'Location';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Location: ' . $this->getFieldValue();
    }
    
}
