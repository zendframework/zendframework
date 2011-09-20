<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @todo FIND SPEC FOR THIS
 */
class Refresh implements HeaderDescription
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'refresh') {
            throw new Exception\InvalidArgumentException('Invalid header line for Refresh string');
        }

        // @todo implementation details
        $header->value= $value;
        
        return $header;
    }

    public function getFieldName()
    {
        return 'Refresh';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Refresh: ' . $this->getFieldValue();
    }
    
}
