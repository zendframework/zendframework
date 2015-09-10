<?php
/**
 * Zend Framework (http://framework.zend/)
 *
 * @link      http://github/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend)
 * @license   http://framework.zend/license/new-bsd New BSD License
 */

namespace ZendTest\Mail\Header;

use Zend\Mail\Address;
use Zend\Mail\Header;

/**
 * @group      Zend_Mail
 */
class SenderTest extends \PHPUnit_Framework_TestCase
{
    public function testFromStringCreatesValidReceivedHeader()
    {
        $sender = Header\Sender::fromString('Sender: <foo@bar>');
        $this->assertInstanceOf('Zend\Mail\Header\HeaderInterface', $sender);
        $this->assertInstanceOf('Zend\Mail\Header\Sender', $sender);
    }

    public function testGetFieldNameReturnsHeaderName()
    {
        $sender = new Header\Sender();
        $this->assertEquals('Sender', $sender->getFieldName());
    }

    /**
     * @dataProvider validSenderHeaderDataProvider
     * @group ZF2015-04
     * @param string $email
     * @param null|string $name
     * @param string $expectedFieldValue,
     * @param string $encodedValue
     * @param string $encoding
     */
    public function testParseValidSenderHeader($expectedFieldValue, $encodedValue, $encoding)
    {
        $header = Header\Sender::fromString('Sender:' . $encodedValue);

        $this->assertEquals($expectedFieldValue, $header->getFieldValue());
        $this->assertEquals($encoding, $header->getEncoding());
    }

    /**
     * @dataProvider invalidSenderEncodedDataProvider
     * @group ZF2015-04
     * @param string $decodedValue
     * @param string $expectedException
     * @param string|null $expectedExceptionMessage
     */
    public function testParseInvalidSenderHeaderThrowException(
        $decodedValue,
        $expectedException,
        $expectedExceptionMessage
    ) {
        $this->setExpectedException($expectedException, $expectedExceptionMessage);
        Header\Sender::fromString('Sender:' . $decodedValue);
    }

    /**
     * @dataProvider validSenderDataProvider
     * @group ZF2015-04
     * @param string $email
     * @param null|string $name
     * @param string $encodedValue
     * @param string $expectedFieldValue,
     * @param string $encoding
     */
    public function testSetAddressValidValue($email, $name, $expectedFieldValue, $encodedValue, $encoding)
    {
        $header = new Header\Sender();
        $header->setAddress($email, $name);

        $this->assertEquals($expectedFieldValue, $header->getFieldValue());
        $this->assertEquals('Sender: ' . $encodedValue, $header->toString());
        $this->assertEquals($encoding, $header->getEncoding());
    }

    /**
     * @dataProvider invalidSenderDataProvider
     * @group ZF2015-04
     * @param string $email
     * @param null|string $name
     */
    public function testSetAddressInvalidValue($email, $name)
    {
        $header = new Header\Sender();
        $this->setExpectedException('Zend\Mail\Exception\InvalidArgumentException');
        $header->setAddress($email, $name);
    }

    /**
     * @dataProvider validSenderDataProvider
     * @group ZF2015-04
     * @param string $email
     * @param null|string $name
     * @param string $expectedFieldValue,
     * @param string $encodedValue
     * @param string $encoding
     */
    public function testSetAddressValidAddressObject($email, $name, $expectedFieldValue, $encodedValue, $encoding)
    {
        $address = new Address($email, $name);

        $header = new Header\Sender();
        $header->setAddress($address);

        $this->assertSame($address, $header->getAddress());
        $this->assertEquals($expectedFieldValue, $header->getFieldValue());
        $this->assertEquals('Sender: ' . $encodedValue, $header->toString());
        $this->assertEquals($encoding, $header->getEncoding());
    }

    public function validSenderDataProvider()
    {
        return array(
            // Description => [sender address, sender name, getFieldValue, encoded version, encoding],
            'ASCII address' => array(
                'foo@bar',
                null,
                '<foo@bar>',
                '<foo@bar>',
                'ASCII'
            ),
            'ASCII name' => array(
                'foo@bar',
                'foo',
                'foo <foo@bar>',
                'foo <foo@bar>',
                'ASCII'
            ),
            'UTF-8 name' => array(
                'foo@bar',
                'ázÁZ09',
                'ázÁZ09 <foo@bar>',
                '=?UTF-8?Q?=C3=A1z=C3=81Z09?= <foo@bar>',
                'UTF-8'
            ),
        );
    }

    public function validSenderHeaderDataProvider()
    {
        return array_merge(array_map(function ($parameters) {
            return array_slice($parameters, 2);
        }, $this->validSenderDataProvider()), array(
            // Per RFC 2822, 3.4 and 3.6.2, "Sender: foo@bar" is valid.
            'Unbracketed email' => array(
                '<foo@bar>',
                'foo@bar',
                'ASCII'
            )
        ));
    }

    public function invalidSenderDataProvider()
    {
        $mailInvalidArgumentException = 'Zend\Mail\Exception\InvalidArgumentException';

        return array(
            // Description => [sender address, sender name, exception class, exception message],
            'Empty' => array('', null, $mailInvalidArgumentException, null),
            'any ASCII' => array('azAZ09-_', null, $mailInvalidArgumentException, null),
            'any UTF-8' => array('ázÁZ09-_', null, $mailInvalidArgumentException, null),

            // CRLF @group ZF2015-04 cases
            array("foo@bar\n", null, $mailInvalidArgumentException, null),
            array("foo@bar\r", null, $mailInvalidArgumentException, null),
            array("foo@bar\r\n", null, $mailInvalidArgumentException, null),
            array("foo@bar", "\r", $mailInvalidArgumentException, null),
            array("foo@bar", "\n", $mailInvalidArgumentException, null),
            array("foo@bar", "\r\n", $mailInvalidArgumentException, null),
            array("foo@bar", "foo\r\nevilBody", $mailInvalidArgumentException, null),
            array("foo@bar", "\r\nevilBody", $mailInvalidArgumentException, null),
        );
    }

    public function invalidSenderEncodedDataProvider()
    {
        $mailInvalidArgumentException = 'Zend\Mail\Exception\InvalidArgumentException';
        $headerInvalidArgumentException = 'Zend\Mail\Header\Exception\InvalidArgumentException';

        return array(
            // Description => [decoded format, exception class, exception message],
            'Empty' => array('', $mailInvalidArgumentException, null),
            'any ASCII' => array('azAZ09-_', $mailInvalidArgumentException, null),
            'any UTF-8' => array('ázÁZ09-_', $mailInvalidArgumentException, null),
            array("xxx yyy\n", $mailInvalidArgumentException, null),
            array("xxx yyy\r\n", $mailInvalidArgumentException, null),
            array("xxx yyy\r\n\r\n", $mailInvalidArgumentException, null),
            array("xxx\r\ny\r\nyy", $mailInvalidArgumentException, null),
            array("foo\r\n@\r\nbar", $mailInvalidArgumentException, null),

            array("ázÁZ09 <foo@bar>", $headerInvalidArgumentException, null),
            'newline' => array("<foo@bar>\n", $headerInvalidArgumentException, null),
            'cr-lf' => array("<foo@bar>\r\n", $headerInvalidArgumentException, null),
            'cr-lf-wsp' => array("<foo@bar>\r\n\r\n", $headerInvalidArgumentException, null),
            'multiline' => array("<foo\r\n@\r\nbar>", $headerInvalidArgumentException, null),
        );
    }
}
