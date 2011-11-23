<?php

namespace Zend\Mail\Header;

use Zend\Mail\AddressList;

/**
 * Base class for headers composing address lists (to, from, cc, bcc, reply-to)
 */
abstract class AbstractAddressList implements HeaderDescription
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
     * @var string lowercased field name
     */
    protected static $type;

    public static function fromString($headerLine)
    {
        // split into name/value
        list($fieldName, $fieldValue) = explode(': ', $headerLine, 2);

        if (strtolower($fieldName) !== static::$type) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid header line for "%s" string',
                __CLASS__
            ));
        }
        $header = new static();

        // split value on ","
        $values = explode(',', $fieldValue);
        array_walk($values, 'trim');

        $addressList = $header->getAddressList();
        foreach ($values as $address) {
            // split values into name/email
            if (!preg_match('/^(?<name>.*?)<(?<email>[^>]+>)$/', $address, $matches)) {
                // Should we raise an exception here?
                continue;
            }
            $name  = $matches['name'];
            $email = $matches['email'];
            if (empty($name)) {
                $name = null;
            }

            // populate address list
            $addressList->add($email, $name);
        }

        return $header;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function getFieldValue()
    {
        $emails = array();
        foreach ($this->getAddressList() as $address) {
            $email = sprintf('<%s>', $address->getEmail());
            $name  = $address->getName();
            if (empty($name)) {
                $emails[] = $email;
            } else {
                $emails[] = sprintf('%s %s', $name, $email);
            }
        }
        $string = implode(', ', $emails);
        return $string;
    }

    public function setAddressList(AddressList $addressList)
    {
        $this->addressList = $addressList;
    }

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
        $value = $this->getFieldValue();
        return sprintf("%s: %s\r\n", $name, $value);
    }
} 
