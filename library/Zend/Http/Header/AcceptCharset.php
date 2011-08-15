<?php

namespace Zend\Http\Header;

/**
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.2
 */
class AcceptCharset implements HeaderDescription
{

    protected $charsets = array();

    protected $qualityValue = 1;

    public static function fromString($headerLine)
    {
        $acceptCharsetHeader = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'accept-charset') {
            throw new Exception\InvalidArgumentException('Invalid header line for accept header string');
        }

        $valueParts = explode(';', $value, 2);
        if (count($valueParts) >= 1) {
            $acceptCharsetHeader->charsets = explode(',', $valueParts[0]);
        }
        if (count($valueParts) == 2 && preg_match('#q=(?P<qvalue>\d(?\.\d)+)#', $valueParts[1], $matches)) {
            $acceptCharsetHeader->qualityValue = $matches['qvalue'];
        }

        return $acceptCharsetHeader;
    }

    public function getFieldName()
    {
        return 'Accept-Charset';
    }

    public function getFieldValue()
    {
        return implode(', ', $this->charsets) . ';q=' . $this->qualityValue;
    }

    public function toString()
    {
        return 'Accept-Charset: ' . $this->getFieldValue();
    }
}
