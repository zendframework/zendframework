<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Header;

use Zend\Mail\Headers;

class ContentTransferEncoding implements HeaderInterface
{
    /**
     * @var string
     */
    protected $transferEncoding;

    /**
     * @var array
     */
    protected $parameters = array();

    public static function fromString($headerLine)
    {
        $headerLine = iconv_mime_decode($headerLine, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'content-transfer-encoding') {
            throw new Exception\InvalidArgumentException('Invalid header line for Content-Transfer-Encoding string');
        }

        $transferEncoding   = $value;

        $header = new static();
        $header->setTransferEncoding($transferEncoding);

        return $header;
    }

    public function getFieldName()
    {
        return 'Content-Transfer-Encoding';
    }

    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        return $this->transferEncoding;
    }

    public function setEncoding($encoding)
    {
        // This header must be always in US-ASCII
        return $this;
    }

    public function getEncoding()
    {
        return 'ASCII';
    }

    public function toString()
    {
        return 'Content-Transfer-Encoding: ' . $this->getFieldValue();
    }

    /**
     * Set the content transfer encoding
     *
     * @param  string $transferEncoding
     * @throws Exception\InvalidArgumentException
     * @return ContentTransferEncoding
     */
    public function setTransferEncoding($transferEncoding)
    {
        if (!preg_match('/^(7bit|8bit|quoted\-printable|base64)+$/i', $transferEncoding)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a value in the format "(7bit|8bit|quoted-printable|base64)"; received "%s"',
                __METHOD__,
                (string) $transferEncoding
            ));
        }
        $this->transferEncoding = $transferEncoding;
        return $this;
    }

    /**
     * Retrieve the content transfer encoding 
     *
     * @return string
     */
    public function getTransferEncoding()
    {
        return $this->transferEncoding;
    }
}

