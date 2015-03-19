<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mail\Header;

use Zend\Mail\Header\ContentTransferEncoding;

/**
 * @group      Zend_Mail
 */
class ContentTransferEncodingTest extends \PHPUnit_Framework_TestCase
{
    public function dataValidEncodings()
    {
        return array(
            array('7bit'),
            array('8bit'),
            array('binary'),
            array('quoted-printable'),
        );
    }

    public function dataInvalidEncodings()
    {
        return array(
            array('9bit'),
            array('x-something'),
        );
    }

    /**
     * @dataProvider dataValidEncodings
     */
    public function testContentTransferEncodingFromStringCreatesValidContentTransferEncodingHeader($encoding)
    {
        $contentTransferEncodingHeader = ContentTransferEncoding::fromString('Content-Transfer-Encoding: '.$encoding);
        $this->assertInstanceOf('Zend\Mail\Header\HeaderInterface', $contentTransferEncodingHeader);
        $this->assertInstanceOf('Zend\Mail\Header\ContentTransferEncoding', $contentTransferEncodingHeader);
    }

    /**
     * @dataProvider dataInvalidEncodings
     */
    public function testContentTransferEncodingFromStringCreateExcaption($encoding)
    {
        $this->setExpectedException('Zend\Mail\Header\Exception\InvalidArgumentException');
        $contentTransferEncodingHeader = ContentTransferEncoding::fromString('Content-Transfer-Encoding: '.$encoding);
    }

    public function testContentTransferEncodingGetFieldNameReturnsHeaderName()
    {
        $contentTransferEncodingHeader = new ContentTransferEncoding();
        $this->assertEquals('Content-Transfer-Encoding', $contentTransferEncodingHeader->getFieldName());
    }

    /**
     * @dataProvider dataValidEncodings
     */
    public function testContentTransferEncodingGetFieldValueReturnsProperValue($encoding)
    {
        $contentTransferEncodingHeader = new ContentTransferEncoding();
        $contentTransferEncodingHeader->setTransferEncoding($encoding);
        $this->assertEquals($encoding, $contentTransferEncodingHeader->getFieldValue());
    }

    /**
     * @dataProvider dataValidEncodings
     */
    public function testContentTransferEncodingHandlesCaseInsensitivity($encoding)
    {
        $header = new ContentTransferEncoding();
        $header->setTransferEncoding(strtoupper(substr($encoding, 0, 4)).substr($encoding, 4));
        $this->assertEquals(strtolower($encoding), strtolower($header->getFieldValue()));
    }

    /**
     * @dataProvider dataValidEncodings
     */
    public function testContentTransferEncodingToStringReturnsHeaderFormattedString($encoding)
    {
        $contentTransferEncodingHeader = new ContentTransferEncoding();
        $contentTransferEncodingHeader->setTransferEncoding($encoding);
        $this->assertEquals("Content-Transfer-Encoding: ".$encoding, $contentTransferEncodingHeader->toString());
    }

    public function testProvidingParametersIntroducesHeaderFolding()
    {
        $header = new ContentTransferEncoding();
        $header->setTransferEncoding('quoted-printable');
        $string = $header->toString();

        $this->assertContains("Content-Transfer-Encoding: quoted-printable", $string);
    }
}
