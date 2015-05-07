<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
        $sender = Header\Sender::fromString('Sender: xxx');
        $this->assertInstanceOf('Zend\Mail\Header\HeaderInterface', $sender);
        $this->assertInstanceOf('Zend\Mail\Header\Sender', $sender);
    }

    public function testGetFieldNameReturnsHeaderName()
    {
        $sender = new Header\Sender();
        $this->assertEquals('Sender', $sender->getFieldName());
    }

    public function testReceivedGetFieldValueReturnsProperValue()
    {
        $sender = new Header\Sender();
        $sender->setAddress('foo@bar.com');
        $this->assertEquals('<foo@bar.com>', $sender->getFieldValue());
    }

    public function testReceivedToStringReturnsHeaderFormattedString()
    {
        $sender = new Header\Sender();
        $sender->setAddress('foo@bar.com');

        $this->assertEquals('Sender: <foo@bar.com>', $sender->toString());
    }

    /** Implementation specific tests here */

    public function headerLines()
    {
        return array(
            'newline'      => array("Sender: <foo@bar.com>\n"),
            'cr-lf'        => array("Sender: <foo@bar.com>\r\n"),
            'cr-lf-wsp'    => array("Sender: <foo@bar.com>\r\n\r\n"),
            'multiline'    => array("Sender: <foo\r\n@\r\nbar.com>"),
        );
    }

    /**
     * @dataProvider headerLines
     * @group ZF2015-04
     */
    public function testFromStringRaisesExceptionOnCrlfInjectionDetection($header)
    {
        $this->setExpectedException('Zend\Mail\Header\Exception\InvalidArgumentException');
        Header\Sender::fromString($header);
    }

    /**
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaAddress()
    {
        $address = new Address("foo\r@\r\nexample\n.com", "This\ris\r\na\nCRLF Attack");
        $header  = new Header\Sender();
        $header->setAddress($address);

        $this->setExpectedException('Zend\Mail\Header\Exception\RuntimeException');
        $headerLine = $header->toString();
    }
}
