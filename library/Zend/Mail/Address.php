<?php

namespace Zend\Mail;

class Address implements AddressDescription
{
    protected $email;
    protected $name;

    public function __construct($email, $name = null)
    {
        if (!is_string($email)) {
            throw new Exception\InvalidArgumentException('Email must be a string');
        }
        if (null !== $name && !is_string($name)) {
            throw new Exception\InvalidArgumentException('Name must be a string');
        }

        $this->email = $email;
        $this->name  = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getName()
    {
        return $this->name;
    }

    public function toString()
    {
        $string = '<' . $this->getEmail() . '>';
        $name   = $this->getName();
        if (null === $name) {
            return $string;
        }

        $string = $name . ' ' . $string;
        return $string;
    }
}
