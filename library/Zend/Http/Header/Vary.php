<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.44
 */
class Vary implements HeaderInterface
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'vary') {
            throw new Exception\InvalidArgumentException('Invalid header line for Vary string: "' . $name . '"');
        }

        // @todo implementation details
        $header->value = $value;

        return $header;
    }

    public function getFieldName()
    {
        return 'Vary';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Vary: ' . $this->getFieldValue();
    }

}
