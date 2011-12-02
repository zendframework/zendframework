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

    /**
     * Parse string to create header object
     * 
     * @param  string $headerLine 
     * @return AbstractAddressList
     */
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
        $fieldValue = str_replace("\r\n ", " ", $fieldValue);
        $values     = explode(',', $fieldValue);
        array_walk($values, 'trim');

        $addressList = $header->getAddressList();
        foreach ($values as $address) {
            // split values into name/email
            if (!preg_match('/^(?<name>.*?)<(?<email>[^>]+)>$/', $address, $matches)) {
                // Should we raise an exception here?
                continue;
            }
            $name  = trim($matches['name']);
            $email = $matches['email'];
            if (empty($name)) {
                $name = null;
            }

            // populate address list
            $addressList->add($email, $name);
        }

        return $header;
    }

    /**
     * Get field name of this header
     * 
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Get field value of this header
     * 
     * @return string
     */
    public function getFieldValue()
    {
        $emails = array();
        foreach ($this->getAddressList() as $address) {
            $email = $address->getEmail();
            $name  = $address->getName();
            if (empty($name)) {
                $emails[] = $email;
            } else {
                $name = str_replace(array('"', "'"), array('\\"', "'"), $name);
                if (false !== strstr($name, ',')) {
                    $name = sprintf('"%s"', $name);
                }
                $emails[] = sprintf('%s <%s>', $name, $email);
            }
        }
        $string = implode(",\r\n ", $emails);
        return $string;
    }

    /**
     * Set address list for this header
     * 
     * @param  AddressList $addressList 
     * @return void
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

    /**
     * Serialize to string
     * 
     * @return string
     */
    public function toString()
    {
        $name  = $this->getFieldName();
        $value = $this->getFieldValue();
        return sprintf("%s: %s\r\n", $name, $value);
    }
} 
