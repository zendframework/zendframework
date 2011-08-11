<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.ietf.org/rfc/rfc2109.txt
 */
class SetCookie implements HeaderDescription
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'set-cookie') {
            throw new Exception\InvalidArgumentException('Invalid header line for Set-Cookie string');
        }

        // @todo implementation details

        return $header;
    }

    public function getFieldName()
    {
        return 'Set-Cookie';
    }

    public function getFieldValue()
    {
        // TODO: Implement getFieldValue() method.
    }

    public function toString()
    {
        return 'Set-Cookie: ' . $this->getFieldValue();
    }
    
}
