<?php

namespace Zend\Http\Header;

class GenericMultiHeader extends GenericHeader implements MultipleHeaderInterface
{
    public static function fromString($headerLine)
    {
        list($fieldName, $fieldValue) = explode(': ', $headerLine, 2);

        if (strpos($fieldValue, ',')) {
            $headers = array();
            foreach (explode(',', $fieldValue) as $multiValue) {
                $headers[] = new static($fieldName, $multiValue);
            }
            return $headers;
        } else {
            $header = new static($fieldName, $fieldValue);
            return $header;
        }
    }

    public function toStringMultipleHeaders(array $headers)
    {
        $name  = $this->getFieldName();
        $values = array($this->getFieldValue());
        foreach ($headers as $header) {
            if (!$header instanceof static) {
                throw new Exception\InvalidArgumentException('This method toStringMultipleHeaders was expecting an array of headers of the same type');
            }
            $values[] = $header->getFieldValue();
        }
        return $name. ': ' . implode(',', $values) . "\r\n";
    }
}
