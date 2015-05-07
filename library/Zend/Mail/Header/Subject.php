<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Header;

class Subject implements UnstructuredInterface
{
    /**
     * @var string
     */
    protected $subject = '';

    /**
     * Header encoding
     *
     * @var string
     */
    protected $encoding = 'ASCII';

    public static function fromString($headerLine)
    {
        list($name, $value) = GenericHeader::splitHeaderLine($headerLine);
        $decodedValue = HeaderWrap::mimeDecodeValue($value);
        $wasEncoded = ($decodedValue !== $value);
        $value = $decodedValue;

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'subject') {
            throw new Exception\InvalidArgumentException('Invalid header line for Subject string');
        }

        $header = new static();
        if ($wasEncoded) {
            $header->setEncoding('UTF-8');
        }
        $header->setSubject($value);

        return $header;
    }

    public function getFieldName()
    {
        return 'Subject';
    }

    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        if (HeaderInterface::FORMAT_ENCODED === $format) {
            return HeaderWrap::wrap($this->subject, $this);
        }

        return $this->subject;
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function setSubject($subject)
    {
        $subject = (string) $subject;
        if (! HeaderValue::isValid($subject)) {
            throw new Exception\InvalidArgumentException('Invalid Subject value detected');
        }
        $this->subject = $subject;
        return $this;
    }

    public function toString()
    {
        return 'Subject: ' . $this->getFieldValue(HeaderInterface::FORMAT_ENCODED);
    }
}
