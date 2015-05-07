<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mail\Header;

use Zend\Mail\Header;

/**
 * @group      Zend_Mail
 */
class ReceivedTest extends \PHPUnit_Framework_TestCase
{
    public function testFromStringCreatesValidReceivedHeader()
    {
        $receivedHeader = Header\Received::fromString('Received: xxx');
        $this->assertInstanceOf('Zend\Mail\Header\HeaderInterface', $receivedHeader);
        $this->assertInstanceOf('Zend\Mail\Header\Received', $receivedHeader);
    }

    public function testGetFieldNameReturnsHeaderName()
    {
        $receivedHeader = new Header\Received();
        $this->assertEquals('Received', $receivedHeader->getFieldName());
    }

    public function testReceivedGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Received needs to be completed');

        $receivedHeader = new Header\Received();
        $this->assertEquals('xxx', $receivedHeader->getFieldValue());
    }

    public function testReceivedToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Received needs to be completed');

        $receivedHeader = new Header\Received();

        // @todo set some values, then test output
        $this->assertEmpty('Received: xxx', $receivedHeader->toString());
    }

    /** Implementation specific tests here */

    public function headerLines()
    {
        return array(
            'newline'      => array("Received: xx\nx"),
            'cr-lf'        => array("Received: xxx\r\n"),
            'cr-lf-fold'   => array("Received: xxx\r\n\r\n zzz"),
            'cr-lf-x2'     => array("Received: xx\r\n\r\nx"),
            'multiline'    => array("Received: x\r\nx\r\nx"),
        );
    }

    /**
     * @dataProvider headerLines
     * @group ZF2015-04
     */
    public function testRaisesExceptionViaFromStringOnDetectionOfCrlfInjection($header)
    {
        $this->setExpectedException('Zend\Mail\Header\Exception\InvalidArgumentException');
        $received = Header\Received::fromString($header);
    }

    public function invalidValues()
    {
        return array(
            'newline'      => array("xx\nx"),
            'cr-lf'        => array("xxx\r\n"),
            'cr-lf-wsp'    => array("xx\r\n\r\nx"),
            'multiline'    => array("x\r\nx\r\nx"),
        );
    }

    /**
     * @dataProvider invalidValues
     * @group ZF2015-04
     */
    public function testConstructorRaisesExceptionOnValueWithCRLFInjectionAttempt($value)
    {
        $this->setExpectedException('Zend\Mail\Header\Exception\InvalidArgumentException');
        new Header\Received($value);
    }
}
