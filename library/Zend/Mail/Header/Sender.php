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

use Zend\Mail;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
     * @var string
     */
    protected $encoding = 'ASCII';

    /**
     * Factory: create Sender header object from string
     *
     * @param  string $headerLine
     * @return Sender
     * @throws Exception\InvalidArgumentException on invalid header line
     */
    public static function fromString($headerLine)
    {
        $headerLine = iconv_mime_decode($headerLine, ICONV_MIME_DECODE_CONTINUE_ON_ERROR);
        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'sender') {
            throw new Exception\InvalidArgumentException('Invalid header line for Sender string');
        }

        $header = new static();

        // Check for address, and set if found
        if (preg_match('^(?P<name>.*?)<(?P<email>[^>]+)>$', $value, $matches)) {
            $name = $matches['name'];
            if (empty($name)) {
                $name = null;
            } else {
                $name = iconv_mime_decode($name, ICONV_MIME_DECODE_CONTINUE_ON_ERROR);
            }
            $header->setAddress($matches['email'], $name);
        }

        return $header;
    }

    /**
     * Get header name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Sender';
    }

    /**
     * Get header value
     *
     * @return string
     */
    public function getFieldValue()
    {
        if (!$this->address instanceof Mail\Address\AddressInterface) {
            return '';
        }

        $email = sprintf('<%s>', $this->address->getEmail());
        $name  = $this->address->getName();
        if (!empty($name)) {
            $encoding = $this->getEncoding();
            if ('ASCII' !== $encoding) {
                $name  = HeaderWrap::mimeEncodeValue($name, $encoding, false);
            }
            $email = sprintf('%s %s', $name, $email);
        }
        return $email;
    }

    /**
     * Set header encoding
     *
     * @param  string $encoding
     * @return Sender
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Get header encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Serialize to string
     *
     * @return string
     */
    public function toString()
    {
        return 'Sender: ' . $this->getFieldValue();
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
        }
        if (!$emailOrAddress instanceof Mail\Address\AddressInterface) {
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
