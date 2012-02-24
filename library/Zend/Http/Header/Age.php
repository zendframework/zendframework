<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.6
 */
class Age implements HeaderDescription
{

    protected $deltaSeconds = null;

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'age') {
            throw new Exception\InvalidArgumentException('Invalid header line for Age string: "' . $name . '"');
        }

        $header->deltaSeconds = $value;

        return $header;
    }

    public function getFieldName()
    {
        return 'Age';
    }

    public function getFieldValue()
    {
        return $this->getDeltaSeconds();
    }

    public function setDeltaSeconds($deltaSeconds)
    {
        $this->deltaSeconds = $deltaSeconds;
        return $this;
    }

    public function getDeltaSeconds()
    {
        return $this->deltaSeconds;
    }

    public function toString()
    {
        return 'Age: ' . $this->getFieldValue();
    }
    
}
