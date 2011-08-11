<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.41
 */
class TransferEncoding implements HeaderDescription
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'transfer-encoding') {
            throw new Exception\InvalidArgumentException('Invalid header line for Transfer-Encoding string');
        }

        // @todo implementation details

        return $header;
    }

    public function getFieldName()
    {
        return 'Transfer-Encoding';
    }

    public function getFieldValue()
    {
        // TODO: Implement getFieldValue() method.
    }

    public function toString()
    {
        return 'Transfer-Encoding: ' . $this->getFieldValue();
    }
    
}
