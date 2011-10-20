<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.33
 */
class ProxyAuthenticate implements MultipleHeaderDescription
{

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'proxy-authenticate') {
            throw new Exception\InvalidArgumentException('Invalid header line for Proxy-Authenticate string');
        }

        // @todo implementation details
        $header->value= $value;
        
        return $header;
    }

    public function getFieldName()
    {
        return 'Proxy-Authenticate';
    }

    public function getFieldValue()
    {
        return $this->value;
    }

    public function toString()
    {
        return 'Proxy-Authenticate: ' . $this->getFieldValue();
    }
    
    public function toStringMultipleHeaders(array $headers)
    {
        $strings = array($this->toString());
        foreach ($headers as $header) {
            if (!$header instanceof ProxyAuthenticate) {
                throw new Exception\RuntimeException(
                    'The ProxyAuthenticate multiple header implementation can only accept an array of ProxyAuthenticate headers'
                );
            }
            $strings[] = $header->toString();
        }
        return implode("\r\n", $strings);
    }
}
