<?php

namespace ZendTest\Mail;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Mail\Address;

class AddressTest extends TestCase
{
    public function testDoesNotRequireNameForInstantiation()
    {
        $address = new Address('zf-devteam@zend.com');
        $this->assertEquals('zf-devteam@zend.com', $address->getEmail());
        $this->assertNull($address->getName());
    }

    public function testAcceptsNameViaConstructor()
    {
        $address = new Address('zf-devteam@zend.com', 'ZF DevTeam');
        $this->assertEquals('zf-devteam@zend.com', $address->getEmail());
        $this->assertEquals('ZF DevTeam', $address->getName());
    }

    public function testToStringCreatesStringRepresentation()
    {
        $address = new Address('zf-devteam@zend.com', 'ZF DevTeam');
        $this->assertEquals('ZF DevTeam <zf-devteam@zend.com>', $address->toString());
    }
}
