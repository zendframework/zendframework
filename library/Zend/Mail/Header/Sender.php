<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Header;

use Zend\Mail;
use Zend\Mime\Mime;

/**
 * Sender header class methods.
 *
 * @see https://tools.ietf.org/html/rfc2822 RFC 2822
 * @see https://tools.ietf.org/html/rfc2047 RFC 2047
 */
class Sender implements HeaderInterface
{
    /**
     * @var \Zend\Mail\Address\AddressInterface
     */
    protected $address;

    /**
     * Header encoding
     *
     * @var null|string
     */
    protected $encoding;

    public static function fromString($headerLine)
    {
        list($name, $value) = GenericHeader::splitHeaderLine($headerLine);
        $value = HeaderWrap::mimeDecodeValue($value);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'sender') {
            throw new Exception\InvalidArgumentException('Invalid header line for Sender string');
        }

        $header      = new static();
        $senderName  = '';
        $senderEmail = '';

        // Check for address, and set if found
        if (preg_match('/^(?P<name>.*?)<(?P<email>[^>]+)>$/', $value, $matches)) {
            $senderName = trim($matches['name']);
            if (empty($senderName)) {
                $senderName = null;
            }
            $senderEmail = $matches['email'];
        }

        $header->setAddress($senderEmail, $senderName);

        return $header;
    }

    public function getFieldName()
    {
        return 'Sender';
    }

    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        if (! $this->address instanceof Mail\Address\AddressInterface) {
            return '';
        }

        $email = sprintf('<%s>', $this->address->getEmail());
        $name  = $this->address->getName();

        if (!empty($name)) {
            if ($format == HeaderInterface::FORMAT_ENCODED) {
                $encoding = $this->getEncoding();
                if ('ASCII' !== $encoding) {
                    $name  = HeaderWrap::mimeEncodeValue($name, $encoding);
                }
            }
            $email = sprintf('%s %s', $name, $email);
        }

        return $email;
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    public function getEncoding()
    {
        if (! $this->encoding) {
            $this->encoding = Mime::isPrintable($this->getFieldValue(HeaderInterface::FORMAT_RAW))
                ? 'ASCII'
                : 'UTF-8';
        }

        return $this->encoding;
    }

    public function toString()
    {
        return 'Sender: ' . $this->getFieldValue(HeaderInterface::FORMAT_ENCODED);
    }

    /**
     * Set the address used in this header
     *
     * @param  string|\Zend\Mail\Address\AddressInterface $emailOrAddress
     * @param  null|string $name
     * @throws Exception\InvalidArgumentException
     * @return Sender
     */
    public function setAddress($emailOrAddress, $name = null)
    {
        if (is_string($emailOrAddress)) {
            $emailOrAddress = new Mail\Address($emailOrAddress, $name);
        } elseif (!$emailOrAddress instanceof Mail\Address\AddressInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string or AddressInterface object; received "%s"',
                __METHOD__,
                (is_object($emailOrAddress) ? get_class($emailOrAddress) : gettype($emailOrAddress))
            ));
        }
        $this->address = $emailOrAddress;
        return $this;
    }

    /**
     * Retrieve the internal address from this header
     *
     * @return \Zend\Mail\Address\AddressInterface|null
     */
    public function getAddress()
    {
        return $this->address;
    }
}
