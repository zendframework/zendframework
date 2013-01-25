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

use Zend\Mail\Header\HeaderWrap;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @group      Zend_Mail
 */
class HeaderWrapTest extends \PHPUnit_Framework_TestCase
{
    public function testWrapUnstructuredHeaderAscii()
    {
        $string = str_repeat('foobarblahblahblah baz bat', 4);
        $header = $this->getMock('Zend\Mail\Header\UnstructuredInterface');
        $header->expects($this->any())
            ->method('getEncoding')
            ->will($this->returnValue('ASCII'));
        $expected = wordwrap($string, 78, "\r\n ");

        $test = HeaderWrap::wrap($string, $header);
        $this->assertEquals($expected, $test);
    }

    /**
     * @group ZF2-258
     */
    public function testWrapUnstructuredHeaderMime()
    {
        $string = str_repeat('foobarblahblahblah baz bat', 3);
        $header = $this->getMock('Zend\Mail\Header\UnstructuredInterface');
        $header->expects($this->any())
            ->method('getEncoding')
            ->will($this->returnValue('UTF-8'));
        $expected = "=?UTF-8?Q?foobarblahblahblah=20baz=20batfoobarblahblahblah=20baz=20?=\r\n"
                    . " =?UTF-8?Q?batfoobarblahblahblah=20baz=20bat?=";

        $test = HeaderWrap::wrap($string, $header);
        $this->assertEquals($expected, $test);
        $this->assertEquals($string, iconv_mime_decode($test, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8'));
    }

    /**
     * @group ZF2-359
     */
    public function testMimeEncoding()
    {
        $string   = 'Umlauts: ä';
        $expected = '=?UTF-8?Q?Umlauts:=20=C3=A4?=';

        $test = HeaderWrap::mimeEncodeValue($string, 'UTF-8', 78);
        $this->assertEquals($expected, $test);
        $this->assertEquals($string, iconv_mime_decode($test, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8'));
    }
}
