<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mail;

use Zend\Mail\Address;

class AddressTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @dataProvider invalidSenderDataProvider
     * @group ZF2015-04
     * @param string $email
     * @param null|string $name
     */
    public function testSetAddressInvalidAddressObject($email, $name)
    {
        $this->setExpectedException('Zend\Mail\Exception\InvalidArgumentException');
        new Address($email, $name);
    }

    public function invalidSenderDataProvider()
    {
        return array(
            // Description => [sender address, sender name],
            'Empty' => array('', null),
            'any ASCII' => array('azAZ09-_', null),
            'any UTF-8' => array('ázÁZ09-_', null),

            // CRLF @group ZF2015-04 cases
            array("foo@bar\n", null),
            array("foo@bar\r", null),
            array("foo@bar\r\n", null),
            array("foo@bar", "\r"),
            array("foo@bar", "\n"),
            array("foo@bar", "\r\n"),
            array("foo@bar", "foo\r\nevilBody"),
            array("foo@bar", "\r\nevilBody"),
        );
    }

    /**
     * @dataProvider validSenderDataProvider
     * @param string $email
     * @param null|string $name
     */
    public function testSetAddressValidAddressObject($email, $name)
    {
        $address = new Address($email, $name);
        $this->assertInstanceOf('\Zend\Mail\Address', $address);
    }

    public function validSenderDataProvider()
    {
        return array(
            // Description => [sender address, sender name],
            'german IDN' => array('öäü@ä-umlaut.de', null),
        );
    }
}
