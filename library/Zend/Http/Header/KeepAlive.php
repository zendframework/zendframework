<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @todo Search for RFC for this header
 */
class KeepAlive implements HeaderDescription
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'keep-alive') {
            throw new Exception\InvalidArgumentException('Invalid header line for Keep-Alive string: "' . $name . '"');
        }

        // @todo implementation details
        $header->value = $value;

        return $header;
    }

    public function getFieldName()
    {
        return 'Keep-Alive';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Keep-Alive: ' . $this->getFieldValue();
    }

}
