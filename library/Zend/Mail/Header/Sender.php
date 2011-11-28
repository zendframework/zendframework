<?php

namespace Zend\Mail\Header;

use Zend\Mail\Address,
    Zend\Mail\AddressDescription,
    Zend\Mail\Exception;

/**
 * @throws Exception\InvalidArgumentException
 */
class Sender implements HeaderDescription
{
    /**
     * @var AddressDescription|null
     */
    protected $address;

    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'sender') {
            throw new Exception\InvalidArgumentException('Invalid header line for Sender string');
        }

        // Check for address, and set if found
        if (preg_match('^(?<name>.*?)<(?<email>[^>]+)>$', $value, $matches)) {
            $header->setAddress($matches['email'], $matches['name']);
        }
        
        return $header;
    }

    public function getFieldName()
    {
        return 'Sender';
    }

    public function getFieldValue()
    {
        if (!$this->address instanceof AddressDescription) {
            return '';
        }

        $email = sprintf('<%s>', $this->address->getEmail());
        $name  = $this->address->getName();
        if (!empty($name)) {
            $email = sprintf('%s %s', $name, $email);
        }
        return $email;
    }

    public function toString()
    {
        return 'Sender: ' . $this->getFieldValue();
    }
    
    /**
     * Set the address used in this header
     * 
     * @param  string|AddressDescription $emailOrAddress 
     * @param  null|string $name 
     * @return Sender
     */
    public function setAddress($emailOrAddress, $name = null)
    {
        if (is_string($emailOrAddress)) {
            $emailOrAddress = new Address($emailOrAddress, $name);
        }
        if (!$emailOrAddress instanceof AddressDescription) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string or AddressDescription object; received "%s"',
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
     * @return AddressDescription|null
     */
    public function getAddress()
    {
        return $this->address;
    }
}
