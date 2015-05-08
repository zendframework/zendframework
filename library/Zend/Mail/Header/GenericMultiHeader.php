<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Header;

/**
 * Generic class for Headers with multiple occurs in the same message
 */
class GenericMultiHeader extends GenericHeader implements MultipleHeadersInterface
{
    public static function fromString($headerLine)
    {
        list($fieldName, $fieldValue) = GenericHeader::splitHeaderLine($headerLine);
        $fieldValue = HeaderWrap::mimeDecodeValue($fieldValue);

        if (strpos($fieldValue, ',')) {
            $headers = array();
            foreach (explode(',', $fieldValue) as $multiValue) {
                $headers[] = new static($fieldName, $multiValue);
            }
            return $headers;
        }

        return new static($fieldName, $fieldValue);
    }

    /**
     * Cast multiple header objects to a single string header
     *
     * @param  array $headers
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function toStringMultipleHeaders(array $headers)
    {
        $name   = $this->getFieldName();
        $values = array($this->getFieldValue(HeaderInterface::FORMAT_ENCODED));

        foreach ($headers as $header) {
            if (! $header instanceof static) {
                throw new Exception\InvalidArgumentException(
                    'This method toStringMultipleHeaders was expecting an array of headers of the same type'
                );
            }
            $values[] = $header->getFieldValue(HeaderInterface::FORMAT_ENCODED);
        }

        return $name . ': ' . implode(',', $values);
    }
}
