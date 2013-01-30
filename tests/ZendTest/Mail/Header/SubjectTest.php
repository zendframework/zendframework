<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace ZendTest\Mail\Header;

use Zend\Mail\Header;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
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
}
