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

    public function testAllowsEmptyValueWhenParsing()
    {
        $headerString = 'Subject:';
        $subject      = Header\Subject::fromString($headerString);
        $this->assertEquals('', $subject->getFieldValue());
    }

    public function headerLines()
    {
        return array(
            'newline'      => array("Subject: xxx yyy\n"),
            'cr-lf'        => array("Subject: xxx yyy\r\n"),
            'cr-lf-wsp'    => array("Subject: xxx yyy\r\n\r\n"),
            'multiline'    => array("Subject: xxx\r\ny\r\nyy"),
        );
    }

    /**
     * @dataProvider headerLines
     * @group ZF2015-04
     */
    public function testFromStringRaisesExceptionOnCrlfInjectionDetection($header)
    {
        $this->setExpectedException('Zend\Mail\Header\Exception\InvalidArgumentException');
        $subject = Header\Subject::fromString($header);
    }

    public function invalidSubjects()
    {
        return array(
            'newline'      => array("xxx yyy\n"),
            'cr-lf'        => array("xxx yyy\r\n"),
            'cr-lf-wsp'    => array("xxx yyy\r\n\r\n"),
            'multiline'    => array("xxx\r\ny\r\nyy"),
        );
    }

    /**
     * @dataProvider invalidSubjects
     * @group ZF2015-04
     */
    public function testSettingSubjectRaisesExceptionOnCrlfInjection($value)
    {
        $header = new Header\Subject();
        $this->setExpectedException('Zend\Mail\Header\Exception\InvalidArgumentException');
        $header->setSubject($value);
    }
}
