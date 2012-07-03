<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mail\Header;

/**
 * Generic class for Headers with multiple occurs in the same message
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GenericMultiHeader extends GenericHeader implements MultipleHeadersInterface
{
    public static function fromString($headerLine)
    {
        $decodedLine = iconv_mime_decode($headerLine, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
        $parts = explode(': ', $decodedLine, 2);
        if (count($parts) != 2) {
            throw new Exception\InvalidArgumentException('Header must match with the format "name: value"');
        }
        list($fieldName, $fieldValue) = $parts;

        if (strpos($fieldValue, ',')) {
            $headers = array();
            $encoding = ($decodedLine != $headerLine) ? 'UTF-8' : 'ASCII';
            foreach (explode(',', $fieldValue) as $multiValue) {
                $header = new static($fieldName, $multiValue);
                $headers[] = $header->setEncoding($encoding);

            }
            return $headers;
        } else {
            $header = new static($fieldName, $fieldValue);
            if ($decodedLine != $headerLine) {
                $header->setEncoding('UTF-8');
            }
            return $header;
        }
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
        $name  = $this->getFieldName();
        $values = array($this->getFieldValue(HeaderInterface::FORMAT_ENCODED));
        foreach ($headers as $header) {
            if (!$header instanceof static) {
                throw new Exception\InvalidArgumentException(
                    'This method toStringMultipleHeaders was expecting an array of headers of the same type'
                );
            }
            $values[] = $header->getFieldValue(HeaderInterface::FORMAT_ENCODED);
        }
        return $name. ': ' . implode(',', $values);
    }
}
