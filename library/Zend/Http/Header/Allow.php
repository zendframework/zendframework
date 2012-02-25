<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.7
 */
class Allow implements HeaderDescription
{

    protected $allowedMethods = array();

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'allow') {
            throw new Exception\InvalidArgumentException('Invalid header line for Allow string: "' . $name . '"');
        }

        foreach (explode(',', $value) as $method) {
            $header->allowedMethods[] = trim(strtoupper($method));
        }

        return $header;
    }

    public function getFieldName()
    {
        return 'Allow';
    }

    public function getFieldValue()
    {
        return implode(', ', $this->allowedMethods);
    }

    public function getAllowedMethods()
    {
        return $this->allowedMethods;
    }

    public function setAllowedMethods(array $allowedMethods)
    {
        $this->allowedMethods = $allowedMethods;
    }

    public function toString()
    {
        return 'Allow: ' . $this->getFieldValue();
    }
    
}
