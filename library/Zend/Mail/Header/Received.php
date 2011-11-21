<?php

namespace Zend\Mail\Header;

/**
 * @throws Exception\InvalidArgumentException
 */
class Received implements MultipleHeaderDescription
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'received') {
            throw new Exception\InvalidArgumentException('Invalid header line for received string');
        }

        // @todo implementation details
        $header->value= $value;
        
        return $header;
    }

    public function getFieldName()
    {
        return 'Received';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Received: ' . $this->getFieldValue();
    }
    
    public function toStringMultipleHeaders(array $headers)
    {
        $strings = array($this->toString());
        foreach ($headers as $header) {
            if (!$header instanceof Received) {
                throw new Exception\RuntimeException(
                    'The Received multiple header implementation can only accept an array of Received headers'
                );
            }
            $strings[] = $header->toString();
        }
        return implode("\r\n", $strings);
    }
}
