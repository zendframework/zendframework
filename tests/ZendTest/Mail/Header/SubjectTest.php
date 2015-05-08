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
class SubjectTest extends \PHPUnit_Framework_TestCase
{
    public function testHeaderFolding()
    {
        $string  = str_repeat('foobarblahblahblah baz bat', 10);
        $subject = new Header\Subject();
        $subject->setSubject($string);

        $expected = wordwrap($string, 78, "\r\n ");
        $test     = $subject->getFieldValue(Header\HeaderInterface::FORMAT_ENCODED);
        $this->assertEquals($expected, $test);
    }

    /**
     * @dataProvider validSubjectValuesProvider
     * @group ZF2015-04
     * @param string $decodedValue
     * @param string $encodedValue
     * @param string $encoding
     */
    public function testParseValidSubjectHeader($decodedValue, $encodedValue, $encoding)
    {
        $header = Header\Subject::fromString('Subject:' . $encodedValue);

        $this->assertEquals($decodedValue, $header->getFieldValue());
        $this->assertEquals($encoding, $header->getEncoding());
    }

    /**
     * @dataProvider invalidSubjectValuesProvider
     * @group ZF2015-04
     * @param string $decodedValue
     * @param string $expectedException
     * @param string|null $expectedExceptionMessage
     */
    public function testParseInvalidSubjectHeaderThrowException(
        $decodedValue,
        $expectedException,
        $expectedExceptionMessage
    ) {
        $this->setExpectedException($expectedException, $expectedExceptionMessage);
        Header\Subject::fromString('Subject:' . $decodedValue);
    }

    /**
     * @dataProvider validSubjectValuesProvider
     * @group ZF2015-04
     * @param string $decodedValue
     * @param string $encodedValue
     * @param string $encoding
     */
    public function testSetSubjectValidValue($decodedValue, $encodedValue, $encoding)
    {
        $header = new Header\Subject();
        $header->setSubject($decodedValue);

        $this->assertEquals($decodedValue, $header->getFieldValue());
        $this->assertEquals('Subject: ' . $encodedValue, $header->toString());
        $this->assertEquals($encoding, $header->getEncoding());
    }

    public function validSubjectValuesProvider()
    {
        return array(
            // Description => [decoded format, encoded format, encoding],
            'Empty' => array('', '', 'ASCII'),

            // Encoding cases
            'ASCII charset' => array('azAZ09-_', 'azAZ09-_', 'ASCII'),
            'UTF-8 charset' => array('ázÁZ09-_', '=?UTF-8?Q?=C3=A1z=C3=81Z09-=5F?=', 'UTF-8'),

            // CRLF @group ZF2015-04 cases
            'newline' => array("xxx yyy\n", '=?UTF-8?Q?xxx=20yyy=0A?=', 'UTF-8'),
            'cr-lf' => array("xxx yyy\r\n", '=?UTF-8?Q?xxx=20yyy=0D=0A?=', 'UTF-8'),
            'cr-lf-wsp' => array("xxx yyy\r\n\r\n", '=?UTF-8?Q?xxx=20yyy=0D=0A=0D=0A?=', 'UTF-8'),
            'multiline' => array("xxx\r\ny\r\nyy", '=?UTF-8?Q?xxx=0D=0Ay=0D=0Ayy?=', 'UTF-8'),
        );
    }

    public function invalidSubjectValuesProvider()
    {
        $invalidArgumentException = 'Zend\Mail\Header\Exception\InvalidArgumentException';
        $invalidHeaderValueDetected = 'Invalid header value detected';

        return array(
            // Description => [decoded format, exception class, exception message],
            'newline' => array("xxx yyy\n", $invalidArgumentException, $invalidHeaderValueDetected),
            'cr-lf' => array("xxx yyy\r\n", $invalidArgumentException, $invalidHeaderValueDetected),
            'cr-lf-wsp' => array("xxx yyy\r\n\r\n", $invalidArgumentException, $invalidHeaderValueDetected),
            'multiline' => array("xxx\r\ny\r\nyy", $invalidArgumentException, $invalidHeaderValueDetected),
        );
    }
}
