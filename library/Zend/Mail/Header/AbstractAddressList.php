<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Header;

use Zend\Mail\AddressList;
use Zend\Mail\Headers;

/**
 * Base class for headers composing address lists (to, from, cc, bcc, reply-to)
 */
abstract class AbstractAddressList implements HeaderInterface
{
    /**
     * @var AddressList
     */
    protected $addressList;

    /**
     * @var string Normalized field name
     */
    protected $fieldName;

    /**
     * Header encoding
     *
     * @var string
     */
    protected $encoding = 'ASCII';

    /**
     * @var string lower case field name
     */
    protected static $type;

    public static function fromString($headerLine)
    {
        list($fieldName, $fieldValue) = GenericHeader::splitHeaderLine($headerLine);
        $decodedValue = HeaderWrap::mimeDecodeValue($fieldValue);
        $wasEncoded = ($decodedValue !== $fieldValue);
        $fieldValue = $decodedValue;

        if (strtolower($fieldName) !== static::$type) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid header line for "%s" string',
                __CLASS__
            ));
        }
        $header = new static();
        if ($wasEncoded) {
            $header->setEncoding('UTF-8');
        }
        // split value on ","
        $fieldValue = str_replace(Headers::FOLDING, ' ', $fieldValue);
        $values     = str_getcsv($fieldValue, ',');
        array_walk(
            $values,
            function (&$value) {
                $value = trim($value);
            }
        );

        $addressList = $header->getAddressList();
        foreach ($values as $address) {
            $addressList->addFromString($address);
        }
        return $header;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        $emails   = array();
        $encoding = $this->getEncoding();

        foreach ($this->getAddressList() as $address) {
            $email = $address->getEmail();
            $name  = $address->getName();

            if (empty($name)) {
                $emails[] = $email;
                continue;
            }

            if (false !== strstr($name, ',')) {
                $name = sprintf('"%s"', $name);
            }

            if ($format === HeaderInterface::FORMAT_ENCODED
                && 'ASCII' !== $encoding
            ) {
                $name = HeaderWrap::mimeEncodeValue($name, $encoding);
            }

            $emails[] = sprintf('%s <%s>', $name, $email);
        }

        // Ensure the values are valid before sending them.
        if ($format !== HeaderInterface::FORMAT_RAW) {
            foreach ($emails as $email) {
                HeaderValue::assertValid($email);
            }
        }

        return implode(',' . Headers::FOLDING, $emails);
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

    /**
     * Set address list for this header
     *
     * @param  AddressList $addressList
     */
    public function setAddressList(AddressList $addressList)
    {
        $this->addressList = $addressList;
    }

    /**
     * Get address list managed by this header
     *
     * @return AddressList
     */
    public function getAddressList()
    {
        if (null === $this->addressList) {
            $this->setAddressList(new AddressList());
        }
        return $this->addressList;
    }

    public function toString()
    {
        $name  = $this->getFieldName();
        $value = $this->getFieldValue(HeaderInterface::FORMAT_ENCODED);
        return (empty($value)) ? '' : sprintf('%s: %s', $name, $value);
    }
}
